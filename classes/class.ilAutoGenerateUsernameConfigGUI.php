<?php

declare(strict_types=1);

use ILIAS\UI\Component\Input\Container\Form\Standard;
use ILIAS\UI\Implementation\Component\MessageBox\MessageBox;
use ILIAS\UI\Renderer;
use ILIAS\UI\Factory;
use ILIAS\HTTP\GlobalHttpState;
use Leifos\AutoGenerateUsername\I\Factory as lfAGUDFactoryInterface;
use Leifos\AutoGenerateUsername\Factory as lfAGUDFactory;
use Leifos\AutoGenerateUsername\DB\Settings\Settings;

/**
 * @ilCtrl_IsCalledBy ilAutoGenerateUsernameConfigGUI : ilObjComponentSettingsGUI
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

    public function configure(MessageBox $messageBox = null, Standard $form = null): void
    {
        $form = is_null($form) ? $this->initConfigurationForm() : $form;
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
            ->withValue($settings->read(Settings::LOGIN_TEMPLATE));
        $demo = $this->ui->input()->field()->text(
            $this->pl->txt("demo"),
            $this->pl->txt('demo_info')
        )
            ->withDisabled(true)
            ->withValue($this->pl->generateUsername($this->ilUser, true));
        $string_to_lower_choice = $this->ui->input()->field()->checkbox(
            $this->pl->txt("string_to_lower")
        )
            ->withValue($settings->readAsBool(Settings::STRING_TO_LOWER));
        $camelcase_choice = $this->ui->input()->field()->checkbox(
            $this->pl->txt("camel_case")
        )
            ->withValue($settings->readAsBool(Settings::CAMEL_CASE));
        $configuration_section = $this->ui->input()->field()->section(
            [$template, $demo, $string_to_lower_choice, $camelcase_choice],
            $this->pl->txt("configuration")
        );
        //section update existing
        $active_accounts = $this->ui->input()->field()->checkbox(
            $this->pl->txt("active_update")
        )
            ->withValue($settings->readAsBool(Settings::ACTIVE_UPDATE));
        $auth_mode = ($settings->read(Settings::AUTH_MODE_UPDATE) ?? 'default');
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
        $context_sections = [];
        foreach ($this->getContextArray() as $key => $name) {
            $context = $this->ui->input()->field()->checkbox($name)
                ->withValue(in_array($key, $settings->readAsArray(Settings::ALLOWED_CONTEXTS)));
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
        $form = $this->initConfigurationForm()->withRequest($this->http->request());
        /** @var Standard $form */
        if ($request->getMethod() == "POST" && is_null($form->getError())) {
            /** @var \ILIAS\UI\Component\Input\Field\Section $configuration */
            /** @var \ILIAS\UI\Component\Input\Field\Section $update_existing */
            /** @var \ILIAS\UI\Component\Input\Field\Section $context */
            /** @var \ILIAS\UI\Component\Input\Field\Text $template */
            /** @var \ILIAS\UI\Component\Input\Field\Checkbox $lowercase */
            /** @var \ILIAS\UI\Component\Input\Field\Checkbox $camelcase */
            /** @var \ILIAS\UI\Component\Input\Field\Checkbox $active */
            /** @var \ILIAS\UI\Component\Input\Field\Select $auth_mode */
            $result = $form->getInputs();
            $configuration = $result['configuration'];
            $update_existing = $result['update_existing'];
            $context = $result['context'];
            $template = $configuration->getInputs()[0];
            $lowercase = $configuration->getInputs()[2];
            $camelcase = $configuration->getInputs()[3];
            $active = $update_existing->getInputs()[0];
            $auth_mode = $update_existing->getInputs()[1];
            $settings->set(Settings::STRING_TO_LOWER, (string)((bool) $lowercase->getValue()));
            $settings->set(Settings::CAMEL_CASE, (string)((bool) $camelcase->getValue()));
            $settings->set(Settings::ACTIVE_UPDATE, (string)((bool) $active->getValue()));
            $settings->set(Settings::AUTH_MODE_UPDATE, $auth_mode->getValue() ?? '');
            $contexts = [];
            foreach ($this->getContextArray() as $key => $value) {
                /** @var \ILIAS\UI\Component\Input\Field\Checkbox $current_context */
                $current_context = $context->getInputs()[$key];
                if ($current_context->getValue()) {
                    $contexts[] = $key;
                }
            }
            $settings->set(Settings::ALLOWED_CONTEXTS, implode(';', $contexts));
            $template = $this->agu_factory->pattern()->handler()
                ->withPattern($template->getValue())
                ->cleanPattern();
            $settings->set(Settings::LOGIN_TEMPLATE, $template);
            $this->configure($this->ui->messageBox()->success(
                $this->lng->txt("saved_successfully")),
                $form
            );
        } else {
            $this->configure(
                null,
                $form
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
        $placeholder = [];
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
        return [
            ilUserCreationContext::CONTEXT_REGISTRATION => $this->pl->txt("context_registration"),
            ilUserCreationContext::CONTEXT_LDAP => $this->pl->txt("context_ldap")
        ];
    }
}
