@props([
    'points' => [],          // array of numbers
    'color' => '#696CFF',    // stroke colour
    'fill' => null,          // area fill (defaults to color)
    'height' => 34,
    'width' => 120,
])
@php
    $pts = array_values(array_map('floatval', $points ?: [0, 0]));
    if (count($pts) === 1) { $pts[] = $pts[0]; }
    $n = count($pts);
    $min = min($pts); $max = max($pts);
    $range = ($max - $min) ?: 1;
    $w = (float) $width; $h = (float) $height; $pad = 3;
    $stepX = $n > 1 ? ($w / ($n - 1)) : 0;
    $coords = [];
    foreach ($pts as $i => $v) {
        $x = round($i * $stepX, 2);
        $y = round($h - $pad - (($v - $min) / $range) * ($h - 2 * $pad), 2);
        $coords[] = [$x, $y];
    }
    $line = 'M' . implode(' L', array_map(fn ($c) => $c[0] . ',' . $c[1], $coords));
    $area = $line . " L{$w},{$h} L0,{$h} Z";
    $fillColor = $fill ?: $color;
    $id = 'sp' . substr(md5($line . $color), 0, 6);
@endphp
<svg class="lp-spark" viewBox="0 0 {{ $w }} {{ $h }}" preserveAspectRatio="none" {{ $attributes }}>
    <defs>
        <linearGradient id="{{ $id }}" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="{{ $fillColor }}" stop-opacity="0.32"/>
            <stop offset="100%" stop-color="{{ $fillColor }}" stop-opacity="0"/>
        </linearGradient>
    </defs>
    <path class="area" d="{{ $area }}" fill="url(#{{ $id }})"/>
    <path class="line" d="{{ $line }}" stroke="{{ $color }}"/>
</svg>
