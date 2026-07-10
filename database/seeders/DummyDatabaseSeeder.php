<?php

namespace Database\Seeders;

use App\Models\CategoryGallery;
use App\Models\FinancialReport;
use App\Models\Gallery;
use App\Models\OriginCampus;
use App\Models\OriginCity;
use App\Models\Payment;
use App\Models\PendaftaranBaru;
use App\Models\Resident;
use App\Models\RoomNumber;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DummyDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with reusable dummy data.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $admin = User::updateOrCreate(
            ['email' => 'admin@asrama.test'],
            [
                'name' => 'Admin Asrama',
                'password' => 'password',
                'role' => 'Admin',
            ]
        );

        $users = collect([
            ['name' => 'Budi Santoso', 'email' => 'budi@asrama.test'],
            ['name' => 'Siti Aminah', 'email' => 'siti@asrama.test'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky@asrama.test'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@asrama.test'],
            ['name' => 'Andi Wijaya', 'email' => 'andi@asrama.test'],
        ])->map(function (array $user) {
            return User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => 'password',
                    'role' => 'User',
                ]
            );
        });

        $categories = collect([
            ['name' => 'Fasilitas', 'description' => 'Foto fasilitas asrama'],
            ['name' => 'Kegiatan & Aktifitas', 'description' => 'Dokumentasi kegiatan penghuni'],
            ['name' => 'Hiburan', 'description' => 'Kegiatan santai dan hiburan'],
        ])->mapWithKeys(function (array $category) {
            $model = CategoryGallery::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );

            return [$category['name'] => $model];
        });

        $campuses = collect([
            'Universitas Gadjah Mada',
            'Universitas Negeri Yogyakarta',
            'Universitas Islam Indonesia',
            'Universitas Ahmad Dahlan',
            'Universitas Sanata Dharma',
        ])->mapWithKeys(function (string $name) {
            return [$name => OriginCampus::firstOrCreate(['name' => $name], ['description' => 'Data dummy'])];
        });

        $cities = collect([
            'DI Yogyakarta',
            'Jawa Tengah',
            'Jawa Barat',
            'DKI Jakarta',
            'Sumatera Barat',
        ])->mapWithKeys(function (string $name) {
            return [$name => OriginCity::firstOrCreate(['name' => $name], ['description' => 'Data dummy'])];
        });

        $rooms = collect(['A1', 'A2', 'B1', 'B2', 'C1', 'C2'])->mapWithKeys(function (string $name) {
            return [$name => RoomNumber::firstOrCreate(['name' => $name], ['description' => 'Kamar dummy'])];
        });

        $pendaftaranRows = [
            [
                'user' => $users[0],
                'nama_lengkap' => 'Budi Santoso',
                'nim' => '230001001',
                'universitas' => 'Universitas Gadjah Mada',
                'program_studi' => 'Teknik Informatika',
                'jenis_kelamin' => 'Laki-laki',
                'no_hp' => '6281211110001',
                'email' => 'budi@asrama.test',
                'alamat_asal' => 'Sleman, DI Yogyakarta',
                'status_pendaftaran' => 'Diterima',
            ],
            [
                'user' => $users[1],
                'nama_lengkap' => 'Siti Aminah',
                'nim' => '230001002',
                'universitas' => 'Universitas Negeri Yogyakarta',
                'program_studi' => 'Pendidikan Matematika',
                'jenis_kelamin' => 'Perempuan',
                'no_hp' => '6281211110002',
                'email' => 'siti@asrama.test',
                'alamat_asal' => 'Semarang, Jawa Tengah',
                'status_pendaftaran' => 'Menunggu',
            ],
            [
                'user' => $users[2],
                'nama_lengkap' => 'Rizky Pratama',
                'nim' => '230001003',
                'universitas' => 'Universitas Islam Indonesia',
                'program_studi' => 'Manajemen',
                'jenis_kelamin' => 'Laki-laki',
                'no_hp' => '6281211110003',
                'email' => 'rizky@asrama.test',
                'alamat_asal' => 'Bandung, Jawa Barat',
                'status_pendaftaran' => 'Diterima',
            ],
            [
                'user' => $users[3],
                'nama_lengkap' => 'Dewi Lestari',
                'nim' => '230001004',
                'universitas' => 'Universitas Ahmad Dahlan',
                'program_studi' => 'Akuntansi',
                'jenis_kelamin' => 'Perempuan',
                'no_hp' => '6281211110004',
                'email' => 'dewi@asrama.test',
                'alamat_asal' => 'Jakarta Selatan, DKI Jakarta',
                'status_pendaftaran' => 'Ditolak',
            ],
        ];

        $pendaftarans = collect($pendaftaranRows)->map(function (array $row) use ($now) {
            $user = $row['user'];
            unset($row['user']);

            $row = array_merge($row, [
                'user_id' => $user->id,
                'nama_wali' => 'Wali ' . $row['nama_lengkap'],
                'semester' => 3,
                'no_ortu_wali' => '6281311110000',
                'nama_ortu_wali' => 'Orang Tua ' . $row['nama_lengkap'],
                'file_berkas' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return PendaftaranBaru::updateOrCreate(
                ['nim' => $row['nim']],
                $this->onlyExistingColumns('pendaftaran_baru', $row)
            );
        });

        $residentRows = [
            [
                'pendaftaran' => $pendaftarans[0],
                'user' => $users[0],
                'name' => 'Budi Santoso',
                'age' => 20,
                'birth_date' => '2005-03-12',
                'address' => 'Sleman, DI Yogyakarta',
                'origin_city_id' => $cities['DI Yogyakarta']->id,
                'origin_campus_id' => $campuses['Universitas Gadjah Mada']->id,
                'phone_number' => '6281211110001',
                'room_number_id' => $rooms['A1']->id,
                'status' => 'active',
                'tanggal_masuk' => $now->toDateString(),
            ],
            [
                'pendaftaran' => $pendaftarans[2],
                'user' => $users[2],
                'name' => 'Rizky Pratama',
                'age' => 21,
                'birth_date' => '2004-08-21',
                'address' => 'Bandung, Jawa Barat',
                'origin_city_id' => $cities['Jawa Barat']->id,
                'origin_campus_id' => $campuses['Universitas Islam Indonesia']->id,
                'phone_number' => '6281211110003',
                'room_number_id' => $rooms['A2']->id,
                'status' => 'active',
                'tanggal_masuk' => $now->copy()->subDays(10)->toDateString(),
            ],
            [
                'pendaftaran' => null,
                'user' => $users[4],
                'name' => 'Andi Wijaya',
                'age' => 22,
                'birth_date' => '2003-11-04',
                'address' => 'Padang, Sumatera Barat',
                'origin_city_id' => $cities['Sumatera Barat']->id,
                'origin_campus_id' => $campuses['Universitas Sanata Dharma']->id,
                'phone_number' => '6281211110005',
                'room_number_id' => $rooms['B1']->id,
                'status' => 'inactive',
                'tanggal_masuk' => $now->copy()->subMonths(6)->toDateString(),
            ],
        ];

        $residents = collect($residentRows)->map(function (array $row) {
            $user = $row['user'];
            $pendaftaran = $row['pendaftaran'];
            unset($row['user'], $row['pendaftaran']);

            $row['user_id'] = $user->id;
            $row['pendaftaran_id'] = $pendaftaran?->id_pendaftaran;

            return Resident::updateOrCreate(
                ['name' => $row['name'], 'phone_number' => $row['phone_number']],
                $this->onlyExistingColumns('residents', $row)
            );
        });

        $residents->each(function (Resident $resident, int $index) use ($now) {
            Payment::updateOrCreate(
                [
                    'resident_id' => $resident->id,
                    'billing_date' => $now->copy()->startOfMonth()->toDateString(),
                ],
                [
                    'billing_amount' => 750000,
                    'status' => $index === 0 ? 'Sudah Dibayar' : 'Belum Dibayar',
                    'payment_evidence' => null,
                    'payment_file_name' => null,
                    'move_to_report' => $index === 0,
                ]
            );
        });

        collect([
            [
                'title' => 'Foto Kamar A1',
                'type' => 'Foto',
                'category_id' => $categories['Fasilitas']->id,
                'url' => 'https://example.com/fasilitas-kamar-a1.jpg',
            ],
            [
                'title' => 'Kegiatan Bersih Asrama',
                'type' => 'Foto',
                'category_id' => $categories['Kegiatan & Aktifitas']->id,
                'url' => 'https://example.com/kegiatan-bersih-asrama.jpg',
            ],
            [
                'title' => 'Malam Keakraban',
                'type' => 'Video',
                'category_id' => $categories['Hiburan']->id,
                'url' => 'https://example.com/malam-keakraban',
            ],
        ])->each(function (array $gallery) {
            Gallery::updateOrCreate(
                ['title' => $gallery['title']],
                array_merge($gallery, [
                    'file' => null,
                    'file_name' => null,
                ])
            );
        });

        collect([
            [
                'title' => 'Pembayaran Iuran Budi Santoso',
                'report_date' => $now->copy()->startOfMonth()->toDateString(),
                'report_amount' => 750000,
                'report_categories' => 'Pemasukan',
            ],
            [
                'title' => 'Pembelian Peralatan Kebersihan',
                'report_date' => $now->copy()->subDays(7)->toDateString(),
                'report_amount' => 250000,
                'report_categories' => 'Pengeluaran',
            ],
        ])->each(function (array $report) {
            FinancialReport::updateOrCreate(
                ['title' => $report['title']],
                array_merge($report, [
                    'report_evidence' => null,
                    'report_file_name' => null,
                ])
            );
        });

        $this->command?->info('Dummy database seeded. Login: admin@asrama.test / password');
        $this->command?->info('Sample user login: budi@asrama.test / password');
    }

    private function onlyExistingColumns(string $table, array $attributes): array
    {
        return collect($attributes)
            ->filter(fn ($value, string $column) => Schema::hasColumn($table, $column))
            ->all();
    }
}
