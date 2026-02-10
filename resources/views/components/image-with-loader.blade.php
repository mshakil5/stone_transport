@props(['src', 'alt'])

@php
    $loaderImg = asset('images/Loading_icon.gif');
@endphp

<img {{ $attributes->merge(['src' => $src]) }}
    onerror="this.onerror=null;this.src='{{ $loaderImg }}';"
    onabort="this.onerror=null;this.src='{{ $loaderImg }}';"
>
