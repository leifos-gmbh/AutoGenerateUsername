<?php

namespace Leifos\AutoGenerateUsername\DB\Settings;

enum Settings : string
{
    case ALLOWED_CONTEXTS = 'xagu_contexts';
    case LOGIN_TEMPLATE = 'xagu_template';
    case ID_SEQUENCE = 'xagu_id';
    case CAMEL_CASE = 'xagu_use_camel_case';
    case STRING_TO_LOWER = 'xagu_string_to_lower';
    case ACTIVE_UPDATE = 'xagu_active_update';
    case AUTH_MODE_UPDATE = 'xagu_auth_mode';
}
