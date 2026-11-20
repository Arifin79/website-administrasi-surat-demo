<?php

namespace App\Services;

use App\Data\SuratMasukData;
use App\Data\SuratKeluarData;
use App\Data\UserData;
use App\Data\TemplateData;

class DummyDataService
{
    // ─── Session keys ────────────────────────────────────────────────────────
    private const KEY_MASUK   = 'dummy.surat_masuk';
    private const KEY_KELUAR  = 'dummy.surat_keluar';
    private const KEY_USERS   = 'dummy.users';
    private const KEY_TMPL    = 'dummy.templates';

    // =========================================================================
    // SURAT MASUK
    // =========================================================================

    public static function getSuratMasuk(): array
    {
        if (! session()->has(self::KEY_MASUK)) {
            session([self::KEY_MASUK => self::defaultSuratMasuk()]);
        }

        return session(self::KEY_MASUK, []);
    }

    public static function getSuratMasukDTOs(): array
    {
        return array_map(fn($r) => SuratMasukData::fromArray($r), self::getSuratMasuk());
    }

    public static function findSuratMasuk(int $id): ?array
    {
        foreach (self::getSuratMasuk() as $r) {
            if ($r['id'] === $id) return $r;
        }

        return null;
    }

    public static function addSuratMasuk(array $data): void
    {
        $records = self::getSuratMasuk();
        $data['id']           = count($records) + 1 + rand(10, 99);
        $data['nomor_agenda'] = 'AG-' . date('Y') . '-' . str_pad(count($records) + 1, 4, '0', STR_PAD_LEFT);
        $records[]            = $data;
        session([self::KEY_MASUK => $records]);
    }

    public static function updateSuratMasuk(int $id, array $data): void
    {
        $records = array_map(
            fn($r) => $r['id'] === $id ? array_merge($r, $data) : $r,
            self::getSuratMasuk()
        );
        session([self::KEY_MASUK => $records]);
    }

    public static function deleteSuratMasuk(int $id): void
    {
        $records = array_values(array_filter(self::getSuratMasuk(), fn($r) => $r['id'] !== $id));
        session([self::KEY_MASUK => $records]);
    }

    // =========================================================================
    // SURAT KELUAR
    // =========================================================================

    public static function getSuratKeluar(): array
    {
        if (! session()->has(self::KEY_KELUAR)) {
            session([self::KEY_KELUAR => self::defaultSuratKeluar()]);
        }

        return session(self::KEY_KELUAR, []);
    }

    public static function getSuratKeluarDTOs(): array
    {
        return array_map(fn($r) => SuratKeluarData::fromArray($r), self::getSuratKeluar());
    }

    public static function findSuratKeluar(int $id): ?array
    {
        foreach (self::getSuratKeluar() as $r) {
            if ($r['id'] === $id) return $r;
        }

        return null;
    }

    public static function nextNomorSurat(): string
    {
        $year    = date('Y');
        $bulanRomawi = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][(int) date('n')];
        $count   = count(array_filter(
            self::getSuratKeluar(),
            fn($r) => str_contains($r['tanggal_surat'], $year)
        )) + 1;

