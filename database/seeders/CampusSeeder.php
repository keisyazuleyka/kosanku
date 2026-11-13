<?php

namespace Database\Seeders;

use App\Models\Campus;
use Illuminate\Database\Seeder;

class CampusSeeder extends Seeder
{
    public function run(): void
    {
        $campuses = [
            // Depok
            [
                'city' => 'Depok',
                'name' => 'Universitas Indonesia (UI) - Kampus Depok',
                'short_name' => 'UI',
                'address' => 'Jl. Margonda Raya, Pondok Cina, Kecamatan Beji, Kota Depok, Jawa Barat 16424',
                'latitude' => -6.365361,
                'longitude' => 106.828556,
            ],
            [
                'city' => 'Depok',
                'name' => 'Politeknik Negeri Jakarta (PNJ)',
                'short_name' => 'PNJ',
                'address' => 'Jl. Prof. Dr. G.A. Siwabessy, Kampus UI Depok, Jawa Barat 16425',
                'latitude' => -6.362145,
                'longitude' => 106.825820,
            ],
            [
                'city' => 'Depok',
                'name' => 'Universitas Gunadarma - Kampus D Margonda',
                'short_name' => 'Gunadarma',
                'address' => 'Jl. Margonda Raya No. 100, Pondok Cina, Kecamatan Beji, Kota Depok 16424',
                'latitude' => -6.368814,
                'longitude' => 106.833215,
            ],
            [
                'city' => 'Depok',
                'name' => 'Universitas Pancasila',
                'short_name' => 'UP',
                'address' => 'Jl. Srengseng Sawah, Jagakarsa / Perbatasan Depok 12640',
                'latitude' => -6.339188,
                'longitude' => 106.834457,
            ],

            // Jakarta Selatan
            [
                'city' => 'Jakarta Selatan',
                'name' => 'Universitas Bakrie',
                'short_name' => 'Bakrie',
                'address' => 'Kawasan Rasuna Epicentrum, Jl. H.R. Rasuna Said, Kuningan, Jakarta Selatan 12920',
                'latitude' => -6.219800,
                'longitude' => 106.834100,
            ],
            [
                'city' => 'Jakarta Selatan',
                'name' => 'Universitas Al-Azhar Indonesia (UAI)',
                'short_name' => 'UAI',
                'address' => 'Jl. Sisingamangaraja No. 2, Kebayoran Baru, Jakarta Selatan 12110',
                'latitude' => -6.234600,
                'longitude' => 106.799300,
            ],
            [
                'city' => 'Jakarta Selatan',
                'name' => 'Universitas Pertamina',
                'short_name' => 'UPER',
                'address' => 'Jl. Teuku Nyak Arief, Simprug, Kebayoran Lama, Jakarta Selatan 12220',
                'latitude' => -6.229400,
                'longitude' => 106.786500,
            ],

            // Jakarta Timur
            [
                'city' => 'Jakarta Timur',
                'name' => 'Universitas Negeri Jakarta (UNJ) - Kampus Rawamangun',
                'short_name' => 'UNJ',
                'address' => 'Jl. Rawamangun Muka No. 1, Pulo Gadung, Jakarta Timur 13220',
                'latitude' => -6.194700,
                'longitude' => 106.879400,
            ],
            [
                'city' => 'Jakarta Timur',
                'name' => 'Universitas Kristen Indonesia (UKI) - Kampus Cawang',
                'short_name' => 'UKI',
                'address' => 'Jl. Mayjen Sutoyo No. 2, Cawang, Kramat Jati, Jakarta Timur 13630',
                'latitude' => -6.252000,
                'longitude' => 106.872200,
            ],

            // Jakarta Barat
            [
                'city' => 'Jakarta Barat',
                'name' => 'Universitas Trisakti - Kampus Grogol',
                'short_name' => 'Trisakti',
                'address' => 'Jl. Kyai Tapa No. 1, Grogol Petamburan, Jakarta Barat 11440',
                'latitude' => -6.167200,
                'longitude' => 106.790400,
            ],
            [
                'city' => 'Jakarta Barat',
                'name' => 'Universitas Tarumanagara (UNTAR)',
                'short_name' => 'UNTAR',
                'address' => 'Jl. Letjen S. Parman No. 1, Tomang, Grogol Petamburan, Jakarta Barat 11440',
                'latitude' => -6.168500,
                'longitude' => 106.788500,
            ],
            [
                'city' => 'Jakarta Barat',
                'name' => 'BINUS University - Kampus Anggrek',
                'short_name' => 'BINUS Anggrek',
                'address' => 'Jl. Kebon Jeruk Raya No. 27, Kebon Jeruk, Jakarta Barat 11530',
                'latitude' => -6.201800,
                'longitude' => 106.781800,
            ],

            // Bogor
            [
                'city' => 'Bogor',
                'name' => 'IPB University - Kampus Dramaga',
                'short_name' => 'IPB Dramaga',
                'address' => 'Jl. Raya Dramaga, Babakan, Kecamatan Dramaga, Kabupaten Bogor 16680',
                'latitude' => -6.558300,
                'longitude' => 106.726400,
            ],
            [
                'city' => 'Bogor',
                'name' => 'Universitas Pakuan (UNPAK)',
                'short_name' => 'UNPAK',
                'address' => 'Jl. Pakuan No. 1, Tegallega, Kecamatan Bogor Tengah, Kota Bogor 16129',
                'latitude' => -6.600600,
                'longitude' => 106.809400,
            ],
            [
                'city' => 'Bogor',
                'name' => 'Universitas Ibn Khaldun (UIKA) Bogor',
                'short_name' => 'UIKA',
                'address' => 'Jl. KH. Sholeh Iskandar KM. 2, Kedungbadak, Tanah Sereal, Kota Bogor 16162',
                'latitude' => -6.564500,
                'longitude' => 106.786000,
            ],

            // Bandung
            [
                'city' => 'Bandung',
                'name' => 'Institut Teknologi Bandung (ITB) - Kampus Ganesha',
                'short_name' => 'ITB',
                'address' => 'Jl. Ganesa No. 10, Lb. Siliwangi, Kecamatan Coblong, Kota Bandung 40132',
                'latitude' => -6.891500,
                'longitude' => 107.610700,
            ],
            [
                'city' => 'Bandung',
                'name' => 'Universitas Padjadjaran (UNPAD) - Kampus Dipati Ukur',
                'short_name' => 'UNPAD Dipati Ukur',
                'address' => 'Jl. Dipati Ukur No. 35, Lebakgede, Kecamatan Coblong, Kota Bandung 40132',
                'latitude' => -6.893200,
                'longitude' => 107.617800,
            ],
            [
                'city' => 'Bandung',
                'name' => 'Telkom University',
                'short_name' => 'Tel-U',
                'address' => 'Jl. Telekomunikasi No. 1, Terusan Buahbatu, Sukapura, Dayeuhkolot, Bandung 40257',
                'latitude' => -6.974000,
                'longitude' => 107.630300,
            ],
            [
                'city' => 'Bandung',
                'name' => 'Universitas Katolik Parahyangan (UNPAR)',
                'short_name' => 'UNPAR',
                'address' => 'Jl. Ciumbuleuit No. 94, Hegarmanah, Cidadap, Kota Bandung 40141',
                'latitude' => -6.873500,
                'longitude' => 107.604500,
            ],

            // Tangerang Selatan
            [
                'city' => 'Tangerang Selatan',
                'name' => 'Universitas Pamulang (UNPAM)',
                'short_name' => 'UNPAM',
                'address' => 'Jl. Surya Kencana No. 1, Pamulang Barat, Kota Tangerang Selatan 15417',
                'latitude' => -6.345800,
                'longitude' => 106.738000,
            ],
            [
                'city' => 'Tangerang Selatan',
                'name' => 'Universitas Multimedia Nusantara (UMN)',
                'short_name' => 'UMN',
                'address' => 'Jl. Scientia Boulevard, Gading Serpong, Kelapa Dua, Tangerang 15810',
                'latitude' => -6.257000,
                'longitude' => 106.618300,
            ],

            // Yogyakarta
            [
                'city' => 'Yogyakarta',
                'name' => 'Universitas Gadjah Mada (UGM)',
                'short_name' => 'UGM',
                'address' => 'Bulaksumur, Caturtunggal, Kecamatan Depok, Kabupaten Sleman, D.I. Yogyakarta 55281',
                'latitude' => -7.771300,
                'longitude' => 110.377500,
            ],
            [
                'city' => 'Yogyakarta',
                'name' => 'Universitas Negeri Yogyakarta (UNY)',
                'short_name' => 'UNY',
                'address' => 'Jl. Colombo No. 1, Karang Malang, Caturtunggal, Kabupaten Sleman, D.I. Yogyakarta 55281',
                'latitude' => -7.773500,
                'longitude' => 110.386000,
            ],
            [
                'city' => 'Yogyakarta',
                'name' => 'UIN Sunan Kalijaga Yogyakarta',
                'short_name' => 'UIN Yogya',
                'address' => 'Jl. Marsda Adisucipto, Caturtunggal, Sleman, D.I. Yogyakarta 55281',
                'latitude' => -7.785000,
                'longitude' => 110.395000,
            ],

            // Surabaya
            [
                'city' => 'Surabaya',
                'name' => 'Institut Teknologi Sepuluh Nopember (ITS)',
                'short_name' => 'ITS',
                'address' => 'Jl. Raya ITS, Sukolilo, Kota Surabaya, Jawa Timur 60111',
                'latitude' => -7.282400,
                'longitude' => 112.794900,
            ],
            [
                'city' => 'Surabaya',
                'name' => 'Universitas Airlangga (UNAIR) - Kampus B',
                'short_name' => 'UNAIR Kampus B',
                'address' => 'Jl. Airlangga No. 4 - 6, Gubeng, Kota Surabaya, Jawa Timur 60286',
                'latitude' => -7.271800,
                'longitude' => 112.758400,
            ],

            // Semarang
            [
                'city' => 'Semarang',
                'name' => 'Universitas Diponegoro (UNDIP) - Kampus Tembalang',
                'short_name' => 'UNDIP Tembalang',
                'address' => 'Jl. Prof. Sudarto, Tembalang, Kota Semarang, Jawa Tengah 50275',
                'latitude' => -7.050500,
                'longitude' => 110.439000,
            ],

            // Malang
            [
                'city' => 'Malang',
                'name' => 'Universitas Brawijaya (UB)',
                'short_name' => 'UB',
                'address' => 'Jl. Veteran, Ketawanggede, Lowokwaru, Kota Malang, Jawa Timur 65145',
                'latitude' => -7.952600,
                'longitude' => 112.614400,
            ],
        ];

        foreach ($campuses as $data) {
            Campus::query()->updateOrCreate(
                [
                    'city' => $data['city'],
                    'name' => $data['name'],
                ],
                [
                    'short_name' => $data['short_name'],
                    'address' => $data['address'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'is_active' => true,
                ]
            );
        }
    }
}
