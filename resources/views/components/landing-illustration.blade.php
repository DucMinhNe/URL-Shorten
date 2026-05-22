{{-- Flat cartoon illustration: tropical scene + phone earning money (by.com.vn vibe) --}}
<svg viewBox="0 0 600 520" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" preserveAspectRatio="xMidYMid meet">
    <defs>
        <linearGradient id="skyGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#FDE68A"/>
            <stop offset="50%" stop-color="#FED7AA"/>
            <stop offset="100%" stop-color="#FCA5A5"/>
        </linearGradient>
        <linearGradient id="seaGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#7DD3FC"/>
            <stop offset="100%" stop-color="#0EA5E9"/>
        </linearGradient>
        <linearGradient id="sandGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#FEF3C7"/>
            <stop offset="100%" stop-color="#FDE68A"/>
        </linearGradient>
        <radialGradient id="sunGlow" cx="50%" cy="50%" r="50%">
            <stop offset="0%" stop-color="#FBBF24" stop-opacity="0.6"/>
            <stop offset="100%" stop-color="#FBBF24" stop-opacity="0"/>
        </radialGradient>
    </defs>

    {{-- Background circle (decorative) --}}
    <circle cx="300" cy="260" r="240" fill="#FFEDD5" opacity="0.6"/>

    {{-- Sun glow --}}
    <circle cx="460" cy="120" r="120" fill="url(#sunGlow)"/>
    {{-- Sun --}}
    <circle cx="460" cy="120" r="42" fill="#FBBF24"/>
    <circle cx="460" cy="120" r="32" fill="#FCD34D"/>

    {{-- Sea (horizontal band) --}}
    <path d="M0 320 L600 320 L600 410 L0 410 Z" fill="url(#seaGrad)"/>
    {{-- Sea waves --}}
    <path d="M0 320 Q50 310 100 320 T200 320 T300 320 T400 320 T500 320 T600 320 V330 H0 Z" fill="#BAE6FD" opacity="0.7"/>
    <path d="M0 340 Q60 330 120 340 T240 340 T360 340 T480 340 T600 340 V350 H0 Z" fill="#7DD3FC" opacity="0.5"/>

    {{-- Sand --}}
    <path d="M0 380 L600 380 L600 500 L0 500 Z" fill="url(#sandGrad)"/>

    {{-- Palm tree (left) --}}
    <g>
        {{-- Trunk --}}
        <path d="M120 410 Q108 360 100 280 Q98 240 110 220" stroke="#92400E" stroke-width="8" fill="none" stroke-linecap="round"/>
        {{-- Palm leaves (5 leaves) --}}
        <ellipse cx="80" cy="195" rx="55" ry="14" fill="#22C55E" transform="rotate(-25 80 195)"/>
        <ellipse cx="140" cy="190" rx="55" ry="14" fill="#16A34A" transform="rotate(20 140 190)"/>
        <ellipse cx="60" cy="220" rx="50" ry="12" fill="#15803D" transform="rotate(-60 60 220)"/>
        <ellipse cx="160" cy="220" rx="50" ry="12" fill="#22C55E" transform="rotate(60 160 220)"/>
        <ellipse cx="110" cy="170" rx="30" ry="10" fill="#16A34A" transform="rotate(-5 110 170)"/>
        {{-- Coconuts --}}
        <circle cx="105" cy="218" r="6" fill="#7C2D12"/>
        <circle cx="118" cy="220" r="6" fill="#92400E"/>
    </g>

    {{-- Beach umbrella (right) --}}
    <g>
        {{-- Pole --}}
        <line x1="480" y1="330" x2="480" y2="410" stroke="#1F2937" stroke-width="3" stroke-linecap="round"/>
        {{-- Umbrella canopy --}}
        <path d="M420 320 Q480 280 540 320 Q480 305 420 320 Z" fill="#EF4444"/>
        <path d="M420 320 Q450 290 480 305 L480 320 Z" fill="#FCA5A5"/>
        <path d="M540 320 Q510 290 480 305 L480 320 Z" fill="#DC2626"/>
        {{-- Top knob --}}
        <circle cx="480" cy="285" r="4" fill="#1F2937"/>
    </g>

    {{-- Phone (center) — showing LinkPay link --}}
    <g transform="translate(245 200)">
        {{-- Phone body --}}
        <rect x="0" y="0" width="120" height="220" rx="18" fill="#1F2937"/>
        <rect x="4" y="4" width="112" height="212" rx="14" fill="#FFFFFF"/>
        {{-- Phone notch --}}
        <rect x="42" y="9" width="36" height="6" rx="3" fill="#1F2937"/>
        {{-- Screen content --}}
        <text x="60" y="42" text-anchor="middle" font-family="Public Sans, system-ui, sans-serif" font-size="9" font-weight="700" fill="#384551">LinkPay</text>
        <rect x="14" y="55" width="92" height="6" rx="3" fill="#E5E7EB"/>
        <rect x="14" y="68" width="60" height="6" rx="3" fill="#E5E7EB"/>

        {{-- Link card --}}
        <rect x="14" y="84" width="92" height="52" rx="8" fill="#F0F1FF"/>
        <rect x="20" y="92" width="80" height="5" rx="2.5" fill="#696CFF"/>
        <text x="60" y="115" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="7" font-weight="700" fill="#696CFF">/khuyenmai</text>
        <rect x="36" y="122" width="48" height="8" rx="4" fill="#696CFF"/>
        <text x="60" y="129" text-anchor="middle" font-family="Public Sans" font-size="5" font-weight="700" fill="#FFFFFF">COPY</text>

        {{-- Earnings stat --}}
        <rect x="14" y="148" width="92" height="40" rx="8" fill="#D1FAE5"/>
        <text x="60" y="163" text-anchor="middle" font-family="Public Sans" font-size="6" font-weight="600" fill="#047857">Hôm nay</text>
        <text x="60" y="180" text-anchor="middle" font-family="Public Sans" font-size="13" font-weight="800" fill="#047857">+247.500đ</text>

        {{-- Home indicator --}}
        <rect x="46" y="206" width="28" height="3" rx="1.5" fill="#1F2937" opacity="0.3"/>
    </g>

    {{-- Floating coins around phone --}}
    <g>
        {{-- Coin 1 (top-left of phone) --}}
        <circle cx="200" cy="170" r="22" fill="#FBBF24"/>
        <circle cx="200" cy="170" r="18" fill="#FCD34D"/>
        <text x="200" y="178" text-anchor="middle" font-family="Public Sans" font-size="18" font-weight="900" fill="#92400E">₫</text>

        {{-- Coin 2 (top-right) --}}
        <circle cx="410" cy="160" r="18" fill="#FBBF24"/>
        <circle cx="410" cy="160" r="14" fill="#FCD34D"/>
        <text x="410" y="167" text-anchor="middle" font-family="Public Sans" font-size="14" font-weight="900" fill="#92400E">$</text>

        {{-- Coin 3 (mid-left, smaller) --}}
        <circle cx="180" cy="280" r="14" fill="#FBBF24" opacity="0.85"/>
        <text x="180" y="286" text-anchor="middle" font-family="Public Sans" font-size="11" font-weight="900" fill="#92400E">₫</text>

        {{-- Sparkles --}}
        <g fill="#FBBF24">
            <path d="M170 130 L173 137 L180 140 L173 143 L170 150 L167 143 L160 140 L167 137 Z"/>
            <path d="M430 250 L432 254 L436 256 L432 258 L430 262 L428 258 L424 256 L428 254 Z"/>
            <path d="M250 130 L252 134 L256 136 L252 138 L250 142 L248 138 L244 136 L248 134 Z"/>
        </g>
    </g>

    {{-- Starfish on sand --}}
    <g transform="translate(135 445)" fill="#F97316">
        <path d="M0,-12 L3.5,-3.5 L12,-1 L5,5 L7,14 L0,9 L-7,14 L-5,5 L-12,-1 L-3.5,-3.5 Z"/>
    </g>

    {{-- Beach ball on sand (right) --}}
    <g transform="translate(420 440)">
        <circle r="16" fill="#FFFFFF" stroke="#E5E7EB" stroke-width="1"/>
        <path d="M-16,0 A16,16 0 0,1 16,0" fill="#EF4444"/>
        <path d="M0,-16 L0,16" stroke="#1F2937" stroke-width="0.5" opacity="0.3"/>
        <path d="M-16,0 L16,0" stroke="#1F2937" stroke-width="0.5" opacity="0.3"/>
    </g>

    {{-- Cloud --}}
    <g fill="#FFFFFF" opacity="0.9">
        <ellipse cx="180" cy="80" rx="30" ry="14"/>
        <ellipse cx="200" cy="75" rx="25" ry="12"/>
        <ellipse cx="160" cy="75" rx="20" ry="10"/>
    </g>
    <g fill="#FFFFFF" opacity="0.7">
        <ellipse cx="380" cy="60" rx="24" ry="10"/>
        <ellipse cx="395" cy="56" rx="20" ry="8"/>
    </g>
</svg>
