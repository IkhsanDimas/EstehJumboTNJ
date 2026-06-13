@props(['name', 'class' => 'w-full h-full'])

@php
    $nameLower = strtolower($name);
    
    // Default values
    $type = 'pint';
    $scoopGradStart = '#FFFFFF';
    $scoopGradEnd = '#F3E5AB';
    $cupGradStart = '#54B3E0';
    $cupGradEnd = '#1D7FB3';
    $flavorTitle = 'Swedish Vanilj';
    $calories = '240 KCAL';
    $speckles = []; // Array of {cx, cy, r}
    
    if (str_contains($nameLower, 'vanilj') || str_contains($nameLower, 'vanilla')) {
        $type = 'pint';
        $scoopGradStart = '#FFFFFF';
        $scoopGradEnd = '#FDF3E7';
        $cupGradStart = '#54B3E0';
        $cupGradEnd = '#1D7FB3';
        $flavorTitle = 'Swedish Vanilj';
        $calories = '240 KCAL';
    } elseif (str_contains($nameLower, 'cookies')) {
        $type = 'pint';
        $scoopGradStart = '#FDF0D5';
        $scoopGradEnd = '#EED9B3';
        $cupGradStart = '#8B5A2B';
        $cupGradEnd = '#5C3818';
        $flavorTitle = 'Cookies & Kräm';
        $calories = '260 KCAL';
        $speckles = [
            ['cx' => 95, 'cy' => 90, 'r' => 4],
            ['cx' => 110, 'cy' => 75, 'r' => 3],
            ['cx' => 140, 'cy' => 85, 'r' => 5],
            ['cx' => 125, 'cy' => 95, 'r' => 3],
            ['cx' => 150, 'cy' => 105, 'r' => 4],
            ['cx' => 90, 'cy' => 110, 'r' => 3]
        ];
    } elseif (str_contains($nameLower, 'mint')) {
        $type = 'pint';
        $scoopGradStart = '#D1FAE5';
        $scoopGradEnd = '#A7F3D0';
        $cupGradStart = '#10B981';
        $cupGradEnd = '#047857';
        $flavorTitle = 'Mint Choko';
        $calories = '220 KCAL';
        $speckles = [
            ['cx' => 100, 'cy' => 85, 'r' => 4],
            ['cx' => 135, 'cy' => 70, 'r' => 3],
            ['cx' => 145, 'cy' => 90, 'r' => 4],
            ['cx' => 120, 'cy' => 100, 'r' => 3],
            ['cx' => 90, 'cy' => 100, 'r' => 3]
        ];
    } elseif (str_contains($nameLower, 'strawberry')) {
        $type = 'pint';
        $scoopGradStart = '#FCE7F3';
        $scoopGradEnd = '#FBCFE8';
        $cupGradStart = '#EC4899';
        $cupGradEnd = '#BE185D';
        $flavorTitle = 'Jordgubb Swirl';
        $calories = '230 KCAL';
    } elseif (str_contains($nameLower, 'chocolate') || str_contains($nameLower, 'cokelat')) {
        $type = 'pint';
        $scoopGradStart = '#78350F';
        $scoopGradEnd = '#451A03';
        $cupGradStart = '#451A03';
        $cupGradEnd = '#1E0E03';
        $flavorTitle = 'Choklad Trio';
        $calories = '270 KCAL';
    } elseif (str_contains($nameLower, 'sandwich')) {
        $type = 'sandwich';
    } elseif (str_contains($nameLower, 'bar')) {
        $type = 'bar';
    } elseif (str_contains($nameLower, 'bites') || str_contains($nameLower, 'dough')) {
        $type = 'bites';
    } else {
        $type = 'topping';
    }
@endphp

