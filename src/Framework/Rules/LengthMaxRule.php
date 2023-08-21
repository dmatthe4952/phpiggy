<?php

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

use InvalidArgumentException;

class LengthMaxRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        if (empty($params)) {
            throw new InvalidArgumentException("No max length given. Check ValidatorService.");
        }
        $length = (int) $params[0];

        return strlen($data[$field]) < $length;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "String exceeds maximum length of {$params[0]} characters.";
    }
}
