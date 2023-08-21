<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Exceptions\ValidationException;
use Framework\Validator;
use Framework\Rules\{
    DateFormatRule,
    RequiredRule,
    EmailRule,
    MinRule,
    InListRule,
    URLRule,
    MatchRule,
    DateRule,
    LengthMaxRule,
    NumericRule
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
        $this->validator->addRule('date', new DateRule());
        $this->validator->addRule('maxLength', new LengthMaxRule());
        $this->validator->addRule('isNumeric', new NumericRule());
        $this->validator->addRule('dateFormat', new DateFormatRule());
    }

    public function validateRegister(array $formData)
    {

        $this->validator->validate($formData, [
            'email'           => ['required', 'email'],
            'age'             => ['required', 'min:18'],
            'country'         => ['required', "in_list:USA,Canada,Mexico"],
            'socialMediaURL'  => ['required', "URL"],
            'password'        => ['required'],
            'confirmPassword' => ['required', 'match:password'],
            'tos'             => ['required'],
        ]);
    }

    public function validateLogin(array $formData)
    {
        echo "Validate login";
        $this->validator->validate($formData, [
            'email'    => ['required', 'email'],
            'password' => ['required']
        ]);
    }

    public function validateTransaction(array $formData)
    {
        $this->validator->validate($formData, [
            'description' => ['required', 'maxLength:255'],
            'amount'      => ['required', 'isNumeric'],
            'date'        => ['required', 'date:noFuture', 'dateFormat:Y-m-d'],

        ]);
    }
}
