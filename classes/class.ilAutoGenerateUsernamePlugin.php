<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Fabian Wolf <wolf@leifos.com>
 */
class ilAutoGenerateUsernamePlugin extends ilEventHookPlugin
{
	/**
	 * @var ilAutoGenerateUsernameConfig
	 */
	protected $settings = null;

	/**
	 * @return string
	 */
	final public function getPluginName()
	{
		return "AutoGenerateUsername";
	}

	/**
	 * @param string $a_component
	 * @param string $a_event
	 * @param array $a_params
	 * @return bool
	 * @throws ilUserException
	 */
	public function handleEvent($a_component, $a_event, $a_parameter)
	{
		ilLoggerFactory::getLogger('usr')->debug('Handling event from ' . $a_component .' ' . $a_event);
		switch($a_component)
		{
			case 'Services/Authentication':
				switch($a_event)
				{
					case 'afterLogin':
						$user_login= $a_parameter['username'];
						$user_id = ilObjUser::_lookupId($user_login);
						$user = new ilObjUser($user_id);
						$user_auth_method = $user->getAuthMode();

						if($this->getSettings()->getActiveUpdateExistingUsers() && $this->getSettings()->getAuthModeUpdate() == $user_auth_method)
						{
							$query = 'update usr_data set login = '. $GLOBALS['DIC']->database()->quote($this->generateUsername($user), 'text').' '.
								'where usr_id = '. $GLOBALS['DIC']->database()->quote($user_id,'integer');
							$GLOBALS['DIC']->database()->manipulate($query);
						}
						break;
				}
				break;
			case 'Services/User':
				switch($a_event)
				{
					case 'afterCreate':

						$context = ilUserCreationContext::getInstance();
						if($this->getSettings()->isValidContext($context->getCurrentContexts()))
						{
							/**
							 * @var ilObjUser $user_obj
							 */
							$user_obj = $a_parameter['user_obj'];
							if($user_obj instanceof ilObjUser)
							{
								$user_obj->updateLogin($this->generateUsername($user_obj));
							}
						}
					break;
				}
			break;
		}

		return true;
	}

	public function generateUsername(ilObjUser $a_usr, bool $a_demo = false) : string
	{
		$settings = $this->getSettings();

		$template = $settings->getLoginTemplate();
		$map = $this->getMap($a_usr);


		while(ilStr::strPos($template, '[') !== false && ilStr::strPos($template, ']') !== false)
		{
			$start = ilStr::strPos($template, '[');
			$end = ilStr::strPos($template, ']');
			$expression = ilStr::substr($template, $start, $end-$start+1);
			$length = 0;
			$add = 0;
			$replacement = "";

			if(ilStr::strPos($expression, ":"))
			{
				$length = (int) ilStr::substr($expression,
				    ilStr::strPos($expression, ":")+1 ,
				    ilStr::strPos($expression, ']')-ilStr::strPos($expression, ":")-1);

				$var = ilStr::substr($expression, 1,ilStr::strPos($expression, ':')-1);
			}
			elseif(ilStr::strPos($expression, "+"))
			{
				$add = (int) ilStr::substr($expression,
				   ilStr::strPos($expression, "+")+1 ,
				   ilStr::strPos($expression, ']')-ilStr::strPos($expression, "+")-1);

				$var = ilStr::substr($expression, 1,ilStr::strPos($expression, '+')-1);
			}
			else
			{
				$var = ilStr::substr($expression, 1,ilStr::strPos($expression, ']')-1);
			}

			if($var == "number")
			{
				if($a_demo)
				{
					$replacement = $settings->getIdSequenz();
				}
				else
				{
					$replacement = $settings->getNextId();
				}

			}
			elseif($var == "hash")
			{
				$replacement = strrev(uniqid());
			}
			elseif(in_array($var, array_keys($map)))
			{
				//adding case to make sure that every replacement is handled as word in CamelCase function
				$replacement = " ".$map[$var];
			}

			if($length > 0 && $var == "number")
			{
				while(strlen($replacement) < $length)
				{
					$replacement = 0 . $replacement;
				}
			}
			elseif($length > 0 && $var != "number")
			{
				$replacement = ilStr::substr($replacement, 1, $length);
			}

			if($var == "number" && $add > 0 )
			{
				$replacement = $replacement+$add;
			}

			$replacement = $this->validateString(
				$replacement,
				$settings->getStringToLower(),
				$settings->getUseCamelCase(),
				true);

			$template = str_replace($expression,$replacement, $template);
		}
		//validate to login
		$template = $this->validateLogin($template);

		$template = self::_generateLogin($template, $a_usr->getId());

		return $template;
	}
	
