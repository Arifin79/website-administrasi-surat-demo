<?php

namespace App\Data;

readonly class SuratKeluarData
{
    public function __construct(
        public int     $id,
        public string  $nomorSurat,
        public string  $tanggalSurat,
        public string  $tujuan,
        public string  $perihal,
        public string  $jenisPembuatan,
        public string  $status,
        public ?int    $templateId = null,
        public ?string $letterBodyHtml = null,
        public ?string $filePath = null,
    ) {}

    public static function fromArray(array $d): self
    {
        return new self(
            id:             $d['id'],
            nomorSurat:     $d['nomor_surat'],
            tanggalSurat:   $d['tanggal_surat'],
            tujuan:         $d['tujuan'],
            perihal:        $d['perihal'],
            jenisPembuatan: $d['jenis_pembuatan'],
            status:         $d['status'],
            templateId:     $d['template_id'] ?? null,
            letterBodyHtml: $d['letter_body_html'] ?? null,
            filePath:       $d['file_path'] ?? null,
        );
    }

    public static function fromModel(\App\Models\OutgoingLetter $m): self
    {
        return new self(
            id:             $m->id,
            nomorSurat:     $m->letter_number,
            tanggalSurat:   $m->letter_date->format('Y-m-d'),
            tujuan:         $m->recipient,
            perihal:        $m->subject,
            jenisPembuatan: $m->creation_type,
            status:         $m->status,
            templateId:     $m->template_id,
            letterBodyHtml: $m->letter_body_html,
            filePath:       $m->file_path,
        );
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'Terkirim' => 'green',
            default    => 'orange',
        };
    }

    public function tanggalFormatted(): string
    {
        return date('d M Y', strtotime($this->tanggalSurat));
    }

    public function isTemplate(): bool
    {
        return $this->jenisPembuatan === 'Template Generator';
    }
}