        return str_pad($count, 3, '0', STR_PAD_LEFT) . '/DRB/' . $bulanRomawi . '/' . $year;
    }

    public static function addSuratKeluar(array $data): void
    {
        $records   = self::getSuratKeluar();
        $data['id'] = count($records) + 1 + rand(10, 99);
        $records[]  = $data;
        session([self::KEY_KELUAR => $records]);
    }

    public static function updateSuratKeluar(int $id, array $data): void
    {
        $records = array_map(
            fn($r) => $r['id'] === $id ? array_merge($r, $data) : $r,
            self::getSuratKeluar()
        );
        session([self::KEY_KELUAR => $records]);
    }

    public static function deleteSuratKeluar(int $id): void
    {
        $records = array_values(array_filter(self::getSuratKeluar(), fn($r) => $r['id'] !== $id));
        session([self::KEY_KELUAR => $records]);
    }

    // =========================================================================
    // USERS
    // =========================================================================

    public static function getUsers(): array
    {
        if (! session()->has(self::KEY_USERS)) {
            session([self::KEY_USERS => self::defaultUsers()]);
        }

        return session(self::KEY_USERS, []);
    }

    public static function getUserDTOs(): array
    {
        return array_map(fn($u) => UserData::fromArray($u), self::getUsers());
    }

    public static function addUser(array $data): void
    {
        $users   = self::getUsers();
        $data['id']         = count($users) + 1 + rand(5, 20);
        $data['created_at'] = date('Y-m-d');
        $users[]            = $data;
        session([self::KEY_USERS => $users]);
    }

    public static function deleteUser(string $username): void
    {
        $users = array_values(array_filter(self::getUsers(), fn($u) => $u['username'] !== $username));
        session([self::KEY_USERS => $users]);
    }

    public static function usernameExists(string $username): bool
    {
        return (bool) array_filter(self::getUsers(), fn($u) => $u['username'] === $username);
    }

    // =========================================================================
    // TEMPLATES
    // =========================================================================

    public static function getTemplates(): array
    {
        if (! session()->has(self::KEY_TMPL)) {
            session([self::KEY_TMPL => self::defaultTemplates()]);
        }

        return session(self::KEY_TMPL, []);
    }

    public static function getTemplateDTOs(): array
    {
        return array_map(fn($t) => TemplateData::fromArray($t), self::getTemplates());
    }

    public static function findTemplate(int $id): ?array
    {
        foreach (self::getTemplates() as $t) {
            if ($t['id'] === $id) return $t;
        }

        return null;
    }

    public static function addTemplate(array $data): void
    {
        $templates   = self::getTemplates();
        $data['id']         = count($templates) + 1 + rand(5, 20);
        $data['created_at'] = date('Y-m-d');
        $templates[]        = $data;
        session([self::KEY_TMPL => $templates]);
    }

    public static function deleteTemplate(int $id): void
    {
        $templates = array_values(array_filter(self::getTemplates(), fn($t) => $t['id'] !== $id));
        session([self::KEY_TMPL => $templates]);
    }

    // =========================================================================
    // DASHBOARD AGGREGATES
    // =========================================================================

    public static function dashboardStats(): array
    {
        $masuk  = self::getSuratMasuk();
        $keluar = self::getSuratKeluar();

        return [
            'total_masuk'    => count($masuk),
            'total_keluar'   => count($keluar),
            'total_arsip'    => count($masuk) + count($keluar),
            'surat_diproses' => count(array_filter($masuk, fn($r) => $r['status'] === 'Diproses')),
        ];
    }

    public static function recentActivity(): array
    {
        $masuk  = array_map(fn($r) => array_merge($r, ['jenis' => 'masuk']),  self::getSuratMasuk());
        $keluar = array_map(fn($r) => array_merge($r, ['jenis' => 'keluar']), self::getSuratKeluar());
        $all    = array_merge($masuk, $keluar);

        usort($all, fn($a, $b) => strcmp($b['tanggal_surat'], $a['tanggal_surat']));

        return array_slice($all, 0, 5);
    }

    public static function monthlyStats(): array
    {
        $masuk  = count(self::getSuratMasuk());
        $keluar = count(self::getSuratKeluar());

        return [
            ['bulan' => 'Jun', 'masuk' => 3,      'keluar' => 2],
            ['bulan' => 'Jul', 'masuk' => 5,      'keluar' => 3],
            ['bulan' => 'Agu', 'masuk' => 4,      'keluar' => 5],
            ['bulan' => 'Sep', 'masuk' => 7,      'keluar' => 4],
            ['bulan' => 'Okt', 'masuk' => 6,      'keluar' => 7],
            ['bulan' => 'Nov', 'masuk' => $masuk, 'keluar' => $keluar],
        ];
    }

    // =========================================================================
    // DEFAULT SEED DATA
    // =========================================================================

    private static function defaultSuratMasuk(): array
    {
        return [
            [
                'id' => 1, 'nomor_agenda' => 'AG-2026-0001',
                'nomor_surat_asal' => '014/MSJ/X/2026', 'tanggal_surat' => '2026-10-05',
                'tanggal_diterima' => '2026-10-06', 'pengirim' => 'PT Mitra Sejahtera Jaya',
                'perihal' => 'Permohonan Kerja Sama Pengadaan Material', 'status' => 'Diterima', 'file_path' => null,
            ],
            [
                'id' => 2, 'nomor_agenda' => 'AG-2026-0002',
                'nomor_surat_asal' => '018/UND/DISHUB/X/2026', 'tanggal_surat' => '2026-10-12',
                'tanggal_diterima' => '2026-10-13', 'pengirim' => 'Dinas Perhubungan Provinsi DKI Jakarta',
                'perihal' => 'Undangan Rapat Koordinasi Pengerukan Alur Pelayaran', 'status' => 'Diproses', 'file_path' => null,
            ],
            [
                'id' => 3, 'nomor_agenda' => 'AG-2026-0003',
                'nomor_surat_asal' => '097/KEMENHUB/IX/2026', 'tanggal_surat' => '2026-10-20',
                'tanggal_diterima' => '2026-10-21', 'pengirim' => 'Kementerian Perhubungan RI',
                'perihal' => 'Pemberitahuan Perpanjangan Izin Operasional Kapal', 'status' => 'Selesai', 'file_path' => null,
            ],
            [
                'id' => 4, 'nomor_agenda' => 'AG-2026-0004',
                'nomor_surat_asal' => '045/INSA/XI/2026', 'tanggal_surat' => '2026-11-01',
                'tanggal_diterima' => '2026-11-02', 'pengirim' => 'DPD INSA Jakarta',
                'perihal' => 'Pemberitahuan Jadwal Pertemuan Anggota INSA', 'status' => 'Diterima', 'file_path' => null,
            ],
            [
                'id' => 5, 'nomor_agenda' => 'AG-2026-0005',
                'nomor_surat_asal' => '102/BKI/XI/2026', 'tanggal_surat' => '2026-11-10',
                'tanggal_diterima' => '2026-11-11', 'pengirim' => 'PT Biro Klasifikasi Indonesia',
                'perihal' => 'Jadwal Survei Tahunan Kelas Kapal KM Duri Jaya I', 'status' => 'Diproses', 'file_path' => null,
            ],
        ];
    }

    private static function defaultSuratKeluar(): array
    {
        return [
            [
                'id' => 1, 'nomor_surat' => '001/DRB/X/2026', 'tanggal_surat' => '2026-10-15',
                'tujuan' => 'PT Mitra Sejahtera Jaya', 'perihal' => 'Balasan Permohonan Kerja Sama',
                'jenis_pembuatan' => 'Template Generator', 'status' => 'Terkirim',
                'template_id' => 1, 'letter_body_html' => null, 'file_path' => null,
            ],
            [
                'id' => 2, 'nomor_surat' => '002/DRB/X/2026', 'tanggal_surat' => '2026-10-20',
                'tujuan' => 'Dinas Perhubungan Provinsi DKI Jakarta', 'perihal' => 'Konfirmasi Kehadiran Rapat Koordinasi',
                'jenis_pembuatan' => 'Template Generator', 'status' => 'Terkirim',
                'template_id' => 2, 'letter_body_html' => null, 'file_path' => null,
            ],
            [
                'id' => 3, 'nomor_surat' => '003/DRB/XI/2026', 'tanggal_surat' => '2026-11-05',
                'tujuan' => 'PT Pelindo II (Persero)', 'perihal' => 'Permohonan Pemakaian Dermaga Sementara',
                'jenis_pembuatan' => 'Manual (Upload Scan)', 'status' => 'Terkirim',
                'template_id' => null, 'letter_body_html' => null, 'file_path' => null,
            ],
            [
                'id' => 4, 'nomor_surat' => '004/DRB/XI/2026', 'tanggal_surat' => '2026-11-14',
                'tujuan' => 'PT Biro Klasifikasi Indonesia', 'perihal' => 'Penjadwalan Ulang Survei Kelas KM Duri Jaya I',
                'jenis_pembuatan' => 'Template Generator', 'status' => 'Draft',
                'template_id' => 1, 'letter_body_html' => null, 'file_path' => null,
            ],
        ];
    }

    private static function defaultUsers(): array
    {
        return [
            [
                'id' => 1, 'name' => 'Administrator', 'username' => 'admin',
                'role' => 'admin', 'password' => 'admin123', 'created_at' => '2026-01-01',
            ],
            [
                'id' => 2, 'name' => 'Siti Rahayu', 'username' => 'siti.rahayu',
                'role' => 'staf', 'password' => 'staf123', 'created_at' => '2026-02-15',
            ],
            [
                'id' => 3, 'name' => 'Budi Santoso', 'username' => 'budi.santoso',
                'role' => 'staf', 'password' => 'staf123', 'created_at' => '2026-03-10',
            ],
        ];
    }

    private static function defaultTemplates(): array
    {
        return [
            [
                'id' => 1, 'template_name' => 'Surat Balasan Resmi', 'template_code' => 'TMPL-BALASAN',
                'default_subject' => 'Balasan atas Surat Nomor {{nomor_surat_asal}}',
                'body_template' => "Menindaklanjuti surat Saudara nomor {{nomor_surat_asal}} perihal {{perihal}},\n\nBersama ini kami sampaikan bahwa PT Duri Rejang Berseri menyatakan kesediaan untuk {{isi_surat}}.\n\nDemikian surat ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.",
                'created_by' => 'Administrator', 'created_at' => '2026-01-10',
            ],
            [
                'id' => 2, 'template_name' => 'Surat Konfirmasi Kehadiran', 'template_code' => 'TMPL-KONFIRMASI',
                'default_subject' => 'Konfirmasi Kehadiran {{perihal}}',
                'body_template' => "Menanggapi undangan yang telah kami terima, dengan hormat kami beritahukan bahwa perwakilan PT Duri Rejang Berseri akan hadir pada acara tersebut.\n\n{{isi_surat}}\n\nDemikian konfirmasi ini kami sampaikan. Atas undangannya kami mengucapkan terima kasih.",
                'created_by' => 'Administrator', 'created_at' => '2026-01-10',
            ],
            [
                'id' => 3, 'template_name' => 'Surat Pemberitahuan Internal', 'template_code' => 'TMPL-PEMBERITAHUAN',
                'default_subject' => 'Pemberitahuan {{perihal}}',
                'body_template' => "Dengan ini kami sampaikan pemberitahuan bahwa {{isi_surat}}.\n\nHal-hal yang perlu diperhatikan dalam pelaksanaannya kami serahkan kepada pihak yang bersangkutan untuk ditindaklanjuti sebagaimana mestinya.\n\nDemikian pemberitahuan ini kami sampaikan untuk dapat dimaklumi.",
                'created_by' => 'Administrator', 'created_at' => '2026-02-20',
            ],
        ];
    }
}
