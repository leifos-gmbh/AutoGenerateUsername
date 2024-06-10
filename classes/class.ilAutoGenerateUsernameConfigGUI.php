<?php

declare(strict_types=1);

use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Implementation\Component\MessageBox\MessageBox;
use ILIAS\UI\Renderer;
use ILIAS\UI\Factory;
use ILIAS\HTTP\GlobalHttpState;
use Leifos\AutoGenerateUsername\I\Factory as lfAGUDFactoryInterface;
use Leifos\AutoGenerateUsername\Factory as lfAGUDFactory;

/**
 * Auto generate username configuration GUI class
 *
 * @author Marvin Barz <barz@leifos.com>
 * @version $Id$
 *
 * @ilCtrl_IsCalledBy ilAutoGenerateUsernameConfigGUI : ilObjComponentSettingsGUI
 *
 */
class ilAutoGenerateUsernameConfigGUI extends ilPluginConfigGUI
{
    protected lfAGUDFactoryInterface $agu_factory;
    protected ilAutoGenerateUsernamePlugin $pl;
    protected ilGlobalTemplateInterface $tpl;
    protected ilLanguage $lng;
    protected ilObjUser $ilUser;
    protected Renderer $renderer;
    protected Factory $ui;
    protected ilCtrl $ilCtrl;
    protected GlobalHttpState $http;

    public function __construct()
    {
        global $DIC;
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->lng = $DIC->language();
        $this->ilUser = $DIC->user();
        $this->renderer = $DIC->ui()->renderer();
        $this->ui = $DIC->ui()->factory();
        $this->ilCtrl = $DIC->ctrl();
        $this->http = $DIC->http();
        $this->agu_factory = new lfAGUDFactory($this->lng, $DIC->database());
        $this->pl = new ilAutoGenerateUsernamePlugin($DIC->database(), $DIC['component.repository'], 'xagu');
    }

    public function performCommand($cmd): void
    {
        switch ($cmd) {
            case "configure":
            case "save":
                $this->$cmd();
                break;
        }
    }

    public function configure(MessageBox $messageBox = null): void
    {
        $form = $this->initConfigurationForm();
        $content = is_null($messageBox) ? [ $form ] : [ $messageBox, $form ];
        $this->tpl->setContent($this->renderer->render($content));
    }

    public function initConfigurationForm(): Standard
    {
        $settings = $this->agu_factory->db()->settings()->handler();
        $this->tpl->addJavaScript($this->pl->getDirectory() . "/js/ilAutoGenerateUsername.js");
        $placeholders = $this->createPlaceholderHTML();
        //section configuration
        $template = $this->ui->input()->field()->text(
            $this->pl->txt("template"),
            $this->pl->txt('template_info') . $placeholders
        )
            ->withRequired(true)
            ->withValue($settings->getLoginTemplate());
        $demo = $this->ui->input()->field()->text(
            $this->pl->txt("demo"),
            $this->pl->txt('demo_info')
        )
            ->withDisabled(true)
            ->withValue($this->pl->generateUsername($this->ilUser, true));
        $string_to_lower_choice = $this->ui->input()->field()->checkbox(
            $this->pl->txt("string_to_lower")
        )
            ->withValue($settings->getStringToLower());
        $camelcase_choice = $this->ui->input()->field()->checkbox(
            $this->pl->txt("camel_case")
        )
            ->withValue($settings->getUseCamelCase());
        $configuration_section = $this->ui->input()->field()->section(
            [$template, $demo, $string_to_lower_choice, $camelcase_choice],
            $this->pl->txt("configuration")
        );
        //section update existing
        $active_accounts = $this->ui->input()->field()->checkbox(
            $this->pl->txt("active_update")
        )
            ->withValue($settings->getActiveUpdateExistingUsers());
        $auth_mode = ($settings->getAuthModeUpdate() ?? 'default');
        $authentication_select = $this->ui->input()->field()->select(
            $this->pl->txt("select_auth_modes"),
            $settings->getStringActiveAuthModes()
        )
            ->withRequired(true)
            ->withValue($auth_mode);
        $update_existing_section = $this->ui->input()->field()->section(
            [$active_accounts, $authentication_select],
            $this->pl->txt("update_existing")
        );
        //context section
        $context_sections = array();
        foreach ($this->getContextArray() as $key => $name) {
            $context = $this->ui->input()->field()->checkbox($name)
                ->withValue(in_array($key, $settings->getAllowedContexts()));
            $context_sections[$key] = $context;
        }
        $context_section = $this->ui->input()->field()->section(
            $context_sections,
            $this->pl->txt("context")
        );
        $form_action = $this->ilCtrl->getFormActionByClass('ilAutoGenerateUsernameConfigGUI', 'save');
        $form_elements = [
            "configuration" => $configuration_section,
            "update_existing" => $update_existing_section,
            "context" => $context_section
        ];
        return $this->ui->input()->container()->form()->standard($form_action, $form_elements);
    }