   /**
	* generate free login by starting with a default string and adding
	* postfix numbers
	*/
	public static function _generateLogin(string $a_login, int $a_usr_id)
	{
		global $ilDB;
		
		// Check if username already exists
		$found = false;
		$postfix = 0;
		$c_login = $a_login;
		while(!$found)
		{
			$r = $ilDB->query("SELECT login FROM usr_data WHERE login = ".
				$ilDB->quote($c_login).' '.
				'AND usr_id != '.$ilDB->quote($a_usr_id, ilDBConstants::T_INTEGER));
			if ($r->numRows() > 0)
			{
				$postfix++;
				$c_login = $a_login.$postfix;
			}
			else
			{
				$found = true;
			}
		}
		
		return $c_login;
	}

    /**
     * @return string[]
     */
	protected function getMap(ilObjUser $a_user) : array
	{
		return array_merge($this->getUserMap($a_user), $this->getUDFMap($a_user));
	}

    /**
     * @return string[]
     */
	protected function getUserMap(ilObjUser $a_user) : array
	{
		return array(
			"login" => $a_user->getLogin(),
			"firstname" => $this->alphanumeric($a_user->getFirstname(), ' '),
			"lastname" => $this->alphanumeric($a_user->getLastname(), ' '),
			"email" => $a_user->getEmail(),
			"matriculation" => $a_user->getMatriculation()
		);
	}

	/**
	 * @return string[]
	 */
	protected function getUDFMap(ilObjUser $a_user) : array
	{
		$map = array();
		/**
		 * @var ilUserDefinedFields $user_defined_fields
		 */
		$user_defined_fields = ilUserDefinedFields::_getInstance();
		$user_defined_data = $a_user->getUserDefinedData();
		foreach($user_defined_fields->getDefinitions() as $field_id => $definition)
		{
			if($definition['field_type'] !=  UDF_TYPE_WYSIWYG)
			{
				$map["udf_".$field_id] = $user_defined_data["f_".$field_id];
			}
		}

		return $map;
	}

	public function camelCase(string $a_string) : string
	{
		return $string = str_replace(' ', '', ucwords($a_string));
	}

	public function validateString(string $a_string, bool $a_str_to_lower = false, bool $a_camel_case = false , bool $a_umlauts = false) : string
	{
		if($a_umlauts)
		{
			$a_string = $this->umlauts($a_string);
		}

		if($a_str_to_lower || $a_camel_case)
		{
			$a_string = ilStr::strToLower($a_string);
		}

		if($a_camel_case)
		{
			$a_string = $this->camelCase($a_string);
		}
		else
		{
			$a_string = str_replace(' ', '', $a_string);
		}

		return $a_string;
	}

	protected function getSettings() : ilAutoGenerateUsernameConfig
	{
		if(!$this->settings)
		{
			$this->includeClass("class.ilAutoGenerateUsernameConfig.php");
			$this->settings = new ilAutoGenerateUsernameConfig();
		}

		return $this->settings;
	}

	protected function validateLogin(string $a_login) : string
	{
		$a_login = preg_replace('/[^A-Za-z0-9_\.\+\*\@!\$\%\~\-]+/', '', $a_login);

		if(empty($a_login) || ilStr::strLen($a_login) < 3)
		{
			return 'invalid_login';
		}
		return $a_login;
	}

	protected function afterUninstall()
	{
		$settings = new ilSetting("xagu");

		$settings->deleteAll();
	}

	protected function alphanumeric(string $a_string, string $a_replace = '') : string
	{
		return preg_replace('/[_\.\+\*\@!\$\%\~\-]+/', $a_replace, $a_string);
	}

	protected function umlauts(string $a_string) : string
	{
		return iconv("utf-8","ASCII//TRANSLIT",$a_string);
	}
}
