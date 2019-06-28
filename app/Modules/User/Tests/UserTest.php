<?php

namespace App\Modules\User\Tests;

use App\Modules\User\Actions\CreateUserAction;
use App\Modules\User\Events\UserRegisteredEvent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Larapie\Core\Base\Test;
use Larapie\Core\Traits\DispatchedEvents;
use Larapie\Core\Traits\ResetDatabase;

class UserTest extends Test
{
    use ResetDatabase, DispatchedEvents;

    public function testSomething()
    {
        $this->assertTrue(true);
    }

    public function testCreateUserAction()
    {
        $action = new CreateUserAction([
            'name' => 'a random name',
            'email' => 'gsqdgqdsqgsd@gmail.com',
            'password' => Hash::make(Str::random()),
        ]);

        $this->expectsEvents(UserRegisteredEvent::class);
        $action->run();

        $this->assertTrue(true);
    }
}
