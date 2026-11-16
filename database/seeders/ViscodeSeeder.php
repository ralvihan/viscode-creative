<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ViscodeSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed 8 Anggota Tim Viscode Creative + Role RBAC
        $team = [
            ['name' => 'Raihan Alvian', 'email' => 'raihan@viscode.id', 'role' => 'ceo'],
            ['name' => 'Sutan Raja', 'email' => 'sutan@viscode.id', 'role' => 'finance'],
            ['name' => 'Feni Dwi Lestari', 'email' => 'feni@viscode.id', 'role' => 'marketing'],
            ['name' => 'Ziddan Azzahra', 'email' => 'ziddan@viscode.id', 'role' => 'super_admin'],
            ['name' => 'Ahmad Naufal Akbar', 'email' => 'naufal@viscode.id', 'role' => 'graphic_designer'],
            ['name' => 'Muhammad Fikri AlFauzan', 'email' => 'fikri@viscode.id', 'role' => 'ui_ux_designer'],
            ['name' => 'Yoga K', 'email' => 'yoga@viscode.id', 'role' => 'sales'],
            ['name' => 'Morren', 'email' => 'morren@viscode.id', 'role' => 'qc_admin'],
        ];

        foreach ($team as $member) {
            User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make('viscode2026'),
                    'role' => $member['role'],
                ]
            );
        }

        // 2. Seed Katalog Layanan & Paket Bundling (Sesuai Slide Presentasi)
        $services = [
            // Web Development
            ['name' => 'Landing Page Sederhana', 'category' => 'web_development', 'base_price' => 150000, 'description' => '1 halaman responsif, desain modern, integrasi WhatsApp.'],
            ['name' => 'Website Profil / UMKM', 'category' => 'web_development', 'base_price' => 350000, 'description' => 'Multi-halaman (Home, About, Layanan, Kontak), SEO dasar.'],
            ['name' => 'Website Custom', 'category' => 'web_development', 'base_price' => 500000, 'description' => 'Fitur khusus sesuai kebutuhan bisnis atau sistem informasi sederhana.'],

            // Graphic Design & Event
            ['name' => 'Desain Poster / Flyer', 'category' => 'graphic_design', 'base_price' => 25000, 'description' => 'Desain publikasi ukuran A4/A3 siap cetak maupun digital.'],
            ['name' => 'Paket 5 Konten Sosial Media', 'category' => 'graphic_design', 'base_price' => 75000, 'description' => '5 desain feed/story Instagram yang konsisten dengan brand.'],
            ['name' => 'Desain ID Card Panitia', 'category' => 'id_card_event', 'base_price' => 10000, 'description' => 'Desain kartu identitas acara/panitia siap cetak.'],

            // Paket Bundling
            ['name' => 'Paket UMKM Digital', 'category' => 'bundling', 'base_price' => 450000, 'description' => 'Landing page sederhana + Logo sederhana + 3 desain konten sosial.'],
            ['name' => 'Paket Organisasi', 'category' => 'bundling', 'base_price' => 175000, 'description' => 'Poster kegiatan + Desain ID card + Template pengumuman.'],
            ['name' => 'Paket Event Starter', 'category' => 'bundling', 'base_price' => 650000, 'description' => 'Landing page acara + Poster digital + ID card panitia.'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                $service
            );
        }
    }
}
