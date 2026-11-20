<?php

namespace App\Data;

readonly class TemplateData
{
    public function __construct(
        public int    $id,
        public string $templateName,
        public string $templateCode,
        public string $defaultSubject,
        public string $bodyTemplate,
        public string $createdBy,
        public string $createdAt,
    ) {}

    public static function fromArray(array $d): self
    {
        return new self(
            id:             $d['id'],
            templateName:   $d['template_name'],
            templateCode:   $d['template_code'],
            defaultSubject: $d['default_subject'],
            bodyTemplate:   $d['body_template'],
            createdBy:      $d['created_by'],
            createdAt:      $d['created_at'],
        );
    }

    public static function fromModel(\App\Models\LetterTemplate $m): self
    {
        return new self(
            id:             $m->id,
            templateName:   $m->template_name,
            templateCode:   $m->template_code,
            defaultSubject: $m->default_subject ?? '',
            bodyTemplate:   $m->body_template,
            createdBy:      $m->creator?->name ?? '',
            createdAt:      $m->created_at?->format('Y-m-d H:i:s') ?? '',
        );
    }

    public function createdAtFormatted(): string
    {
        return date('d M Y', strtotime($this->createdAt));
    }
}
