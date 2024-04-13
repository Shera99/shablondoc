<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Permission;
use App\Models\Role;
use App\Models\TranslationDirection;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'first_name' => 'admin',
//            'last_name' => 'admin',
//            'email' => 'admin@admin.com',
//            'country_id' => 1,
//            'city_id' => 1,
//            'password' => Hash::make('pass'), // replace 'password' with the actual password
//            'remember_token' => Str::random(10),
//        ]);

        // Reset cached roles and permissions

//        $role = Role::create(['name' => 'Super-Admin']);
//        $user = User::query()->first();
//        $user->assignRole($role);

        $languages = Language::all();
        $translation_directions = $languages->crossJoin($languages)
            ->reject(function ($combination) {
                return $combination[0]->id === $combination[1]->id;
            })
            ->map(function ($combination) {
                return [
                    'source_language_id' => $combination[0]->id,
                    'target_language_id' => $combination[1]->id
                ];
            });
        TranslationDirection::insert($translation_directions->toArray());
    }
}
