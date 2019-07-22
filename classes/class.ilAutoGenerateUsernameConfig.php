<?php
 
/**
 * Auto Generate Username configuration class
 *
 * @author Fabian Wolf <wolf@leifos.com>
 *
 */
class ilAutoGenerateUsernameConfig
{
	/**
	 * @var ilSetting
	 */
	protected $setting;
	/**
	 * @var string[]
	 */
	protected $allowed_contexts = array();

	/**
	 * @var string
	 */
	protected $login_template = "[login]";

	/**
	 * @var int
	 */
	protected $id_sequenz = 1;

	/**
	 * @var bool
	 */
	protected $use_camelCase = false;

	/**
	 * @var bool
	 */
	protected $string_to_lower = true;

	/**
	 * @var bool
	 */
	protected $active_update = false;

	/**
	 * @var string
	 */
	protected $auth_mode_update;

	/**
	 * ilAutoGenerateUsernameConfig constructor.
	 */
	public function __construct()
	{
		$this->setting = new \ilSetting('xagu');
		$this->read();
	}

	/**
	 * Read settings
	 */
	public function read()
	{
		$this->setting->read();
		$this->setAllowedContexts(explode(';',$this->setting->get("xagu_contexts", implode(';',$this->getAllowedContexts()))));
		$this->setIdSequenz($this->setting->get("xagu_id", $this->getIdSequenz()));
		$this->setLoginTemplate($this->setting->get("xagu_template", $this->getLoginTemplate()));
		$this->setUseCamelCase((bool)$this->setting->get("xagu_use_camel_case", $this->getUseCamelCase()));
		$this->setStringToLower((bool)$this->setting->get("xagu_string_to_lower", $this->getStringToLower()));
		$this->setActiveUpdateExistingUsers((bool)$this->setting->get("xagu_active_update", $this->getActiveUpdateExistingUsers()));
		$this->setAuthModeUpdate($this->setting->get('xagu_auth_mode', $this->getAuthModeUpdate()));
	}

	public function update()
	{
		$this->setting->set("xagu_contexts", implode(';',$this->getAllowedContexts()));
		$this->setting->set("xagu_id", $this->getIdSequenz());
		$this->setting->set("xagu_template", $this->getLoginTemplate());
		$this->setting->set("xagu_use_camel_case", (int) $this->getUseCamelCase());
		$this->setting->set("xagu_string_to_lower", (int) $this->getStringToLower());
		$this->setting->set("xagu_active_update", (int) $this->getActiveUpdateExistingUsers());
		$this->setting->set("xagu_auth_mode", $this->getAuthModeUpdate());
	}

	/**
	 * @param array $allowed_contexts
	 */
	public function setAllowedContexts($allowed_contexts)
	{
		$this->allowed_contexts = $allowed_contexts;
	}

	/**
	 * @return array
	 */
	public function getAllowedContexts()
	{
		return $this->allowed_contexts;
	}

	/**
	 * @param int $id_sequenz
	 */
	public function setIdSequenz($id_sequenz)
	{
		$this->id_sequenz = $id_sequenz;
	}

	/**
	 * @return int
	 */
	public function getIdSequenz()
	{
		return $this->id_sequenz;
	}

	/**
	 * @param string $login_template
	 */
	public function setLoginTemplate($login_template)
	{
		$this->login_template = $login_template;
	}

	/**
	 * @return string
	 */
	public function getLoginTemplate()
	{
		return $this->login_template;
	}

	/**
	 * @param bool $string_to_lower
	 */
	public function setStringToLower($string_to_lower)
	{
		$this->string_to_lower = $string_to_lower;
	}

	/**
	 * @return bool
	 */
	public function getStringToLower()
	{
		return $this->string_to_lower;
	}

	/**
	 * @param bool $use_camelCase
	 */
	public function setUseCamelCase($use_camelCase)
	{
		$this->use_camelCase = $use_camelCase;
	}

	/**
	 * @return bool
	 */
	public function getUseCamelCase()
	{
		return $this->use_camelCase;
	}

	/**
	 * @param $active_update
	 */
	public function setActiveUpdateExistingUsers($active_update)
	{
		$this->active_update = $active_update;
	}

	/**
	 * @return bool
	 */
	public function getActiveUpdateExistingUsers()
	{
		return $this->active_update;
	}

	public function setAuthModeUpdate($mode)
	{
		$this->auth_mode_update = $mode;
	}

	/**
	 * @return int
	 */
	public function getAuthModeUpdate()
	{
		return $this->auth_mode_update;
	}


	/**
	 * @return int
	 */
	public function getNextId()
	{
		$this->setIdSequenz($this->getIdSequenz()+1);
		$this->setting->set("xagu_id", $this->getIdSequenz());
		return $this->getIdSequenz();
	}

	/**
	 * @param $a_context
	 * @return bool
	 */
	public function isValidContext($a_context)
	{
		include_once('./Services/User/classes/class.ilUserCreationContext.php');

		if(in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $a_context) && in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $this->getAllowedContexts()))
		{
			return true;
		}

		if(in_array(ilUserCreationContext::CONTEXT_LDAP, $a_context) && in_array(ilUserCreationContext::CONTEXT_LDAP, $this->getAllowedContexts()))
		{
			return true;
		}

		return false;
	}

	public function getStringActiveAuthModes()
	{
		global $DIC;

		$lng = $DIC->language();

		$modes = array();
		foreach (ilAuthUtils::_getActiveAuthModes() as $mode_name => $mode)
		{
			if(ilLDAPServer::isAuthModeLDAP($mode))
			{
				$server = ilLDAPServer::getInstanceByServerId(ilLDAPServer::getServerIdByAuthMode($mode));
				$name = $server->getName();
				$modes[$mode_name] = $name;
			}
			else
			{
				$modes[$mode_name] = $lng->txt("auth_" . $mode_name);
			}
		}
		return $modes;
	}
}