<?php

/**
 * Auto Generate Username configuration class
 *
 * @author Fabian Wolf <wolf@leifos.com>
 *
 */

declare(strict_types=1);

namespace Leifos\AutoGenerateUsername\DB\Settings;

use Leifos\AutoGenerateUsername\I\DB\Settings\Handler as lfAGUDBSettingsInterface;
use ilSetting;
use ilLanguage;
use ilUserCreationContext;
use ilAuthUtils;
use ilLDAPServer;

class Handler implements lfAGUDBSettingsInterface
{

    private ilSetting $setting;

    public function __construct(
        protected ilLanguage $lng
    ) {
        $this->setting = new \ilSetting('xagu');
    }

    public function deleteAll(): void
    {
        $this->setting->deleteAll();
    }

    public function getNextId(): int
    {
        $id_sequence = $this->readAsInt(Settings::ID_SEQUENCE);
        $this->set(Settings::ID_SEQUENCE, (string) ($id_sequence + 1));
        return $id_sequence + 1;
    }

    public function isValidContext(array $a_context): bool
    {
        if (
            in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $a_context) &&
            in_array(ilUserCreationContext::CONTEXT_REGISTRATION, $this->readAsArray(Settings::ALLOWED_CONTEXTS))
        ) {
            return true;
        }
        if (
            in_array(ilUserCreationContext::CONTEXT_LDAP, $a_context) &&
            in_array(ilUserCreationContext::CONTEXT_LDAP, $this->readAsArray(Settings::ALLOWED_CONTEXTS))
        ) {
            return true;
        }
        return false;
    }

    public function getStringActiveAuthModes(): array
    {
        $modes = [];
        foreach (ilAuthUtils::_getActiveAuthModes() as $mode_name => $mode) {
            if (ilLDAPServer::isAuthModeLDAP((string) $mode)) {
                $server = ilLDAPServer::getInstanceByServerId(ilLDAPServer::getServerIdByAuthMode((string) $mode));
                $name = $server->getName();
                $modes[$mode_name] = $name;
            } else {
                $modes[$mode_name] = $this->lng->txt("auth_" . $mode_name);
            }
        }
        return $modes;
    }

    public function set(
        Settings $setting,
        string $value
    ): void {
        $this->setting->set($setting->value, $value);
    }

    public function read(
        Settings $setting
    ): string {
        return $this->setting->get($setting->value, '');
    }

    public function readAsInt(
        Settings $setting
    ): int {
        return (int) $this->read($setting);
    }

    public function readAsBool(
        Settings $setting
    ): bool {
        return (bool) $this->read($setting);
    }

    public function readAsArray(
        Settings $setting,
        string $separator = ';'
    ): array {
        return explode($separator, $this->read($setting));
    }
}
