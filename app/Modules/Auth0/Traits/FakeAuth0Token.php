<?php

namespace App\Modules\Auth0\Traits;

use Auth0\SDK\Helpers\Cache\CacheHandler;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Config;

/**
 * This Trait is intended to be used for testing only!
 * This changes the auth0 config parameters
 * To ensure the token will be deemed valid.
 */
trait FakeAuth0Token
{
    protected function generateToken($payload = [])
    {
        $domain = 'https://larapie.io';
        $audience = $domain;
        $kid = 'a_random_kid';

        $cacheHandler = $this->app->make(CacheHandler::class);
        $cacheKey = $domain.'.well-known/jwks.json|'.$kid;
        $cacheHandler->set($cacheKey, $this->getTokenPublicKey());

        Config::set('laravel-auth0.domain', $domain);
        Config::set('laravel-auth0.authorized_issuers', [$domain]);
        Config::set('laravel-auth0.client_id', $domain);
        Config::set('laravel-auth0.client_secret', 'some_secret');

        $data = [
            'given_name' => 'Lara',
            'family_name' => 'Pie',
            'nickname' => 'larapie',
            'name' => 'Lara Pie',
            'picture' => 'https://cdn4.iconfinder.com/data/icons/logos-3/256/laravel-512.png',
            'email' => 'info@larapie.io',
            'email_verified' => true,
            'iss' => $domain,
            'sub' => 'google-oauth2|102251668224605606587',
            'aud' => $audience,
            'iat' => Carbon::now()->unix(),
            'exp' => Carbon::now()->addDay()->unix(),
        ];
        $payload = array_merge($data, $payload);

        $header = [
            'kid' => $kid,
        ];

        return JWT::encode($payload, $this->getTokenPrivateKey(), 'RS256', null, $header);
    }

    protected function getTokenPrivateKey()
    {
        return <<<'EOD'
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQC8kGa1pSjbSYZVebtTRBLxBz5H4i2p/llLCrEeQhta5kaQu/Rn
vuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t0tyazyZ8JXw+KgXTxldMPEL9
5+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4ehde/zUxo6UvS7UrBQIDAQAB
AoGAb/MXV46XxCFRxNuB8LyAtmLDgi/xRnTAlMHjSACddwkyKem8//8eZtw9fzxz
bWZ/1/doQOuHBGYZU8aDzzj59FZ78dyzNFoF91hbvZKkg+6wGyd/LrGVEB+Xre0J
Nil0GReM2AHDNZUYRv+HYJPIOrB0CRczLQsgFJ8K6aAD6F0CQQDzbpjYdx10qgK1
cP59UHiHjPZYC0loEsk7s+hUmT3QHerAQJMZWC11Qrn2N+ybwwNblDKv+s5qgMQ5
5tNoQ9IfAkEAxkyffU6ythpg/H0Ixe1I2rd0GbF05biIzO/i77Det3n4YsJVlDck
ZkcvY3SK2iRIL4c9yY6hlIhs+K9wXTtGWwJBAO9Dskl48mO7woPR9uD22jDpNSwe
k90OMepTjzSvlhjbfuPN1IdhqvSJTDychRwn1kIJ7LQZgQ8fVz9OCFZ/6qMCQGOb
qaGwHmUK6xzpUbbacnYrIM6nLSkXgOAwv7XXCojvY614ILTK3iXiLBOxPu5Eu13k
eUz9sHyD6vkgZzjtxXECQAkp4Xerf5TGfQXGXhxIX52yH+N2LtujCdkQZjXAsGdm
B2zNzvrlgRmgBrklMTrMYgm1NPcW+bRLGcwgW2PTvNM=
-----END RSA PRIVATE KEY-----
EOD;
    }

    protected function getTokenPublicKey()
    {
        return <<<'EOD'
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
ehde/zUxo6UvS7UrBQIDAQAB
-----END PUBLIC KEY-----
EOD;
    }
}