<div class="{{ $class }}">
    @if($type === 'pint')
        <svg viewBox="0 0 240 320" class="w-full h-full drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="scoop-grad-{{ $nameLower }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="{{ $scoopGradStart }}" />
                    <stop offset="100%" stop-color="{{ $scoopGradEnd }}" />
                </linearGradient>
                <linearGradient id="cup-grad-{{ $nameLower }}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="{{ $cupGradStart }}" />
                    <stop offset="100%" stop-color="{{ $cupGradEnd }}" />
                </linearGradient>
                <linearGradient id="brand-text-grad-{{ $nameLower }}" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#1E293B" />
                    <stop offset="100%" stop-color="#0F172A" />
                </linearGradient>
            </defs>

            <!-- Ice Cream Scoops -->
            <g>
                <!-- Shadow -->
                <ellipse cx="120" cy="110" rx="65" ry="55" fill="rgba(0,0,0,0.06)" />
                <!-- Scoop 1 (Back Left) -->
                <circle cx="90" cy="100" r="45" fill="url(#scoop-grad-{{ $nameLower }})" />
                <!-- Scoop 2 (Back Right) -->
                <circle cx="150" cy="100" r="45" fill="url(#scoop-grad-{{ $nameLower }})" />
                <!-- Scoop 3 (Front Center) -->
                <circle cx="120" cy="80" r="50" fill="url(#scoop-grad-{{ $nameLower }})" />
                
                <!-- Speckles/Chips if any -->
                @foreach($speckles as $chip)
                    <circle cx="{{ $chip['cx'] }}" cy="{{ $chip['cy'] }}" r="{{ $chip['r'] }}" fill="#3D261C" />
                @endforeach

                <!-- Highlights -->
                <path d="M 85,65 Q 110,60 120,80" stroke="rgba(255,255,255,0.4)" stroke-width="6" fill="none" stroke-linecap="round" />
                <path d="M 125,70 Q 140,65 155,75" stroke="rgba(255,255,255,0.4)" stroke-width="5" fill="none" stroke-linecap="round" />
                <circle cx="110" cy="70" r="6" fill="rgba(255,255,255,0.6)" />
            </g>
            
            <!-- Cup Lid/Rim shadow -->
            <path d="M 38,125 Q 120,140 202,125 L 202,130 Q 120,145 38,130 Z" fill="rgba(0,0,0,0.08)" />

            <!-- Cup Body -->
            <path d="M 40,125 L 55,290 Q 60,305 75,305 L 165,305 Q 180,305 185,290 L 200,125 Z" fill="url(#cup-grad-{{ $nameLower }})" />

            <!-- Cup Rim (White) -->
            <ellipse cx="120" cy="125" rx="80" ry="12" fill="#FFFFFF" />
            <ellipse cx="120" cy="125" rx="80" ry="12" fill="none" stroke="#E2E8F0" stroke-width="1" />

            <!-- Label White Background -->
            <path d="M 42,145 Q 80,165 120,150 Q 160,135 198,155 L 192,260 Q 120,285 48,260 Z" fill="#FFFFFF" opacity="0.95" />

            <!-- Brand logo -->
            <text x="120" y="195" font-family="'Fredoka', sans-serif" font-weight="900" font-size="28" fill="url(#brand-text-grad-{{ $nameLower }})" text-anchor="middle" letter-spacing="1">N!CK'S</text>
            
            <!-- Subtitle -->
            <text x="120" y="218" font-family="sans-serif" font-weight="800" font-size="9" fill="#E28743" text-anchor="middle" letter-spacing="0.5">SWEDISH STYLE LIGHT ICE CREAM</text>
            <text x="120" y="235" font-family="'Fredoka', sans-serif" font-weight="700" font-size="14" fill="#334155" text-anchor="middle">{{ $flavorTitle }}</text>

            <!-- Calories Badge -->
            <g transform="translate(120, 275)">
                <rect x="-35" y="-12" width="70" height="20" rx="10" fill="#FFEDD5" />
                <text x="0" y="2" font-family="sans-serif" font-weight="900" font-size="9" fill="#EA580C" text-anchor="middle">{{ $calories }}</text>
            </g>
        </svg>
    @elseif($type === 'sandwich')
        <!-- Cookie Sandwich Vector SVG -->
        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Cream filling -->
            <rect x="35" y="75" width="130" height="48" rx="8" fill="#FFFDF2" stroke="#E2E8F0" stroke-width="2" />
            <!-- Cookies -->
            <rect x="30" y="60" width="140" height="20" rx="6" fill="#3A2312" />
            <rect x="30" y="118" width="140" height="20" rx="6" fill="#3A2312" />
            
            <!-- Cookie Details/Dots -->
            <g fill="#1F1008">
                <circle cx="50" cy="70" r="2.5" />
                <circle cx="80" cy="70" r="2.5" />
                <circle cx="110" cy="70" r="2.5" />
                <circle cx="140" cy="70" r="2.5" />
                <circle cx="65" cy="128" r="2.5" />
                <circle cx="95" cy="128" r="2.5" />
                <circle cx="125" cy="128" r="2.5" />
                <circle cx="155" cy="128" r="2.5" />
            </g>
        </svg>
    @elseif($type === 'bar')
        <!-- Popsicle Bar Vector SVG -->
        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="bar-grad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#8B4513" />
                    <stop offset="100%" stop-color="#5C2D0C" />
                </linearGradient>
            </defs>
            <!-- Stick -->
            <path d="M 90,130 L 90,180 Q 90,190 100,190 Q 110,190 110,180 L 110,130 Z" fill="#E8C39E" />
            
            <!-- Ice Cream Bar body -->
            <path d="M 50,40 C 50,20 150,20 150,40 L 150,140 Q 150,150 140,150 L 60,150 Q 50,150 50,140 Z" fill="url(#bar-grad)" />
            
            <!-- Bite mark -->
            <path d="M 125,18 C 135,18 140,32 153,30 C 158,35 158,45 152,48 C 145,55 130,55 125,38 Z" fill="#FDF3E7" />
        </svg>
    @elseif($type === 'bites')
        <!-- Bites/Keto Treats Vector SVG -->
        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
            <ellipse cx="100" cy="140" rx="70" ry="25" fill="#E2E8F0" />
            <!-- Cookie dough bites -->
            <circle cx="75" cy="115" r="22" fill="#EED9B3" stroke="#8B5A2B" stroke-width="2" />
            <circle cx="100" cy="100" r="25" fill="#EED9B3" stroke="#8B5A2B" stroke-width="2" />
            <circle cx="125" cy="115" r="22" fill="#EED9B3" stroke="#8B5A2B" stroke-width="2" />
            
            <!-- Choco chips in bites -->
            <g fill="#3D261C">
                <circle cx="70" cy="110" r="2.5" />
                <circle cx="82" cy="122" r="2" />
                <circle cx="95" cy="95" r="3" />
                <circle cx="105" cy="110" r="2.5" />
                <circle cx="120" cy="112" r="2" />
                <circle cx="132" cy="120" r="3" />
            </g>
        </svg>
    @else
        <!-- Topping Vector SVG (sprinkle shaker/bowl) -->
        <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-md" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Bowl -->
            <path d="M 40,90 L 50,150 Q 55,165 70,165 L 130,165 Q 145,165 150,150 L 160,90 Z" fill="#E2E8F0" />
            <!-- Sprinkles/Topping Heap -->
            <path d="M 45,90 Q 100,60 155,90 Z" fill="#EC4899" />
            <circle cx="80" cy="80" r="4" fill="#3B82F6" />
            <circle cx="100" cy="72" r="4" fill="#10B981" />
            <circle cx="120" cy="80" r="4" fill="#F59E0B" />
        </svg>
    @endif
</div>
