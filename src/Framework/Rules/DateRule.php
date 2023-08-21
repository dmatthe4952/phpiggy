<?php

declare(strict_types=1);


namespace Framework\Rules;

use Framework\Contracts\RuleInterface;
use InvalidArgumentException;
use DateTime;

class DateRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        $now = new DateTime('now');
        if (empty($params)) {
            throw new InvalidArgumentException("No limit instruction given. Check ValidatorService.");
        }
        switch ($params[0]) {
            case 'noFuture':
                return $data[$field] < $now->format('Y-m-d H:i:s');
                break;
            case 'noPast':
                return $data[$field] > $now->format('Y-m-d H:i:s');
                break;
            default:
                return false;
        }

        return true;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Date Invalid";
    }
}
