<?php

namespace App\Packages\Actions\Exceptions;

class ValidationException extends \Exception
{

    public function __construct(\Illuminate\Contracts\Validation\Validator $validator)
    {
        parent::__construct($this->buildMessage($validator));
    }

    protected function buildMessage(\Illuminate\Contracts\Validation\Validator $validator)
    {
        $baseMessage = "The given data was invalid. ";
        foreach ($validator->getMessageBag()->getMessages() as $attribute => $messages) {
            foreach($messages as $message)
            $baseMessage = $baseMessage . ' - ' . $message;
        }
        return $baseMessage;
    }

}
