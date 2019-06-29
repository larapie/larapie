<?php

namespace App\Packages\Actions\Abstracts;

use App\Packages\Actions\Exceptions\ValidationException;
use Throwable;

abstract class Action extends \Lorisleiva\Actions\Action
{
    protected function failedValidation()
    {
        throw new ValidationException($this->validator);
    }

    public function run(array $attributes = [])
    {
        $this->fill($attributes);
        $this->resolveBeforeHook();
        $this->resolveValidation();
        $this->resolveAuthorization();

        try {
            $value = $this->resolveAndCall($this, 'handle');
        } catch (Throwable $exception) {
            $this->failHook($exception);
        }

        return tap($value, function ($value) {
            $this->successHook($value);
        });
    }

    protected function failHook(Throwable $exception)
    {
        if (! method_exists($this, 'onFail')) {
            throw $exception;
        }
        $this->resolveAndCall($this, 'onFail', compact('exception'));
        throw $exception;
    }

    /* Extensive resolving to ensure you always get the proper object as parameter */
    protected function successHook($result)
    {
        if (method_exists($this, 'onSuccess')) {
            try {
                $parameters = (new \ReflectionMethod($this, 'onSuccess'))->getParameters();
                $extraParameter = $this->resolveParameter($parameters, $result);
            } catch (\ReflectionException $e) {
            }
            $this->resolveAndCall($this, 'onSuccess', $extraParameter ?? []);
        }
    }

    private function resolveParameter($parameters, $result)
    {
        $extraParameter = null;

        //BIND the value to the first parameter with the correct type
        collect($parameters)
            ->filter(function (\ReflectionParameter $parameter) {
                return $parameter->hasType();
            })
            ->filter(function (\ReflectionParameter $parameter) use ($result) {
                return is_a($result, $parameter->getClass()->getName());
            })
            ->each(function (\ReflectionParameter $parameter) use ($result, &$extraParameter) {
                $extraParameter = [$parameter->getName() => $result];

                return false;
            });

        if (isset($extraParameter)) {
            return $extraParameter;
        }

        //BIND the value to the parameter with the same name
        //IF there's not a parameter with the same name as the value type
        //CHOOSE the first value that doesn't have a type
        collect($parameters)
            ->filter(function (\ReflectionParameter $parameter) {
                return ! $parameter->hasType();
            })
            ->each(function (\ReflectionParameter $parameter) use ($result, &$extraParameter) {
                if ($extraParameter === null) {
                    $extraParameter = [$parameter->getName() => $result];
                } elseif (strcasecmp(get_short_class_name($result), $parameter->getName()) == 0) {
                    $extraParameter = [$parameter->getName() => $result];

                    return false;
                }
            });

        return $extraParameter;
    }

    public static function execute(array $data = [])
    {
        return (new static($data))->run();
    }

    public function exists(string $attribute)
    {
        return array_key_exists($attribute, $this->attributes);
    }
}
