<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StoreCountryData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:country-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $countries = DB::table('tbcountries')->get();

        // Store each country in the database
        foreach ($countries as $country) {
            DB::table('tbcountries_data')->insert([
                    'name' => $country->name_ar,
                    'lang_id' => 1,
                    'country_id' => $country->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            DB::table('tbcountries_data')->insert([
                    'name' => $country->name_en,
                    'lang_id' => 2,
                    'country_id' => $country->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->info('Country data stored successfully.');
    }
}
