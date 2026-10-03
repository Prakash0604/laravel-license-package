<?php
namespace YourCompany\LaravelLicense;
use Illuminate\Support\Facades\Http;
use RuntimeException;
class LicenseClient
{
    public function activate(string $id): array { return $this->send('activate',$this->payload($id),null); }
    public function check(string $id,?string $token): array { return $this->send('check',$this->payload($id),$token); }
    public function deactivate(string $id,?string $token): array { return $this->send('deactivate',$this->payload($id),$token); }
    private function send(string $endpoint,array $payload,?string $token): array
    {
        $server=rtrim((string)config('license.server'),'/'); if(!$server)throw new RuntimeException('LICENSE_SERVER is not configured.');
        $request=Http::acceptJson()->asJson()->timeout(10)->connectTimeout(4)->retry(2,250,throw:false);
        if($token)$request=$request->withToken($token);
        $response=$request->post($server.'/api/v1/license/'.$endpoint,$payload);
        if(!$response->successful())throw new RuntimeException($response->json('message')?:'License server request failed (HTTP '.$response->status().').');
        $data=$response->json(); if(!is_array($data))throw new RuntimeException('Invalid license server response.'); return $data;
    }
    private function payload(string $id): array
    {
        $domain=config('license.domain') ?: (app()->runningInConsole()?parse_url(config('app.url'),PHP_URL_HOST):request()->getHost());
        return ['license_key'=>config('license.key'),'installation_id'=>$id,'domain'=>$domain,'environment'=>config('license.environment'),'metadata'=>['php_version'=>PHP_VERSION,'laravel_version'=>app()->version()]];
    }
}
