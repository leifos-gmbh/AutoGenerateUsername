<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ilObjUser;
use ilUserDefinedFields;
use Leifos\AutoGenerateUsername\I\Pattern\Node\UDFHandler as lfAGUPatternNodeUDFInterface;
use Leifos\AutoGenerateUsername\Pattern\Handler as lfAGUPattern;

class UDFHandler implements lfAGUPatternNodeUDFInterface
{
    protected ilObjUser $user;
    protected int $field_id;

    public function toString(): string
    {
        if (!isset($this->field_id)) {
            return "";
        }
        $user_defined_fields = ilUserDefinedFields::_getInstance();
        $field_definition = $user_defined_fields->getDefinitions()[$this->field_id] ?? null;
        $user_defined_data = $this->user->getUserDefinedData();
        $f_field_id = "f_" . $this->field_id;
        if (
            is_null($field_definition) ||
            $field_definition['field_type'] === UDF_TYPE_WYSIWYG ||
            !array_key_exists($f_field_id, $user_defined_data)
        ) {
            return "";
        }
        return " " . ($this->user->getUserDefinedData()[$f_field_id] ?? "");
    }

    public function formattContent(): bool
    {
        return true;
    }

    public function withUser(ilObjUser $user): lfAGUPatternNodeUDFInterface
    {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function withFieldId(int $field_id): lfAGUPatternNodeUDFInterface
    {
        $clone = clone $this;
        $clone->field_id = $field_id;
        return $clone;
    }
}