<?php

/**
 * Auto Generate Username configuration class
 *
 * @author Fabian Wolf <wolf@leifos.com>
 *
 */
class ilAutoGenerateUsernameConfig
{
    private const SETTING_ALLOWED_CONTEXTS = 'xagu_contexts';
    private const SETTING_LOGIN_TEMPLATE = 'xagu_template';
    private const SETTING_ID_SEQUENCE = 'xagu_id';
    private const SETTING_CAMEL_CASE = 'xagu_use_camel_case';
    private const SETTING_STRING_TO_LOWER = 'xagu_string_to_lower';
    private const SETTING_ACTIVE_UPDATE = 'xagu_active_update';
    private const SETTING_AUTH_MODE_UPDATE = 'xagu_auth_mode';
    private ilSetting $setting;
    private ilLanguage $lng;

    public function __construct()
    {
        global $DIC;
        $this->setting = new \ilSetting('xagu');
        $this->lng = $DIC->language();
    }

    public function deleteAll(): void
    {
        $this->setting->deleteAll();
    }

    public function setAllowedContexts(array $allowed_contexts): void
    {
        $this->setting->set(self::SETTING_ALLOWED_CONTEXTS, implode(';', $allowed_contexts));
    }

    public function getAllowedContexts(): array
    {
        return explode(';', $this->setting->get(self::SETTING_ALLOWED_CONTEXTS, ""));
    }

    public function setIdSequenz(int $id_sequenz): void
    {
        $this->setting->set(self::SETTING_ID_SEQUENCE, (string) $id_sequenz);
    }

    public function getIdSequenz(): int
    {
        return (int) $this->setting->get(self::SETTING_ID_SEQUENCE, 1);
    }

    public function setLoginTemplate(string $login_template): void
    {
        $this->setting->set(self::SETTING_LOGIN_TEMPLATE, $login_template);
    }

    public function getLoginTemplate(): string
    {
        return $this->setting->get(self::SETTING_LOGIN_TEMPLATE, "[login]");
    }

    public function setStringToLower(bool $string_to_lower): void
    {
        $this->setting->set(self::SETTING_STRING_TO_LOWER, (string) $string_to_lower);
    }

    public function getStringToLower(): bool
    {
        return (bool) $this->setting->get(self::SETTING_STRING_TO_LOWER, true);
    }

    public function setUseCamelCase(bool $use_camelCase): void
    {
        $this->setting->set(self::SETTING_CAMEL_CASE, (string) $use_camelCase);
    }

    public function getUseCamelCase(): bool
    {
        return (bool) $this->setting->get(self::SETTING_CAMEL_CASE, false);
    }

    public function setActiveUpdateExistingUsers(bool $active_update): void
    {
        $this->setting->set(self::SETTING_ACTIVE_UPDATE, (string) $active_update);
    }

    public function getActiveUpdateExistingUsers(): bool
    {
        return (bool) $this->setting->get(self::SETTING_ACTIVE_UPDATE, false);
    }

    public function setAuthModeUpdate(string $mode): void
    {
        $this->setting->set(self::SETTING_AUTH_MODE_UPDATE, $mode);
    }

    public function getAuthModeUpdate(): string
    {
        return $this->setting->get(self::SETTING_AUTH_MODE_UPDATE, 'default');
    }

    public function getNextId(): int
    {
        $id_sequence = $this->getIdSequenz();
        $this->setIdSequenz($id_sequence + 1);
        return $id_sequence + 1;
    }

    public function isValidContext($a_context): bool
    {
        if (in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $a_context) && in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $this->getAllowedContexts())) {
            return true;
        }

        if (in_array(ilUserCreationContext::CONTEXT_LDAP, $a_context) && in_array(ilUserCreationContext::CONTEXT_LDAP, $this->getAllowedContexts())) {
            return true;
        }

        return false;
    }

    public function getStringActiveAuthModes(): array
    {
        $modes = array();
        foreach (ilAuthUtils::_getActiveAuthModes() as $mode_name => $mode) {
            if (ilLDAPServer::isAuthModeLDAP($mode)) {
                $server = ilLDAPServer::getInstanceByServerId(ilLDAPServer::getServerIdByAuthMode($mode));
                $name = $server->getName();
                $modes[$mode_name] = $name;
            } else {
                $modes[$mode_name] = $this->lng->txt("auth_" . $mode_name);
            }
        }
        return $modes;
    }
}
