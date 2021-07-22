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
    protected $auth_mode_update = 'default';


    public function __construct()
    {
        $this->setting = new \ilSetting('xagu');
        $this->read();
    }

    public function read() : void
    {
        $this->setting->read();
        $this->setAllowedContexts(explode(';', $this->setting->get("xagu_contexts", implode(';', $this->getAllowedContexts()))));
        $this->setIdSequenz($this->setting->get("xagu_id", $this->getIdSequenz()));
        $this->setLoginTemplate($this->setting->get("xagu_template", $this->getLoginTemplate()));
        $this->setUseCamelCase((bool) $this->setting->get("xagu_use_camel_case", $this->getUseCamelCase()));
        $this->setStringToLower((bool) $this->setting->get("xagu_string_to_lower", $this->getStringToLower()));
        $this->setActiveUpdateExistingUsers((bool) $this->setting->get("xagu_active_update", $this->getActiveUpdateExistingUsers()));
        $this->setAuthModeUpdate($this->setting->get('xagu_auth_mode', $this->getAuthModeUpdate()));
    }

    public function update() : void
    {
        $this->setting->set("xagu_contexts", implode(';', $this->getAllowedContexts()));
        $this->setting->set("xagu_id", $this->getIdSequenz());
        $this->setting->set("xagu_template", $this->getLoginTemplate());
        $this->setting->set("xagu_use_camel_case", (int) $this->getUseCamelCase());
        $this->setting->set("xagu_string_to_lower", (int) $this->getStringToLower());
        $this->setting->set("xagu_active_update", (int) $this->getActiveUpdateExistingUsers());
        $this->setting->set("xagu_auth_mode", $this->getAuthModeUpdate());
    }

    public function setAllowedContexts(array $allowed_contexts)
    {
        $this->allowed_contexts = $allowed_contexts;
    }

    public function getAllowedContexts() : array
    {
        return $this->allowed_contexts;
    }

    public function setIdSequenz(int $id_sequenz)
    {
        $this->id_sequenz = $id_sequenz;
    }

    public function getIdSequenz() : int
    {
        return $this->id_sequenz;
    }

    public function setLoginTemplate(string $login_template)
    {
        $this->login_template = $login_template;
    }

    public function getLoginTemplate() : string
    {
        return $this->login_template;
    }

    public function setStringToLower(bool $string_to_lower)
    {
        $this->string_to_lower = $string_to_lower;
    }

    public function getStringToLower() : bool
    {
        return $this->string_to_lower;
    }

    public function setUseCamelCase(bool $use_camelCase)
    {
        $this->use_camelCase = $use_camelCase;
    }

    public function getUseCamelCase() : bool
    {
        return $this->use_camelCase;
    }

    public function setActiveUpdateExistingUsers(bool $active_update)
    {
        $this->active_update = $active_update;
    }

    public function getActiveUpdateExistingUsers() : bool
    {
        return $this->active_update;
    }

    public function setAuthModeUpdate(string $mode)
    {
        $this->auth_mode_update = $mode;
    }

    public function getAuthModeUpdate() : string
    {
        return $this->auth_mode_update;
    }

    public function getNextId() : int
    {
        $this->setIdSequenz($this->getIdSequenz() + 1);
        $this->setting->set("xagu_id", $this->getIdSequenz());
        return $this->getIdSequenz();
    }

    public function isValidContext($a_context) : bool
    {
        if (in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $a_context) && in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $this->getAllowedContexts())) {
            return true;
        }

        if (in_array(ilUserCreationContext::CONTEXT_LDAP, $a_context) && in_array(ilUserCreationContext::CONTEXT_LDAP, $this->getAllowedContexts())) {
            return true;
        }

        return false;
    }

    public function getStringActiveAuthModes() : array
    {
        global $DIC;

        $lng = $DIC->language();

        $modes = array();
        foreach (ilAuthUtils::_getActiveAuthModes() as $mode_name => $mode) {
            if (ilLDAPServer::isAuthModeLDAP($mode)) {
                $server = ilLDAPServer::getInstanceByServerId(ilLDAPServer::getServerIdByAuthMode($mode));
                $name = $server->getName();
                $modes[$mode_name] = $name;
            } else {
                $modes[$mode_name] = $lng->txt("auth_" . $mode_name);
            }
        }
        return $modes;
    }
}
