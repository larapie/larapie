<?php

namespace App\Foundation\Tests;

use Larapie\Core\Base\Test;

class FoundationTest extends Test
{

    public function testWebMainPage(){
        $this->json('GET', env('APP_URL'))->assertStatus(200);
    }

    public function testApiMainpage(){
        $this->json('GET', env('API_URL'))->assertStatus(200);
    }
}
