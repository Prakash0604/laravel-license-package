<?php
namespace YourCompany\LaravelLicense;
use RuntimeException;
class LicenseVerifier
{
    public function verify(array $license): bool
    {
        $signature=$license['signature']??null; $publicKey=(string)($license['public_key']??config('license.public_key'));
        if(!$signature||!$publicKey)return false;
        $copy=$license; unset($copy['signature']);
        $encoded=base64_decode((string)$signature,true); $key=base64_decode($publicKey,true);
        if($encoded===false||$key===false||strlen($key)!==SODIUM_CRYPTO_SIGN_PUBLICKEYBYTES)return false;
        return sodium_crypto_sign_verify_detached($encoded,$this->canonical($copy),$key);
    }
    public function freshness(array $license): bool
    {
        try {
            $issued=new \DateTimeImmutable($license['issued_at']??''); $lease=new \DateTimeImmutable($license['lease_expires_at']??'');
            $now=new \DateTimeImmutable('now'); $skew=(int)config('license.max_clock_skew_seconds',300);
            if($issued->getTimestamp()>$now->getTimestamp()+$skew)return false;
            return $lease>$now;
        } catch(\Throwable){return false;}
    }
    private function canonical(array $payload): string { ksort($payload); return json_encode($payload,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRESERVE_ZERO_FRACTION|JSON_THROW_ON_ERROR); }
}
