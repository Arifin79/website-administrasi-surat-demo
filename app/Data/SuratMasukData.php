<?php

namespace App\Data;

readonly class SuratMasukData
{
    public function __construct(
        public int     $id,
        public string  $nomorAgenda,
        public string  $nomorSuratAsal,
        public string  $tanggalSurat,
        public string  $tanggalDiterima,
        public string  $pengirim,
        public string  $perihal,
        public string  $status,
        public ?string $filePath = null,
    ) {}

    public static function fromArray(array $d): self
    {
        return new self(
            id:              $d['id'],
            nomorAgenda:     $d['nomor_agenda'],
            nomorSuratAsal:  $d['nomor_surat_asal'],
            tanggalSurat:    $d['tanggal_surat'],
            tanggalDiterima: $d['tanggal_diterima'],
            pengirim:        $d['pengirim'],
            perihal:         $d['perihal'],
            status:          $d['status'],
            filePath:        $d['file_path'] ?? null,
        );
    }

    public static function fromModel(\App\Models\IncomingLetter $m): self
    {
        return new self(
            id:              $m->id,
            nomorAgenda:     $m->agenda_number,
            nomorSuratAsal:  $m->reference_number,
            tanggalSurat:    $m->letter_date->format('Y-m-d'),
            tanggalDiterima: $m->received_date->format('Y-m-d'),
            pengirim:        $m->sender,
            perihal:         $m->subject,
            status:          $m->status,
            filePath:        $m->file_path,
        );
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'Diproses' => 'orange',
            'Selesai'  => 'green',
            default    => 'blue',
        };
    }

    public function tanggalSuratFormatted(): string
    {
        return self::formatTanggal($this->tanggalSurat);
    }

    public function tanggalDiterimaFormatted(): string
    {
        return self::formatTanggal($this->tanggalDiterima);
    }

    private static function formatTanggal(string $date): string
    {
        return date('d M Y', strtotime($date));
    }
}
