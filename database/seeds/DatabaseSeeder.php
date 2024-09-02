<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(AuthRoleTableSeeder::class);
        $this->call(AuthRoleModuleTableSeeder::class);
        $this->call(AuthUserTableSeeder::class);

        $this->call(PoMClientTableSeeder::class);
        $this->call(PoMVendorTableSeeder::class);
        $this->call(PoMSiteTableSeeder::class);
        $this->call(PoMFieldtechTableSeeder::class);
        $this->call(PoWoMServiceTableSeeder::class);
        $this->call(PoWoMActivityTableSeeder::class);
        $this->call(PoWoMStatusTableSeeder::class);
        $this->call(PoWoMStatusDetailTableSeeder::class);
        $this->call(PoWoMStatusDetailOptionTableSeeder::class);
    }
}












