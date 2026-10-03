<?php
namespace YourCompany\LaravelLicense;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use RuntimeException;
class LicenseManager
{
    private const STATE_FILE='license/state.enc'; private const INSTALL_FILE='license/installation-id';
    public function __construct(private readonly LicenseClient $client,private readonly LicenseVerifier $verifier){}
    public function activate():array { $r=$this->client->activate($this->installationId()); $this->store($r); return $r; }
    public function refresh():array { $state=$this->getState(); $r=$this->client->check($this->installationId(),$state['installation_token']??null); $this->store($r,$state['installation_token']??null); return $r; }
    public function deactivate():array { $state=$this->getState(); $r=$this->client->deactivate($this->installationId(),$state['installation_token']??null); $this->forget(); return $r; }
    public function isValid(): bool
    {
    $state = $this->getState();

    if (!$state || !$this->verifier->verify($state['license'] ?? [])) {
        return false;
    }

    $license = $state['license'];

    if (($license['status'] ?? null) !== 'active') {
        return false;
    }

    if (!$this->verifier->freshness($license)) {
        return false;
    }

    if (!empty($license['expires_at'])) {
        try {
            if (new \DateTimeImmutable($license['expires_at']) <= new \DateTimeImmutable('now')) {
                return false;
            }
        } catch (\Throwable) {
            return false;
        }
    }

    return true;
   }
    public function status():array { $s=$this->getState(); return ['valid'=>$this->isValid(),'license'=>$s['license'] ?? null,'installation_id'=>$this->installationId()]; }
    public function get():?array { $s=$this->getState(); return $s['license']??null; }
    public function installationId():string
    {
        $path=storage_path('app/'.self::INSTALL_FILE); if(is_file($path)){$id=trim((string)file_get_contents($path));if(preg_match('/^[A-Za-z0-9_-]{32}$/',$id))return $id;}
        $id=Str::random(32); if(!is_dir(dirname($path)))mkdir(dirname($path),0750,true); file_put_contents($path,$id,LOCK_EX); return $id;
    }
    public function getState():?array
    { $path=storage_path('app/'.self::STATE_FILE); if(!is_file($path))return null; try{$v=json_decode(Crypt::decryptString(file_get_contents($path)),true,512,JSON_THROW_ON_ERROR);return is_array($v)?$v:null;}catch(\Throwable){return null;} }
    private function store(array $license,?string $oldToken=null):void
    {
        if(!$this->verifier->verify($license)||!$this->verifier->freshness($license))throw new RuntimeException('Invalid or stale license response.');
        $token=$license['installation_token']??$oldToken; if(!$token)throw new RuntimeException('License installation token missing.');
        $state=['license'=>$license,'installation_token'=>$token]; $path=storage_path('app/'.self::STATE_FILE); if(!is_dir(dirname($path)))mkdir(dirname($path),0750,true); file_put_contents($path,Crypt::encryptString(json_encode($state,JSON_THROW_ON_ERROR)),LOCK_EX);
    }
    private function forget():void { $path=storage_path('app/'.self::STATE_FILE); if(is_file($path))@unlink($path); }
}
