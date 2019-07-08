<?php

namespace App\Modules\Auth0\Tests;

use App\Modules\Auth0\Actions\UpsertUserFromTokenAction;
use App\Modules\Auth0\Exceptions\EmailNotVerifiedException;
use App\Modules\Auth0\Traits\FakeAuth0Token;
use App\Modules\User\Events\UserRegisteredEvent;
use App\Modules\User\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Validation\ValidationException;
use Larapie\Core\Base\Test;
use Larapie\Core\Traits\MakesApiRequests;
use Larapie\Core\Traits\ResetDatabase;

class Auth0Test extends Test
{
    use ResetDatabase, FakeAuth0Token, MakesApiRequests;

    public function testCreateUserFromToken()
    {
        $action = new UpsertUserFromTokenAction([
            'token' => $this->generateToken(),
        ]);

        $this->expectsEvents(UserRegisteredEvent::class);
        $user = $action->run();

        $this->assertInstanceOf(User::class, $user);
    }

    public function testCreateUserFromInvalidToken()
    {
        $this->expectException(ValidationException::class);
        (new UpsertUserFromTokenAction([
            'token' => null,
        ]))->run();
    }

    public function testUserNameGetsUpdated()
    {
        $user = (new UpsertUserFromTokenAction([
            'token' => $this->generateToken(),
        ]))->run();

        (new UpsertUserFromTokenAction([
            'token' => $this->generateToken(['name' => 'newname']),
        ]))->run();

        $this->assertEquals('newname', User::find($user->id)->name);
    }

    public function testAuthorizedRoute()
    {
        $response = $this->http('GET', '/v1/auth0/authorized', [], [
            'Authorization' => 'Bearer ' . $this->generateToken(['email' => 'test@test.com']),
        ]);

        $this->assertNotNull(User::where('email', 'test@test.com')->first());

        $this->assertTrue($response->decode());
    }

    public function testInvalidToken()
    {
        $response = $this->http('GET', '/v1/auth0/authorized', [], [
            'Authorization' => 'Bearer ' . 'qsdgqgsd',
        ]);
        $response->assertStatus(401);
    }

    public function testFakeToken()
    {
        $token = $this->generateToken(['name' => 'somename']);
        $decoded = JWT::decode($token, $this->getTokenPublicKey(), ['RS256']);
        $this->assertArrayHasKey('name', (array)$decoded);
        $this->assertEquals('somename', $decoded->name);
    }

    public function testEmailNotVerifiedThrowsError()
    {
        $action = new UpsertUserFromTokenAction([
            'token' => $this->generateToken(['email_verified' => false]),
        ]);

        $this->expectException(EmailNotVerifiedException::class);
        $action->run();
    }

    public function testUnverifiedEmailThroughHttp()
    {
        $response = $this->http('GET', '/v1/auth0/authorized', [], [
            'Authorization' => 'Bearer ' . $this->generateToken(['email_verified' => false]),
        ]);
        $response->assertStatus(401);
    }
}
