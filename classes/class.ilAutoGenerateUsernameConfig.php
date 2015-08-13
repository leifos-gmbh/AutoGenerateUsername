<?php
 
/**
 * Auto Generate Username configuration class
 *
 * @author Fabian Wolf <wolf@leifos.com>
 * @version $Id$
 *
 */
class ilAutoGenerateUsernameConfig
{
	/**
	 * @var ilSetting
	 */
	protected $setting;
	/**
	 * @var string
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

	public function __construct()
	{
		$this->setting = new ilSetting("xagu");

		$this->read();
	}

	public function read()
	{
		$this->setting->read();
		$this->setAllowedContexts(explode(';',$this->setting->get("xagu_contexts", implode(';',$this->getAllowedContexts()))));
		$this->setIdSequenz($this->setting->get("xagu_id", $this->getIdSequenz()));
		$this->setLoginTemplate($this->setting->get("xagu_template", $this->getLoginTemplate()));
		$this->setUseCamelCase((bool)$this->setting->get("xagu_use_camel_case", $this->getUseCamelCase()));
		$this->setStringToLower((bool)$this->setting->get("xagu_string_to_lower", $this->getStringToLower()));
	}

	public function update()
	{
		$this->setting->set("xagu_contexts", implode(';',$this->getAllowedContexts()));
		$this->setting->set("xagu_id", $this->getIdSequenz());
		$this->setting->set("xagu_template", $this->getLoginTemplate());
		$this->setting->set("xagu_use_camel_case", (int) $this->getUseCamelCase());
		$this->setting->set("xagu_string_to_lower", (int) $this->getStringToLower());
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



	public function getNextId()
	{
		$this->setIdSequenz($this->getIdSequenz()+1);
		$this->setting->set("xagu_id", $this->getIdSequenz());
		return $this->getIdSequenz();
	}

	public function isValidContext($a_context)
	{
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
}
?>