@props(['label', 'value'])

<div class="info-row">
    <span class="info-label">{{ $label }}</span>
    <span class="info-value {{ empty($value) ? 'empty' : '' }}">
        {{ !empty($value) ? $value : '—' }}
    </span>
</div>