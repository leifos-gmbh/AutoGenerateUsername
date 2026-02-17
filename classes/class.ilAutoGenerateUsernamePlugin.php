<?php

/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

declare(strict_types=1);

use Leifos\AutoGenerateUsername\I\Factory as lfAGUDFactoryInterface;
use Leifos\AutoGenerateUsername\Factory as lfAGUDFactory;
use Leifos\AutoGenerateUsername\DB\Settings\Settings;

class ilAutoGenerateUsernamePlugin extends ilEventHookPlugin
{
    protected lfAGUDFactoryInterface $agu_factory;

    public function __construct(
        ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        global $DIC;
        parent::__construct($db, $component_repository, $id);
        $this->agu_factory = new lfAGUDFactory($DIC->language(), $DIC->database());
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
        $settings = $this->agu_factory->db()->settings()->handler();
        ilLoggerFactory::getLogger('usr')->debug('Handling event from ' . $a_component . ' ' . $a_event);
        if($a_component === 'components/ILIAS/Authentication' && $a_event === 'afterLogin') {
            $user_login = $a_parameter['username'];
            $user_id = ilObjUser::_lookupId($user_login);
            $user = new ilObjUser($user_id);
            $user_auth_method = $user->getAuthMode();
            if (
                $settings->readAsBool(Settings::ACTIVE_UPDATE) &&
                $settings->read(Settings::AUTH_MODE_UPDATE) == $user_auth_method
            ) {
                $login = $this->generateUsername($user);
                $this->agu_factory->db()->repository()->updateLogin(
                    $this->agu_factory->db()->user()->handler()
                        ->withLogin($login)
                        ->withId($user_id)
                        ->withName($login)
                );
            }
        }
        if($a_component === 'components/ILIAS/User' && $a_event === 'afterCreate') {
            $context = ilUserCreationContext::getInstance();
            if ($settings->isValidContext($context->getCurrentContexts())) {
                $user_obj = $a_parameter['user_obj'];
                if ($user_obj instanceof ilObjUser) {
                    $user_obj->updateLogin($this->generateUsername($user_obj));
                }
            }
        }
    }

    public function generateUsername(ilObjUser $a_usr, bool $a_demo = false): string
    {
        $settings = $this->agu_factory->db()->settings()->handler();
        $pattern = $this->agu_factory->pattern()->handler()
            ->withPattern($settings->read(Settings::LOGIN_TEMPLATE));
        $new_user = $this->agu_factory->db()->repository()->generateLogin(
            $this->agu_factory->db()->user()->handler()
                ->withName($pattern->buildName($a_usr, $a_demo))
                ->withId($a_usr->getId())
        );
        return $new_user->getLogin();
    }

    protected function afterUninstall(): void
    {
        $settings = $this->agu_factory->db()->settings()->handler();
        $settings->deleteAll();
    }
}
