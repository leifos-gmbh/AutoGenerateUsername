<?php

namespace Leifos\AutoGenerateUsername\Pattern\Node;

use ILIAS\User\Profile\Profile as UserProfile;
use ilObjUser;
use Leifos\AutoGenerateUsername\I\Pattern\Node\UDFHandler as lfAGUPatternNodeUDFInterface;

class UDFHandler implements lfAGUPatternNodeUDFInterface
{
    protected ilObjUser $user;
    protected int $field_id;

    public function __construct(
        protected readonly UserProfile $profile
    ) {
    }

    public function toString(): string
    {
        if (!isset($this->field_id)) {
            return "";
        }
        $field = $this->profile->getFieldByIdentifier("f_" . $this->field_id);
        return is_null($field) ? "" : trim((string) $field->retrieveValueFromUser($this->user)) ?? "";
    }

    public function formattContent(): bool
    {
        return true;
    }

    public function withUser(
        ilObjUser $user
    ): lfAGUPatternNodeUDFInterface {
        $clone = clone $this;
        $clone->user = $user;
        return $clone;
    }

    public function withFieldId(
        int $field_id
    ): lfAGUPatternNodeUDFInterface {
        $clone = clone $this;
        $clone->field_id = $field_id;
        return $clone;
    }
}
