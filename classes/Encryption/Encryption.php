<?php
namespace Encryption;
class Encryption
{
    private $specifiedHash;

    public function __construct($hashType = PASSWORD_DEFAULT)
    {
        $this->specifiedHash = $hashType;
    }

    public function hash($data)
    {
        return password_hash($data, $this->specifiedHash);
    }

    public function verify($exposedData, $hashedData)
    {
        return password_verify($exposedData, $hashedData);
    }

    public function updateHash($hashedValue)
    {
        if (password_needs_rehash($hashedValue, $this->specifiedHash))
        {
            $newHashedValue = password_hash($hashedValue, $this->specifiedHash);

            return $newHashedValue;
        } else
        {
            return $hashedValue;
        }
    }
}
?>