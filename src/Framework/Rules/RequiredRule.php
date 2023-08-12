<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class RequiredRule implements RuleInterface
{
    public function validate(array $data, string $field, array $parms): bool
    {
        return !empty($data[$field]);
    }

    public function getMessage(array $data, string $field, array $parms): string
    {
        return "This field is required";
    }
}