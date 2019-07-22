<?php

include_once("./Services/Component/classes/class.ilPluginConfigGUI.php");
 
/**
 * Auto generate username configuration GUI class
 *
 * @author Fabian Wolf <wolf@leifos.com>
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
		$tpl->setContent($form->getHTML());
	}
	
	/**
	 * Init configuration form.
	 *
	 * @return ilPropertyFormGUI form object
	 */
	public function initConfigurationForm()
	{
		global $DIC;

		$lng = $DIC->language();
		$ilCtrl = $DIC->ctrl();
		$ilUser = $DIC->user();

		$this->initConfig();
		$pl = $this->getPluginObject();
		$form = new ilPropertyFormGUI();
		$form->addCommandButton("save", $lng->txt("save"));

		$template = new ilTextInputGUI($pl->txt("template"), "xagu_template");
		$template->setInfo($pl->txt('template_info'));
		$template->setRequired(true);
		$template->setValue($this->config->getLoginTemplate());
		$form->addItem($template);

		$demo = new ilTextInputGUI($pl->txt("demo"), "xagu_demo");
		$demo->setInfo($pl->txt("demo_info"));
		$demo->setValue($pl->generateUsername($ilUser, true));
		$demo->setDisabled(true);
		$form->addItem($demo);

		$pl->includeClass("class.ilPlaceholdersPropertyGUI.php");
		$placeholder = new ilPlaceholdersPropertyGUI();
		$placeholder->setTitle($pl->txt("placeholder"));
		$placeholder->setTextfieldId("xagu_template");
		$placeholder->setPlaceholderAdvice($pl->txt("placeholder_advice"));

		$placeholder->addSection($pl->txt('placeholder_standard'));
		foreach($this->getStandardPlaceholder() as $text => $title)
		{
			$placeholder->addPlaceholder($title, $text);
		}
		$udf =$this->getUDFPlaceholder();

		if(count($udf) > 0)
		{
			$placeholder->addSection($pl->txt('placeholder_udf'));
			foreach($this->getUDFPlaceholder() as $text => $title)
			{
				$placeholder->addPlaceholder($title, $text);
			}
		}


		$form->addItem($placeholder);

		$string_to_lower = new ilCheckboxInputGUI($pl->txt("string_to_lower"), "xagu_string_to_lower");
		$string_to_lower->setInfo($pl->txt('string_to_lower_info'));
		$string_to_lower->setChecked($this->config->getStringToLower());
		$form->addItem($string_to_lower);

		$camelCase = new ilCheckboxInputGUI($pl->txt("camel_case"), "xagu_camel_case");
		$camelCase->setInfo($pl->txt('camel_case_info'));
		$camelCase->setChecked($this->config->getUseCamelCase());
		$form->addItem($camelCase);

		//new update
		$section_update_existing = new ilFormSectionHeaderGUI();
		$section_update_existing->setTitle($pl->txt("update_exising"));
		$form->addItem($section_update_existing);

		//activate update existing users
		$active_update_existing = new ilCheckboxInputGUI($pl->txt("active_update"), "xagu_active_update");
		$active_update_existing->setChecked($this->config->getActiveUpdateExistingUsers());
		$form->addItem($active_update_existing);

		//select auth active modes
		$select_active_modes = new ilSelectInputGUI($pl->txt("select_auth_modes"), "xagu_auth_mode");
		$select_active_modes->setRequired(TRUE);
		$select_active_modes->setOptions($this->config->getStringActiveAuthModes());
		$select_active_modes->setValue($this->config->getAuthModeUpdate());
		$form->addItem($select_active_modes);


		$sec = new ilFormSectionHeaderGUI();
		$sec->setTitle($pl->txt("context"));
		$form->addItem($sec);

		foreach($this->getContextArray() as $key => $name)
		{
			$context = new ilCheckboxInputGUI($name, 'xagu_'.$key);
			$context->setChecked(in_array($key, $this->config->getAllowedContexts()));
			$form->addItem($context);
		}

		$form->setTitle($pl->txt("configuration"));
		$form->setFormAction($ilCtrl->getFormAction($this));

		return $form;
	}
	
	/**
	 * Save form input (currently does not save anything to db)
	 *
	 */
	public function save()
	{
		global $DIC;

		$tpl = $DIC->ui()->mainTemplate();
		$lng = $DIC->language();
		$ilCtrl = $DIC->ctrl();

		$this->initConfig();
		$pl = $this->getPluginObject();
		
		$form = $this->initConfigurationForm();
		if ($form->checkInput())
		{
			$template = $pl->validateString(
				$_POST['xagu_template'],
				(bool)$_POST["xagu_string_to_lower"],
				(bool)$_POST["xagu_camel_case"],
				true);

			$this->config->setLoginTemplate($template);
			$this->config->setStringToLower((bool)$_POST["xagu_string_to_lower"]);
			$this->config->setUseCamelCase((bool)$_POST["xagu_camel_case"]);
			$this->config->setActiveUpdateExistingUsers((bool)$_POST["xagu_active_update"]);
			$this->config->setAuthModeUpdate($_POST["xagu_auth_mode"]);

			$contexts = array();

			foreach($this->getContextArray() as $key => $value)
			{
				if($_POST["xagu_".$key])
				{
					$contexts[] = $key;
				}
			}

			$this->config->setAllowedContexts($contexts);

			$this->config->update();

			ilUtil::sendSuccess($lng->txt("saved_successfully"), true);
			$ilCtrl->redirect($this, "configure");
		}
		else
		{
			$form->setValuesByPost();
			$tpl->setContent($form->getHtml());
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
		include_once './Services/User/classes/class.ilUserDefinedFields.php';
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
