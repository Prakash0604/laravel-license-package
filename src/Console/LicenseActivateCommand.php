<?php
namespace YourCompany\LaravelLicense\Console;
use Illuminate\Console\Command; use YourCompany\LaravelLicense\LicenseManager;
class LicenseActivateCommand extends Command { protected $signature='license:activate'; protected $description='Activate this application installation'; public function handle(LicenseManager $m):int{try{$r=$m->activate();$this->info('License activated.');$this->line('Status: '.($r['status']??'unknown'));return self::SUCCESS;}catch(\Throwable $e){$this->error($e->getMessage());return self::FAILURE;}} }
