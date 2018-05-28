<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // SEEDS TABLE: users
        DB::table('users')->delete();
        DB::table('users')->insert([
            [
                'id' => 1,
                'firstname' => 'N/A',
                'lastname' => '',
                'email' => 'N/A',
                'password' => '',
                'verified' => true,
                'verification_token' => null,
                'user_level' => 1,
                'wrote_review' => true,
                'address' => 'N/A',
                'city' => '',
                'state' => '',
                'postcode' => '',
                'country' => 'N/A',
                'phone' => 'N/A',
                'cellphone' => 'N/A'
            ],
            [
                'id' => 2,
                'firstname' => 'IRWAN',
                'lastname' => 'SISWANDY',
                'email' => 'primasakti_copycenter@yahoo.com',
                'password' => bcrypt('22101982'),
                'verified' => 1,
                'verification_token' => null,
                'user_level' => 1,
                'wrote_review' => 1,
                'address' => 'JL. RAYA TENGGILIS NO. 34-34A',
                'city' => 'SURABAYA',
                'state' => 'JAWA TIMUR',
                'postcode' => '60292',
                'country' => 'INDONESIA',
                'phone' => '+62318484808',
                'cellphone' => '+6281294060954'
            ],
            [
                'id' => 3,
                'firstname' => 'IRWAN',
                'lastname' => 'SISWANDY',
                'email' => 'irwan_up@hotmail.com',
                'password' => bcrypt('22101982'),
                'verified' => 1,
                'verification_token' => null,
                'user_level' => 2,
                'wrote_review' => 1,
                'address' => 'JL. KUTISARI INDAH UTARA IV / 16',
                'city' => 'SURABAYA',
                'state' => 'JAWA TIMUR',
                'postcode' => '60291',
                'country' => 'INDONESIA',
                'phone' => '+62318437683',
                'cellphone' => '+6281294060954'
            ],
            [
                'id' => 4,
                'firstname' => 'IRWAN',
                'lastname' => 'SISWANDY',
                'email' => 'irwansiswandymks@gmail.com',
                'password' => bcrypt('22101982'),
                'verified' => 1,
                'verification_token' => null,
                'user_level' => 3,
                'wrote_review' => 1,
                'address' => 'JL. RAYA TENGGILIS NO. 34-34A',
                'city' => 'SURABAYA',
                'state' => 'JAWA TIMUR',
                'postcode' => '60292',
                'country' => 'INDONESIA',
                'phone' => '+62318484808',
                'cellphone' => '+6281294060954'
            ]
        ]);
        
        // SEEDS TABLE: working_teams
        DB::table('working_teams')->delete();
        DB::table('working_teams')->insert([
            ['name' => 'PRIMA'],
            ['name' => 'SAKTI'],
            ['name' => 'TENGGILIS']
        ]);

        // SEEDS TABLE: shop_schedules
        DB::table('shop_schedules')->delete();
        DB::table('shop_schedules')->insert([
            ['day' => 1, 'open_hour' => 8, 'open_minute' => 0, 'closed_hour' => 21, 'closed_minute' => 0],
            ['day' => 2, 'open_hour' => 8, 'open_minute' => 0, 'closed_hour' => 21, 'closed_minute' => 0],
            ['day' => 3, 'open_hour' => 8, 'open_minute' => 0, 'closed_hour' => 21, 'closed_minute' => 0],
            ['day' => 4, 'open_hour' => 8, 'open_minute' => 0, 'closed_hour' => 21, 'closed_minute' => 0],
            ['day' => 5, 'open_hour' => 8, 'open_minute' => 0, 'closed_hour' => 21, 'closed_minute' => 0],
            ['day' => 6, 'open_hour' => 8, 'open_minute' => 0, 'closed_hour' => 18, 'closed_minute' => 0],
            ['day' => 0, 'open_hour' => 15, 'open_minute' => 0, 'closed_hour' => 21, 'closed_minute' => 0],
        ]);
      
        // SEEDS TABLE: countries
        DB::table('countries')->delete();
    	DB::table('countries')->insert([
    		['id' => '1', 'code' => 'ID', 'name' => 'INDONESIA', 'phonecode' => '+62']
    	]);

        // SEEDS TABLE: states
        DB::table('states')->delete();
        DB::table('states')->insert([
            ['id' => '1', 'name' => strtoupper('Bali')],
            ['id' => '2', 'name' => strtoupper('Banten')],
            ['id' => '3', 'name' => strtoupper('Bengkulu')],
            ['id' => '4', 'name' => strtoupper('DI Aceh')],
            ['id' => '5', 'name' => strtoupper('DI Papua')],
            ['id' => '6', 'name' => strtoupper('DI Papua Barat')],
            ['id' => '7', 'name' => strtoupper('DI Yogyakarta')],
            ['id' => '8', 'name' => strtoupper('DKI Jakarta')],
            ['id' => '9', 'name' => strtoupper('Gorontalo')],
            ['id' => '10', 'name' => strtoupper('Jambi')],
            ['id' => '11', 'name' => strtoupper('Jawa Barat')],
            ['id' => '12', 'name' => strtoupper('Jawa Tengah')],
            ['id' => '13', 'name' => strtoupper('Jawa Timur')],
            ['id' => '14', 'name' => strtoupper('Kalimantan Barat')],
            ['id' => '15', 'name' => strtoupper('Kalimantan Selatan')],
            ['id' => '16', 'name' => strtoupper('Kalimantan Tengah')],
            ['id' => '17', 'name' => strtoupper('Kalimantan Timur')],
            ['id' => '18', 'name' => strtoupper('Kalimantan Utara')],
            ['id' => '19', 'name' => strtoupper('Kep. Bangka-Belitung')],
            ['id' => '20', 'name' => strtoupper('Kep. Riau')],
            ['id' => '21', 'name' => strtoupper('Lampung')],
            ['id' => '22', 'name' => strtoupper('Maluku')],
            ['id' => '23', 'name' => strtoupper('Maluku Utara')],
            ['id' => '24', 'name' => strtoupper('Nusa Tenggara Barat')],
            ['id' => '25', 'name' => strtoupper('Nusa Tenggara Timur')],
            ['id' => '26', 'name' => strtoupper('Riau')],
            ['id' => '27', 'name' => strtoupper('Sulawesi Barat')],
            ['id' => '28', 'name' => strtoupper('Sulawesi Selatan')],
            ['id' => '29', 'name' => strtoupper('Sulawesi Tengah')],
            ['id' => '30', 'name' => strtoupper('Sulawesi Tenggara')],
            ['id' => '31', 'name' => strtoupper('Sulawesi Utara')],
            ['id' => '32', 'name' => strtoupper('Sumatra Barat')],
            ['id' => '33', 'name' => strtoupper('Sumatra Selatan')],
            ['id' => '34', 'name' => strtoupper('Sumatra Utara')] 
        ]);

        // SEEDS TABLE: country_state
        for ($i=1; $i<=34; $i++) // ATTACH state_id (1 to 34) to country_id = 1 (INDONESIA)
        {
            DB::table('country_state')->insert([
                ['country_id' => '1', 'state_id' => $i]
            ]);
        }
    }
}
