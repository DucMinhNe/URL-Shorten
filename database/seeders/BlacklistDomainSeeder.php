<?php

namespace Database\Seeders;

use App\Models\BlacklistDomain;
use Illuminate\Database\Seeder;

class BlacklistDomainSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['spam.test', 'phishing.example', 'malware.invalid', 'virus.test', 'adfraud.example'] as $d) {
            BlacklistDomain::updateOrCreate(['domain' => $d], ['reason' => 'Flagged automatically']);
        }
    }
}
