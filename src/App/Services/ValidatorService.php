<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Exceptions\ValidationException;
use Framework\Validator;
use Framework\Rules\{
    RequiredRule,
    EmailRule,
    MinRule,
    InListRule,
    URLRule,
    MatchRule
};

class ValidatorService
{
    private Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
        $this->validator->addRule("required", new RequiredRule());
        $this->validator->addRule("email", new EmailRule());
        $this->validator->addRule('min', new MinRule());
        $this->validator->addRule('in_list', new InListRule());
        $this->validator->addRule('URL', new URLRule());
        $this->validator->addRule('match', new MatchRule());
    }

    public function validateRegister(array $formData)
    {

        $this->validator->validate($formData, [
            'email' => ['required', 'email'],
            'age' => ['required', 'min:18'],
            'country' => ['required', "in_list:USA,Canada,Mexico"],
            'socialMediaURL' => ['required', "URL"],
            'password' => ['required'],
            'confirmPassword' => ['required', 'match:password'],
            'tos' => ['required'],
        ]);
    }
}
