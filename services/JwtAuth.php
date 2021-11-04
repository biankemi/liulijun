<?php
namespace micro\services;

use \Firebase\JWT\JWT;

class JwtAuth
{
    private string $iss = 'api.tets.com'; //签发人
    private string $aud = 'api.tets.com'; //受众
    private string $key = '#dsafasieg92fsdyhb3290&$#%#@342';

    /**
     * @param $uid
     * @return string
     */
    public function encode($uid): string
    {
        $time = time();
        $payload = array(
            "iss" => $this->iss,
            "aud" => $this->aud,
            "iat" => $time,
            "nbf" => $time,
            "uid" => $uid
        );
        return JWT::encode($payload, $this->key);
    }

    /**
     * @param $token
     * @return object
     */
    public function decode($token): object
    {
        return JWT::decode($token, $this->key, array('HS256'));
    }

}