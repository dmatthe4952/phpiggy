<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;
use InvalidArgumentException;

class MinRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        if (empty($params)) {
            throw new InvalidArgumentException(
                "No minimum given for rule. Check ValidationService listing."
            );
        }
        $length = (int) $params[0];

        return $length <= $data[$field];
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Must be at least {$params[0]}.";
    }
}
