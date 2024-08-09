<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Helpers\SlugHelper;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\Artist::factory(20)->create();
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // $provinces = [
        //     "An Giang", "Bà Rịa - Vũng Tàu", "Bạc Liêu", "Bắc Kạn", "Bắc Giang",
        //     "Bắc Ninh", "Bến Tre", "Bình Dương", "Bình Định", "Bình Phước",
        //     "Bình Thuận", "Cà Mau", "Cao Bằng", "Cần Thơ", "Đà Nẵng",
        //     "Đắk Lắk", "Đắk Nông", "Điện Biên", "Đồng Nai", "Đồng Tháp",
        //     "Gia Lai", "Hà Giang", "Hà Nam", "Hà Nội", "Hà Tĩnh",
        //     "Hải Dương", "Hải Phòng", "Hậu Giang", "Hòa Bình", "Hưng Yên",
        //     "Khánh Hòa", "Kiên Giang", "Kon Tum", "Lai Châu", "Lâm Đồng",
        //     "Lạng Sơn", "Lào Cai", "Long An", "Nam Định", "Nghệ An",
        //     "Ninh Bình", "Ninh Thuận", "Phú Thọ", "Phú Yên", "Quảng Bình",
        //     "Quảng Nam", "Quảng Ngãi", "Quảng Ninh", "Quảng Trị", "Sóc Trăng",
        //     "Sơn La", "Tây Ninh", "Thái Bình", "Thái Nguyên", "Thanh Hóa",
        //     "Thừa Thiên Huế", "Tiền Giang", "TP. Hồ Chí Minh", "Trà Vinh", "Tuyên Quang",
        //     "Vĩnh Long", "Vĩnh Phúc", "Yên Bái"
        // ];

        // foreach ($provinces as $province) {
        //     DB::table('regions')->insert([
        //         'name' => $province,
        //         'code' => SlugHelper::convertToSlug($province)
        //     ]);
        // }

        // $permissions = [
        //     'films.read',
        //     'films.create',
        //     'films.update',
        //     'films.delete',

        //     'artists.read',
        //     'artists.create',
        //     'artists.update',
        //     'artists.delete',

        //     'genres.read',
        //     'genres.create',
        //     'genres.update',
        //     'genres.delete',

        //     'cinemas.companies.read',
        //     'cinemas.companies.create',
        //     'cinemas.companies.update',
        //     'cinemas.companies.delete',

        //     'cinemas.branches.read',
        //     'cinemas.branches.create',
        //     'cinemas.branches.update',
        //     'cinemas.branches.delete',

        //     'auditoria.read',
        //     'auditoria.create',
        //     'auditoria.update',
        //     'auditoria.delete',

        //     'showtimes.read',
        //     'showtimes.create',
        //     'showtimes.update',
        //     'showtimes.delete',

        //     'roles.read',
        //     'roles.create',
        //     'roles.update',
        //     'roles.delete',

        //     'users.read',
        //     'users.create',
        //     'users.update',
        //     'users.delete'
        // ];

        // foreach ($permissions as $permission) {
        //     DB::table('permissions')->insert([
        //         'name' => $permission,
        //         'code' => $permission,
        //     ]);
        // }
    }
}
