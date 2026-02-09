<?php

namespace Leifos\AutoGenerateUsername\DB\Settings;

enum StyleOptions: string
{
    case NONE = 'none';
    case CAMEL_CASE = 'camel_case';
    case STRING_TO_LOWER = 'string_to_lower';
}
