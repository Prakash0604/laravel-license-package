<?php
namespace YourCompany\LaravelLicense\Console;
use Illuminate\Console\Command; use YourCompany\LaravelLicense\LicenseManager;
class LicenseStatusCommand extends Command { protected $signature='license:status'; protected $description='Show local license status'; public function handle(LicenseManager $m):int{$s=$m->status();$this->line('Valid: '.($s['valid']?'yes':'no'));$this->line('Installation: '.$s['installation_id']);$l=$s['license'];if($l){$this->line('Status: '.($l['status']??'unknown'));$this->line('Domain: '.($l['domain']??'unknown'));$this->line('Lease: '.($l['lease_expires_at']??'unknown'));}return $s['valid']?self::SUCCESS:self::FAILURE;} }
