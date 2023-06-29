<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * @author Fabian Wolf <wolf@leifos.com>
 */
class ilAutoGenerateUsernamePlugin extends ilEventHookPlugin
{
    protected ilAutoGenerateUsernameConfig $settings;
    private ilDBInterface $ilDB;

    public function __construct(ilDBInterface $db, ilComponentRepositoryWrite $component_repository, string $id)
    {
        global $DIC;
        parent::__construct($db, $component_repository, $id);
        $this->ilDB = $DIC->database();
        $this->settings = new ilAutoGenerateUsernameConfig();
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return "AutoGenerateUsername";
    }

    public function handleEvent(string $a_component, string $a_event, array $a_parameter): void
    {
        ilLoggerFactory::getLogger('usr')->debug('Handling event from ' . $a_component . ' ' . $a_event);

        if($a_component === 'Services/Authentication' && $a_event === 'afterLogin') {
            $user_login = $a_parameter['username'];
            $user_id = ilObjUser::_lookupId($user_login);
            $user = new ilObjUser($user_id);
            $user_auth_method = $user->getAuthMode();
            if ($this->settings->getActiveUpdateExistingUsers() && $this->settings->getAuthModeUpdate() == $user_auth_method) {
                $query = 'update usr_data set login = ' . $this->ilDB->quote($this->generateUsername($user), 'text') . ' ' .
                    'where usr_id = ' . $this->ilDB->quote($user_id, 'integer');
                $this->ilDB->manipulate($query);
            }
        }

        if($a_component === 'Services/User' && $a_event === 'afterCreate') {
            $context = ilUserCreationContext::getInstance();
            if ($this->settings->isValidContext($context->getCurrentContexts())) {
                $user_obj = $a_parameter['user_obj'];
                if ($user_obj instanceof ilObjUser) {
                    $user_obj->updateLogin($this->generateUsername($user_obj));
                }
            }
        }
    }

    private function strPos(string $a_haystack, string $a_needle, ?int $a_offset = null)
    {
        if (function_exists("mb_strpos")) {
            return mb_strpos($a_haystack, $a_needle, $a_offset, "UTF-8");
        } else {
            return strpos($a_haystack, $a_needle, $a_offset);
        }
    }

    private function subStr(string $a_str, int $a_start, ?int $a_length = null): string
    {
        if (function_exists("mb_substr")) {
            // bug in PHP < 5.4.12: null is not supported as length (if encoding given)
            // https://github.com/php/php-src/pull/133
            if ($a_length === null) {
                $a_length = mb_strlen($a_str, "UTF-8");
            }

            return mb_substr($a_str, $a_start, $a_length, "UTF-8");
        } else {
            return substr($a_str, $a_start, $a_length);
        }
    }

    private function strToLower(string $a_string): string
    {
        if (function_exists("mb_strtolower")) {
            return mb_strtolower($a_string, "UTF-8");
        } else {
            return strtolower($a_string);
        }
    }

    private function strLen(string $a_string): int
    {
        if (function_exists("mb_strlen")) {
            return mb_strlen($a_string, "UTF-8");
        } else {
            return strlen($a_string);
        }
    }

