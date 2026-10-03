<?php
namespace YourCompany\LaravelLicense\Console;
use Illuminate\Console\Command; use YourCompany\LaravelLicense\LicenseManager;
class LicenseRefreshCommand extends Command { protected $signature='license:refresh'; protected $description='Refresh application license from the license server'; public function handle(LicenseManager $m):int{try{$r=$m->refresh();$this->info('License refreshed.');$this->line('Status: '.($r['status']??'unknown'));return self::SUCCESS;}catch(\Throwable $e){$this->error($e->getMessage());return self::FAILURE;}} }
