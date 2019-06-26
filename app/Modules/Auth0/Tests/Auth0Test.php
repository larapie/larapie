<?php

namespace App\Modules\Auth0\Tests;

use App\Modules\Auth0\Actions\CreateOrUpdateUserFromTokenAction;
use App\Modules\Auth0\Traits\FakeAuth0Token;
use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Models\User;
use Larapie\Core\Base\Test;
use Larapie\Core\Traits\MakesApiRequests;
use Larapie\Core\Traits\ResetDatabase;

class Auth0Test extends Test
{
    use ResetDatabase, FakeAuth0Token, MakesApiRequests;

    public function testCreateUserFromToken()
    {
        $action = new CreateOrUpdateUserFromTokenAction([
            "token" => $this->generateToken()
        ]);

        $this->expectsEvents(UserRegisteredEvent::class);
        $user = $action->run();

        $this->assertInstanceOf(User::class, $user);
    }

    public function testAuthorizedRoute(){
        $response = $this->http('GET','/v1/auth0/authorized',[],[
            'Authorization' => 'Bearer '.$this->generateToken()
        ]);

        $this->assertTrue($response->decode());
    }

}
