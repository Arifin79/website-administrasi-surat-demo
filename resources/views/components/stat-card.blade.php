@props([
    'label',
    'value',
    'hint'  => '',
    'icon'  => '▦',
    'iconBg'   => '#eaf1ff',
    'iconColor' => '#2865d9',
])

<div class="stat">
    <div class="stat-top">
        <small>{{ $label }}</small>
        <div class="stat-icon" style="background:{{ $iconBg }};color:{{ $iconColor }}">
            <span class="material-icons">{{ $icon }}</span>
        </div>
    </div>
    <h2>{{ $value }}</h2>
    @if($hint)
        <div class="hint">{{ $hint }}</div>
    @endif
</div>
