<meta charset="utf-8" />
<meta
    content="width=device-width, initial-scale=1.0"
    name="viewport"
/>

<title>
    {{ filled($title ?? null) ? $title . ' - ' . config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link
    href="/favicon.ico"
    rel="icon"
    sizes="any"
>
<link
    href="/favicon.svg"
    rel="icon"
    type="image/svg+xml"
>
<link
    href="/apple-touch-icon.png"
    rel="apple-touch-icon"
>

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
