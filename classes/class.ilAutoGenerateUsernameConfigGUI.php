<?php

/**
 * Auto generate username configuration GUI class
 *
 * @author Marvin Barz <barz@leifos.com>
 * @version $Id$
 *
 */
class ilAutoGenerateUsernameConfigGUI extends ilPluginConfigGUI
{
	/**
	 * @var ilAutoGenerateUsernameConfig
	 */
	protected $config;

	/**
	* Handles all commmands, default is "configure"
	*/
	function performCommand($cmd)
	{

		switch ($cmd)
		{
			case "configure":
			case "save":
				$this->$cmd();
				break;
		}
	}

	/**
	 * Configure screen
	 */
	function configure()
	{
		global $DIC;

		$tpl = $DIC->ui()->mainTemplate();
		$form = $this->initConfigurationForm();
		$tpl->setContent($DIC->ui()->renderer()->render($form));
	}
	
	/**
	 * Init configuration form.
	 *
	 * @return \ILIAS\UI\Component\Input\Container\Form\Standard form component
	 */
	public function initConfigurationForm()
	{
		global $DIC;

		$ilUser = $DIC->user();
		$tpl = $DIC->ui()->mainTemplate();

		$this->initConfig();
		$pl = $this->getPluginObject();
		$ui = $DIC->ui()->factory();

		$tpl->addJavaScript($pl->getDirectory() . "/js/ilAutoGenerateUsername.js");

		$placeholders = $this->createPlaceholderHTML();

		//section configuration
		$template = $ui->input()->field()->text($pl->txt("template"), $pl->txt('template_info') . $placeholders)
		                                 ->withRequired(true)
										 ->withValue($this->config->getLoginTemplate());


		$demo = $ui->input()->field()->text($pl->txt("demo"), $pl->txt('demo_info'))
		                             ->withDisabled(true)
									 ->withValue($pl->generateUsername($ilUser, true));

		$string_to_lower_choice = $ui->input()->field()->checkbox($pl->txt("string_to_lower"))->withValue($this->config->getStringToLower());

		$camelcase_choice = $ui->input()->field()->checkbox($pl->txt("camel_case"))->withValue($this->config->getUseCamelCase());

		$configuration_section = $ui->input()->field()->section([$template, $demo, $string_to_lower_choice, $camelcase_choice], $pl->txt("configuration"));

		//section update existing
		$active_accounts = $ui->input()->field()->checkbox($pl->txt("active_update"))->withValue($this->config->getActiveUpdateExistingUsers());

		$auth_mode = (string) ($this->config->getAuthModeUpdate() ?? 'default');
		$authentication_select = $ui->input()->field()->select($pl->txt("select_auth_modes"), $this->config->getStringActiveAuthModes())
									->withRequired(true)
									->withValue($auth_mode);

		$update_existing_section = $ui->input()->field()->section([$active_accounts, $authentication_select], $pl->txt("update_existing"));


		//context section
		$context_sections = array();
		foreach($this->getContextArray() as $key => $name)
		{
			$context = $ui->input()->field()->checkbox($name)->withValue(in_array($key, $this->config->getAllowedContexts()));

			$context_sections[$key] = $context;
		}

		$context_section = $ui->input()->field()->section($context_sections, $pl->txt("context"));

		$form_action = $DIC->ctrl()->getFormActionByClass('ilAutoGenerateUsernameConfigGUI','save');
		$form_elements = array(
			"configuration"   => $configuration_section,
			"update_existing" => $update_existing_section,
			"context"         => $context_section
		);

		return $ui->input()->container()->form()->standard($form_action, $form_elements);
	}
	
