<?php

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

use InvalidArgumentException;

class DateFormatRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        if (empty($params)) {
            throw new InvalidArgumentException("No date format given. Check ValidatorService.");
        }

        $parsedDate = date_parse_from_format($params[0], $data[$field]);
        return ($parsedDate['error_count'] === 0 && $parsedDate['warning_count'] === 0);
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Date must be MM/DD/YYYY format";
    }
}
