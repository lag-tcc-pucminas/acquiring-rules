<?php

declare(strict_types=1);

use App\Model\Acquirer;
use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class AcquirerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Db::table('acquirers')->insert([
            'name' => Acquirer::GREEN,
            'default' => true
        ]);

        Db::table('acquirers')->insert([
            'name' => Acquirer::RED,
            'default' => false
        ]);

        Db::table('acquirers')->insert([
            'name' => Acquirer::BLUE,
            'default' => false
        ]);
    }
}
