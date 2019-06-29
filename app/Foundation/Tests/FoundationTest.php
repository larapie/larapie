<?php

namespace App\Foundation\Tests;

use App\Packages\Actions\Traits\ApiActionRunner;
use Larapie\Core\Base\Test;
use Larapie\Core\Support\Facades\Larapie;

class FoundationTest extends Test
{
    use ApiActionRunner;

    public function testWebMainPage()
    {
        $this->json('GET', Larapie::getAppUrl())->assertStatus(200);
    }

    public function testApiMainpage()
    {
        $this->json('GET', Larapie::getApiUrl())->assertStatus(200);
    }

    public function testExceptionKernel()
    {
        $response = $this->json('GET', Larapie::getApiUrl().'/somenonexistingroutesgdqjmlgsdgj');
        $response->assertStatus(404);

        $output = $response->decodeResponseJson();

        $this->assertArrayHasKey('error', $output);
        $this->assertArrayHasKeys(['message', 'status_code'], $output['error']);
        $this->assertEquals(404, $output['error']['status_code']);
    }
}
