@props(['status'])

@php
    $color = match($status) {
        'Diterima'  => 'blue',
        'Diproses'  => 'orange',
        'Selesai'   => 'green',
        'Terkirim'  => 'green',
        'Draft'     => 'orange',
        'Admin'     => 'blue',
        'admin'     => 'blue',
        'staf'      => 'green',
        default     => 'gray',
    };
@endphp

<span class="badge badge-{{ $color }}">{{ $status }}</span>