	/**
	 * Save form input (currently does not save anything to db)
	 *
	 */
	public function save()
	{
		global $DIC;

		$lng = $DIC->language();
		$ilCtrl = $DIC->ctrl();
		$request = $DIC->http()->request();
		$ilias   = $DIC["ilias"];

		$this->initConfig();
		$pl = $this->getPluginObject();


		if ($request->getMethod() == "POST") {
			$form   = $this->initConfigurationForm()->withRequest($request);
			$result = $form->getData();

			$template_string  = $result['configuration'][0];
			$string_to_lower  = $result['configuration'][2];
			$string_camelcase = $result['configuration'][3];

			$active_update = $result['update_existing'][0];
			$auth_mode     = $result['update_existing'][1];


			$template = $pl->validateString(
				$template_string,
				(bool)$string_to_lower,
				(bool)$string_camelcase,
				true);

			$this->config->setLoginTemplate($template);
			$this->config->setStringToLower((bool)$string_to_lower);
			$this->config->setUseCamelCase((bool)$string_camelcase);
			$this->config->setActiveUpdateExistingUsers((bool)$active_update);
			$this->config->setAuthModeUpdate($auth_mode);

			$contexts = array();

			foreach($this->getContextArray() as $key => $value)
			{
				if($result["context"][$key] === true)
				{
					$contexts[] = $key;
				}
			}

			$this->config->setAllowedContexts($contexts);

			$this->config->update();

			ilUtil::sendSuccess($lng->txt("saved_successfully"), true);
			$ilCtrl->redirect($this, "configure");
		} else {
			$ilias->raiseError(
				$lng->txt("autogenerateusername_form_not_evaluabe"),
				$ilias->error_obj->MESSAGE
			);
			$ilCtrl->redirect($this, "configure");
		}
	}

	/**
	 * init config
	 */
	public function initConfig()
	{
		$this->getPluginObject()->includeClass("class.ilAutoGenerateUsernameConfig.php");

		$this->config = new ilAutoGenerateUsernameConfig();
	}

	/**
	 * Get standard placeholders
	 * @return array
	 */
	public function getStandardPlaceholder()
	{
		global $DIC;

		$lng = $DIC->language();
		$pl = $this->getPluginObject();

		$placeholder = array(
			"login" => $lng->txt('login'),
			"firstname" =>  $lng->txt('firstname'),
			"lastname" =>  $lng->txt('lastname'),
			"email" =>  $lng->txt('email'),
			"matriculation" =>  $lng->txt('matriculation'),
			"number" => $pl->txt('number'),
			"hash" => $pl->txt('hash')
		);

		return $placeholder;
	}

	/**
	 * Get udf placeholders
	 * @return array
	 */
	public function getUDFPlaceholder()
	{
		$placeholder = array();
		/**
		 * @var ilUserDefinedFields $user_defined_fields
		 */
		$user_defined_fields = ilUserDefinedFields::_getInstance();

		foreach($user_defined_fields->getDefinitions() as $field_id => $definition)
		{
			if($definition['field_type'] !=  UDF_TYPE_WYSIWYG)
			{
				$placeholder["udf_".$field_id] = $definition['field_name'];
			}
		}
		return $placeholder;
	}

	/**
	 * Create placeholders HTML
	 * @return string
	 */
	private function createPlaceholderHTML()
	{
		$pl = $this->getPluginObject();

		$placeholders = "<br/><h2>".$pl->txt('placeholder_standard')."</h2>";
		foreach($this->getStandardPlaceholder() as $text => $title)
		{
			$placeholders .= '<b><a href="#" onclick="insertTextIntoTextField(this.innerHTML, \'form_input_2\'); return false;">['.$text.']</a></b>:'.$title.'<br />';
		}

		$udf =$this->getUDFPlaceholder();
		if(count($udf) > 0)
		{
			$placeholders .= "<br/><h2>".$pl->txt('placeholder_udf')."</h2>";
			foreach($this->getUDFPlaceholder() as $text => $title)
			{
				$placeholders .= '<b><a href="#" onclick="insertTextIntoTextField(this.innerHTML, \'form_input_2\'); return false;">['.$text.']</a></b>:'.$title.'<br />';
			}
		}

		$placeholders .= "<br/>";

		return $placeholders;
	}

	/**
	 * Get context array
	 * @return array
	 */
	public function getContextArray()
	{
		$pl = $this->getPluginObject();

		return array(
			ilUserCreationContext::CONTEXT_REGISTRATION => $pl->txt("context_registration"),
			ilUserCreationContext::CONTEXT_LDAP => $pl->txt("context_ldap")
		);
	}
}
?>