    public function generateUsername(ilObjUser $a_usr, bool $a_demo = false): string
    {
        $template = $this->settings->getLoginTemplate();
        $map = $this->getMap($a_usr);

        while ($this->strPos($template, '[') !== false && $this->strPos($template, ']') !== false) {
            $start = $this->strPos($template, '[');
            $end = $this->strPos($template, ']');
            $expression = $this->subStr($template, $start, $end - $start + 1);
            $length = 0;
            $add = 0;
            $replacement = "";

            if ($this->strPos($expression, ":")) {
                $length = (int) $this->substr(
                    $expression,
                    $this->strPos($expression, ":") + 1,
                    $this->strPos($expression, ']') - $this->strPos($expression, ":") - 1
                );

                $var = $this->substr($expression, 1, $this->strPos($expression, ':') - 1);
            } elseif ($this->strPos($expression, "+")) {
                $add = (int) $this->substr(
                    $expression,
                    $this->strPos($expression, "+") + 1,
                    $this->strPos($expression, ']') - $this->strPos($expression, "+") - 1
                );

                $var = $this->substr($expression, 1, $this->strPos($expression, '+') - 1);
            } else {
                $var = $this->substr($expression, 1, $this->strPos($expression, ']') - 1);
            }

            if ($var == "number") {
                if ($a_demo) {
                    $replacement = $this->settings->getIdSequenz();
                } else {
                    $replacement = $this->settings->getNextId();
                }
            } elseif ($var == "hash") {
                $replacement = strrev(uniqid());
            } elseif (in_array($var, array_keys($map))) {
                //adding case to make sure that every replacement is handled as word in CamelCase function
                $replacement = " " . $map[$var];
            }

            if ($length > 0 && $var == "number") {
                while (strlen($replacement) < $length) {
                    $replacement = 0 . $replacement;
                }
            } elseif ($length > 0 && $var != "number") {
                $replacement = $this->substr($replacement, 1, $length);
            }

            if ($var == "number" && $add > 0) {
                $replacement = $replacement + $add;
            }

            $replacement = $this->validateString(
                $replacement,
                $this->settings->getStringToLower(),
                $this->settings->getUseCamelCase(),
                true
            );

            $template = str_replace($expression, $replacement, $template);
        }
        //validate to login
        $template = $this->validateLogin($template);
        $template = $this->generateLogin($template, $a_usr->getId());

        return $template;
    }

    /**
     * generate free login by starting with a default string and adding
     * postfix numbers
     */
    public function generateLogin(string $a_login, int $a_usr_id): string
    {
        // Check if username already exists
        $found = false;
        $postfix = 0;
        $c_login = $a_login;
        while (!$found) {
            $r = $this->ilDB->query("SELECT login FROM usr_data WHERE login = " .
                $this->ilDB->quote($c_login) . ' ' .
                'AND usr_id != ' . $this->ilDB->quote($a_usr_id, ilDBConstants::T_INTEGER));
            if ($r->numRows() > 0) {
                $postfix++;
                $c_login = $a_login . $postfix;
            } else {
                $found = true;
            }
        }

        return $c_login;
    }

    /**
     * @return string[]
     */
    protected function getMap(ilObjUser $a_user): array
    {
        return array_merge($this->getUserMap($a_user), $this->getUDFMap($a_user));
    }

    /**
     * @return string[]
     */
    protected function getUserMap(ilObjUser $a_user): array
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
    protected function getUDFMap(ilObjUser $a_user): array
    {
        $map = array();
        $user_defined_fields = ilUserDefinedFields::_getInstance();
        $user_defined_data = $a_user->getUserDefinedData();
        foreach ($user_defined_fields->getDefinitions() as $field_id => $definition) {
            if ($definition['field_type'] != UDF_TYPE_WYSIWYG) {
                $map["udf_" . $field_id] = $user_defined_data["f_" . $field_id];
            }
        }

        return $map;
    }

    public function camelCase(string $a_string): string
    {
        return str_replace(' ', '', ucwords($a_string));
    }

    public function validateString(string $a_string, bool $a_str_to_lower = false, bool $a_camel_case = false, bool $a_umlauts = false): string
    {
        if ($a_umlauts) {
            $a_string = $this->umlauts($a_string);
        }

        if ($a_str_to_lower || $a_camel_case) {
            $a_string = $this->strToLower($a_string);
        }

        if ($a_camel_case) {
            $a_string = $this->camelCase($a_string);
        } else {
            $a_string = str_replace(' ', '', $a_string);
        }

        return $a_string;
    }

    protected function validateLogin(string $a_login): string
    {
        $a_login = preg_replace('/[^A-Za-z0-9_\.\+\*\@!\$\%\~\-]+/', '', $a_login);
        if (empty($a_login) || $this->strLen($a_login) < 3) {
            return 'invalid_login';
        }
        return $a_login;
    }

    protected function afterUninstall(): void
    {
        $this->settings->deleteAll();
    }

    protected function alphanumeric(string $a_string, string $a_replace = ''): string
    {
        return preg_replace('/[_\.\+\*\@!\$\%\~\-]+/', $a_replace, $a_string);
    }

    protected function umlauts(string $a_string): string
    {
        return iconv("utf-8", "ASCII//TRANSLIT", $a_string);
    }
}
