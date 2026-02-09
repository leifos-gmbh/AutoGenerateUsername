<?php

namespace Leifos\AutoGenerateUsername\DB\Settings;

enum Settings : string
{
    case ALLOWED_CONTEXTS = 'xagu_contexts';
    case LOGIN_TEMPLATE = 'xagu_template';
    case ID_SEQUENCE = 'xagu_id';
    case STYLE_RADIO = "style_radio";
    case ACTIVE_UPDATE = 'xagu_active_update';
    case AUTH_MODE_UPDATE = 'xagu_auth_mode';
}
