<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Kebijakan;
use App\Models\Anggota;
use App\Models\Buku;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kebijakan default
        Kebijakan::create([
            'maksimal_waktu_pinjam' => 7,
            'maksimal_jumlah_pinjam' => 3,
            'denda_per_hari' => 5000,
            'batas_denda' => 50000,
            'allow_reservasi' => true,
            'masa_berlaku_anggota' => 365,
            'denda_hilang' => 100000,
            'syarat_anggota' => '1. Memiliki identitas yang valid\n2. Mengisi formulir pendaftaran\n3. Membayar iuran keanggotaan'
        ]);

        // 2. Users (Admin & Petugas)
        User::create([
            'kode_pengguna' => 'ADM001',
            'name' => 'Administrator Sistem',
            'email' => 'admin@perpustakaan.test',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status_akun' => 'Aktif',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Perpustakaan No. 1'
        ]);

        User::create([
            'kode_pengguna' => 'PGW001',
            'name' => 'Budi Santoso',
            'email' => 'petugas@perpustakaan.test',
            'password' => Hash::make('petugas123'),
            'role' => 'petugas',
            'status_akun' => 'Aktif',
            'no_telepon' => '081298765432',
            'alamat' => 'Jl. Petugas No. 10'
        ]);

        // 3. Sample Anggota
        $anggotaData = [
            ['MEM001', 'Ahmad Fauzi', 'L', 'Jakarta', '1995-03-15', 'Jl. Merdeka No. 12', '08111222333', 'KTP', '1234567890123456', 'Mahasiswa'],
            ['MEM002', 'Siti Nurhaliza', 'P', 'Bandung', '1998-07-22', 'Jl. Sudirman No. 45', '08222333444', 'KTP', '2345678901234567', 'Pelajar'],
            ['MEM003', 'Bambang Pamungkas', 'L', 'Surabaya', '1990-11-30', 'Jl. Diponegoro No. 78', '08333444555', 'SIM', '3456789012345678', 'Umum'],
            ['MEM004', 'Dewi Sartika', 'P', 'Yogyakarta', '2000-01-10', 'Jl. Gatot Subroto No. 23', '08444555666', 'KTP', '4567890123456789', 'Mahasiswa'],
            ['MEM005', 'Joko Widodo', 'L', 'Solo', '1985-05-05', 'Jl. Pahlawan No. 56', '08555666777', 'KTP', '5678901234567890', 'Umum'],
        ];

        foreach ($anggotaData as $data) {
            Anggota::create([
                'kode_anggota' => $data[0],
                'nama_anggota' => $data[1],
                'jenis_kelamin' => $data[2],
                'tempat_lahir' => $data[3],
                'tanggal_lahir' => $data[4],
                'alamat' => $data[5],
                'no_telepon' => $data[6],
                'jenis_identitas' => $data[7],
                'nomor_identitas' => $data[8],
                'jenis_anggota' => $data[9],
                'status' => 'Aktif',
                'jumlah_pinjam' => 0
            ]);
        }

        // 4. Sample Buku
        $bukuData = [
            ['BUK001', 'Pemrograman PHP untuk Pemula', 'Non-fiksi', 'Buku', 'Cetak', 'Budi Raharjo', 'Informatika', 2020, '1', 'Pertama', 'Tersedia', 5, '978-623-01-0123-4'],
            ['BUK002', 'Laravel: Framework PHP Terbaik', 'Non-fiksi', 'Buku', 'Cetak', 'Eko Kurniawan', 'Media Kita', 2021, '2', 'Kedua', 'Tersedia', 3, '978-623-01-0456-3'],
            ['BUK003', 'Database Design & Implementation', 'Non-fiksi', 'Buku', 'Cetak', 'Agus Setiawan', 'Andi Publisher', 2019, '3', 'Ketiga', 'Dipinjam', 2, '978-623-01-0789-2'],
            ['BUK004', 'JavaScript Modern', 'Non-fiksi', 'Buku', 'Cetak', 'Rudi Hartono', 'Elex Media', 2022, '1', 'Pertama', 'Tersedia', 4, '978-623-01-1123-3'],
            ['BUK005', 'Machine Learning Fundamentals', 'Non-fiksi', 'Buku', 'Cetak', 'Dian Pratiwi', 'Gramedia', 2023, '1', 'Pertama', 'Tersedia', 2, '978-623-01-1456-2'],
            ['BUK006', 'Web Development dengan React', 'Non-fiksi', 'Buku', 'Cetak', 'Fajar Nugroho', 'Informatika', 2021, '2', 'Kedua', 'Tersedia', 3, '978-623-01-1789-1'],
            ['BUK007', 'Python untuk Data Science', 'Non-fiksi', 'Buku', 'Cetak', 'Sari Dewi', 'Media Kita', 2022, '1', 'Pertama', 'Tersedia', 2, '978-623-01-2123-0'],
            ['BUK008', 'Mobile App Development', 'Non-fiksi', 'Buku', 'Cetak', 'Hendra Wijaya', 'Andi Publisher', 2020, '3', 'Ketiga', 'Rusak', 1, '978-623-01-2456-9'],
            ['BUK009', 'Cyber Security Essentials', 'Non-fiksi', 'Buku', 'Cetak', 'Ahmad Syafii', 'Elex Media', 2023, '1', 'Pertama', 'Tersedia', 3, '978-623-01-2789-8'],
            ['BUK010', 'Cloud Computing dengan AWS', 'Non-fiksi', 'Buku', 'Cetak', 'Rina Melati', 'Gramedia', 2022, '2', 'Kedua', 'Tersedia', 2, '978-623-01-3123-7'],
        ];

        foreach ($bukuData as $data) {
            Buku::create([
                'kode_buku' => $data[0],
                'judul' => $data[1],
                'jenis_bahan_pustaka' => $data[2],
                'jenis_koleksi' => $data[3],
                'jenis_media' => $data[4],
                'pengarang' => $data[5],
                'penerbit' => $data[6],
                'tahun_terbit' => $data[7],
                'cetakan' => $data[8],
                'edisi' => $data[9],
                'status' => $data[10],
                'stok' => $data[11],
                'isbn' => $data[12],
                'deskripsi' => 'Buku tentang ' . $data[1],
                'lokasi_rak' => 'RAK-' . rand(1, 10)
            ]);
        }

        $this->command->info('✅ Database berhasil di-seed!');
    }
}