<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use WeDevs\PM\Role\Models\Role;
use Carbon\Carbon;

class ProSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = wp_get_current_user();
        if ( ! Role::where('slug', 'client')->exists() ) {
            Role::insert([
                [
                    'title'       => 'Client',
                    'slug'        => 'client',
                    'description' => 'Client is a person who provid the project.',
                    'status'      => 1,
                    'created_by'  => $user->ID,
                    'updated_by'  => $user->ID,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ],
            ]);
        }
    }

}