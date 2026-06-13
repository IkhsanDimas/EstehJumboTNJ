@props([
    'src',                         // path tanpa extension, relatif ke /public, mis. "images/es-teh-jumbo"
    'alt' => '',
    'class' => '',
    'imgClass' => '',
    'loading' => 'lazy',           // 'lazy' (default) atau 'eager' untuk gambar di atas fold
    'sizes' => null,               // optional, untuk responsive
    'transparent' => false,        // true = pakai PNG/alpha-WebP (background sudah dihapus)
])

@php
    $hasWebp = false;
    $webpUrl = '';
    $fallbackUrl = '';

    // Clean leading slash for consistency
    $cleanSrc = ltrim($src, '/');

    // Strip extension if it exists to allow dynamic extensions matching
    $extension = pathinfo($cleanSrc, PATHINFO_EXTENSION);
    if (in_array(strtolower($extension), ['png', 'jpg', 'jpeg', 'webp'])) {
        $cleanSrc = substr($cleanSrc, 0, -strlen($extension) - 1);
    }

    // 1. Physically check if the WebP file exists on disk
    if ($transparent) {
        $webpFile = $cleanSrc . '.alpha.webp';
        $webpPhysical = public_path($webpFile);
        if (file_exists($webpPhysical)) {
            $webpUrl = asset($webpFile);
            $hasWebp = true;
        }
    } else {
        $webpFile = $cleanSrc . '.webp';
        $webpPhysical = public_path($webpFile);
        if (file_exists($webpPhysical)) {
            $webpUrl = asset($webpFile);
            $hasWebp = true;
        }
    }

    // 2. Loop through extensions to find the actual fallback file existing on disk
    $possibleExtensions = $transparent 
        ? ['.png', '.alpha.webp', '.jpg', '.jpeg', '.webp'] 
        : ['.jpg', '.jpeg', '.webp', '.png'];
        
    foreach ($possibleExtensions as $ext) {
        $testFile = $cleanSrc . $ext;
        if (file_exists(public_path($testFile))) {
            $fallbackUrl = asset($testFile);
            break;
        }
    }

    // Default fallback in case nothing is found on disk
    if (empty($fallbackUrl)) {
        // Fallback to a guaranteed existing premium image of Es Teh Jumbo original
        $originalWebp = 'images/es-teh-original.alpha.webp';
        if (file_exists(public_path($originalWebp))) {
            $fallbackUrl = asset($originalWebp);
            $webpUrl = asset($originalWebp);
            $hasWebp = true;
        } else {
            $fallbackUrl = asset($cleanSrc . ($transparent ? '.png' : '.jpg'));
        }
    }
@endphp

<picture {{ $attributes->merge(['class' => $class]) }}>
    @if($hasWebp)
        <source srcset="{{ $webpUrl }}" type="image/webp">
    @endif
    <img
        src="{{ $fallbackUrl }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        decoding="async"
        @if($sizes) sizes="{{ $sizes }}" @endif
        class="{{ $imgClass }}">
</picture>
