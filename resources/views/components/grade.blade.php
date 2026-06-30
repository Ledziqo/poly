@props(['grade'])

@php
    $class = match ($grade) {
        'Strong Entry' => 'grade strong',
        'Good Entry' => 'grade good',
        'Watch' => 'grade watch',
        'Too Late' => 'grade late',
        default => 'grade skip',
    };
@endphp

<span class="{{ $class }}">{{ $grade }}</span>
