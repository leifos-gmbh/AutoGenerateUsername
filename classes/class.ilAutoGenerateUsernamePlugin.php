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
	public function handleEvent($a_component, $a_event, $a_params)
	{
		ilLoggerFactory::getLogger('usr')->debug('Handling event from ' . $a_component .' ' . $a_event);
		switch($a_component)
		{
			case 'Services/Authentication':
				switch($a_event)
				{
					case 'afterLogin':
						$user_login= $a_params['username'];
						//ilLoggerFactory::getRootLogger()->debug(" username = ".$user_login);
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
						include_once('./Services/User/classes/class.ilUserCreationContext.php');

						$context = ilUserCreationContext::getInstance();
						if($this->getSettings()->isValidContext($context->getCurrentContexts()))
						{
							/**
							 * @var ilObjUser $user_obj
							 */
							$user_obj = $a_params['user_obj'];
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

	/**
	 * @param ilObjUser $a_usr
	 * @param bool $a_demo
	 * @return string
	 */
	public function generateUsername($a_usr, $a_demo = false)
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
				$replacement = $map[$var];
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
				$replacement = ilStr::substr($replacement, 0, $length);
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
	public static function _generateLogin($a_login, $a_usr_id)
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
				'AND usr_id != '.$ilDB->quote($a_usr_id,'text'));
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
	 * @param ilObjUser $a_user
	 * @return string[]
	 */
	protected function getMap($a_user)
	{
		return array_merge($this->getUserMap($a_user), $this->getUDFMap($a_user));
	}

	/**
	 * @param ilObjUser $a_user
	 *
	 * @return string[]
	 */
	protected function getUserMap($a_user)
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
	 * @param ilObjUser $a_user
	 * @return string[]
	 */
	protected function getUDFMap($a_user)
	{
		$map = array();
		include_once './Services/User/classes/class.ilUserDefinedFields.php';
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

	/**
	 * @param string $a_string
	 * @return string
	 */
	public function camelCase($a_string)
	{
		//adding case to make sure that every replacement is handled as word in CamelCase function
		return $string = str_replace(' ', '', ucwords(" ".$a_string));
	}

	/**
	 * @param string $a_string
	 * @param bool $a_str_to_lower
	 * @param bool $a_camel_case
	 * @param bool $a_umlauts
	 * @return string
	 */
	public function validateString($a_string, $a_str_to_lower = false, $a_camel_case = false ,$a_umlauts = false)
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

	/**
	 * @return ilAutoGenerateUsernameConfig
	 */
	protected function getSettings()
	{
		if(!$this->settings)
		{
			$this->includeClass("class.ilAutoGenerateUsernameConfig.php");
			$this->settings = new ilAutoGenerateUsernameConfig();
		}

		return $this->settings;
	}

	/**
	 * @param string $a_login
	 * @return string
	 */
	protected function validateLogin($a_login)
	{
		$a_login = preg_replace('/[^A-Za-z0-9_\.\+\*\@!\$\%\~\-]+/', '', $a_login);

		if(empty($a_login) || ilStr::strLen($a_login) < 3)
		{
			return 'invalid_login';
		}
		return $a_login;
	}

	/**
	 * uninstall plugin data
	 */
	protected function afterUninstall()
	{
		$settings = new ilSetting("xagu");

		$settings->deleteAll();
	}

	/**
	 * @param string $a_string
	 * @param string $a_replace
	 * @return string
	 */
	protected function alphanumeric($a_string, $a_replace = '')
	{
		return preg_replace('/[_\.\+\*\@!\$\%\~\-]+/', $a_replace, $a_string);
	}

	/**
	 * @param string $a_string
	 * @return string
	 */
	protected function umlauts($a_string)
	{
		return iconv("utf-8","ASCII//TRANSLIT",$a_string);
	}
}
