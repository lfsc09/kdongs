@props([
    'id' => uniqid(),
])

<svg
    {{ $attributes }}
    fill="none"
>
    <defs>
        <pattern
            height="8"
            id="pattern-{{ $id }}"
            patternUnits="userSpaceOnUse"
            width="8"
            x="0"
            y="0"
        >
            <path
                d="M-1 5L5 -1M3 9L8.5 3.5"
                stroke-width="0.5"
            ></path>
        </pattern>
    </defs>
    <rect
        fill="url(#pattern-{{ $id }})"
        height="100%"
        stroke="none"
        width="100%"
    ></rect>
</svg>
