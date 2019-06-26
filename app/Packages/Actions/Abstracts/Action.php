<?php

namespace App\Packages\Actions\Abstracts;


use App\Packages\Actions\Exceptions\ValidationException;

abstract class Action extends \Lorisleiva\Actions\Action
{
    protected function failedValidation()
    {
        throw new ValidationException($this->validator);
    }
}
