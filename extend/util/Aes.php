<?php
namespace util;

class Aes
{
    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }
    public function encode($str)
    {
        $key = $this->getSslKey();
        return openssl_encrypt($str, 'AES-128-ECB', $key, 1);
    }

    public function decode($str)
    {
        $key = $this->getSslKey();
        return openssl_decrypt($str, 'AES-128-ECB', $key, 1);
    }

    public function getSslKey()
    {
        $key = substr(openssl_digest(openssl_digest($this->key, 'sha1', true), 'sha1', true), 0, 16);
        return $key;
    }
}