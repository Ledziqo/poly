@props(['label', 'value', 'tone' => 'neutral'])

<div class="stat">
    <span>{{ $label }}</span>
    <strong class="{{ $tone }}">{{ $value }}</strong>
</div>