    /**
     * Save form input (currently does not save anything to db)
     */
    public function save(): void
    {
        $settings = $this->agu_factory->db()->settings()->handler();
        $request = $this->http->request();
        if ($request->getMethod() == "POST") {
            $form = $this->initConfigurationForm()->withRequest($request);
            $result = $form->getData();
            $template_string = $result['configuration'][0];
            $string_to_lower = $result['configuration'][2];
            $string_camelcase = $result['configuration'][3];
            $active_update = $result['update_existing'][0];
            $auth_mode = $result['update_existing'][1];
            $settings->setStringToLower((bool) $string_to_lower);
            $settings->setUseCamelCase((bool) $string_camelcase);
            $settings->setActiveUpdateExistingUsers((bool) $active_update);
            $settings->setAuthModeUpdate($auth_mode);
            $contexts = array();
            foreach ($this->getContextArray() as $key => $value) {
                if ($result["context"][$key] === true) {
                    $contexts[] = $key;
                }
            }
            $settings->setAllowedContexts($contexts);
            $template = $this->agu_factory->pattern()->handler()
                ->withPattern($template_string)
                ->cleanPattern();
            $settings->setLoginTemplate($template);
            $this->configure($this->ui->messageBox()->success(
                $this->lng->txt("saved_successfully"))
            );
        } else {
            $this->configure($this->ui->messageBox()->failure(
                $this->lng->txt("autogenerateusername_form_not_evaluabe"))
            );
        }
    }

    public function getStandardPlaceholder(): array
    {
        return [
            "login" => $this->lng->txt('login'),
            "firstname" => $this->lng->txt('firstname'),
            "lastname" => $this->lng->txt('lastname'),
            "email" => $this->lng->txt('email'),
            "matriculation" => $this->lng->txt('matriculation'),
            "number" => $this->pl->txt('number'),
            "hash" => $this->pl->txt('hash')
        ];
    }

    public function getUDFPlaceholder(): array
    {
        $placeholder = array();
        $user_defined_fields = ilUserDefinedFields::_getInstance();
        foreach ($user_defined_fields->getDefinitions() as $field_id => $definition) {
            if ($definition['field_type'] != UDF_TYPE_WYSIWYG) {
                $placeholder["udf_" . $field_id] = $definition['field_name'];
            }
        }
        return $placeholder;
    }

    private function createPlaceholderHTML(): string
    {
        $placeholders = "<br/><h2>" . $this->pl->txt('placeholder_standard') . "</h2>";
        foreach ($this->getStandardPlaceholder() as $text => $title) {
            $placeholders .= '<b><a href="#" onclick="insertTextIntoTextField(this.innerHTML, \'form_input_2\');'
                . ' return false;">[' . $text . ']</a></b>:' . $title . '<br />';
        }
        $udf = $this->getUDFPlaceholder();
        if (count($udf) > 0) {
            $placeholders .= "<br/><h2>" . $this->pl->txt('placeholder_udf') . "</h2>";
            foreach ($this->getUDFPlaceholder() as $text => $title) {
                $placeholders .= '<b><a href="#" onclick="insertTextIntoTextField(this.innerHTML, \'form_input_2\');'
                    . ' return false;">[' . $text . ']</a></b>:' . $title . '<br />';
            }
        }
        $placeholders .= "<br/>";
        return $placeholders;
    }

    public function getContextArray(): array
    {
        return array(
            ilUserCreationContext::CONTEXT_REGISTRATION => $this->pl->txt("context_registration"),
            ilUserCreationContext::CONTEXT_LDAP => $this->pl->txt("context_ldap")
        );
    }
}
