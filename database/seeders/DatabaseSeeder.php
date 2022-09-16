<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Client;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call(TypeArticleTableSeeder::class);
        Article::factory(10)->create();
        Client::factory(10)->create();
        User::factory(10)->create();

        $this->call(RolesTableSeeder::class);
        $this->call(StatutLocationTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(DureeLocationTableSeeder::class);

       /* $user = User::find(1);
        $role = Role::find(1);
        DB::table("user_role"){
            ["user_id" => $user->id,
                "role_id"=>$role->id
            ]
        };*/
        User::find(1)->roles()->attach(1);
        User::find(2)->roles()->attach(2);
        User::find(3)->roles()->attach(3);
        User::find(4)->roles()->attach(4);
    }
}
