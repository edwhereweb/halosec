<?php
/**
 * Flat brand illustrations for the guides (hero art per topic, plus figures inside guide bodies).
 * Decorative (aria-hidden); the surrounding text or figcaption carries the meaning.
 * Expects $name; optional $class. Same palette as partials/audit_art: ink #0b0f17, blue #0369a1,
 * sky #38bdf8 / #bae6fd, lime #d4f844, slate #94a3b8 / #cbd5e1.
 */
$name = $name ?? 'library';
$class = $class ?? 'h-full w-full';
$bg = '<circle cx="100" cy="78" r="62" fill="#fff" opacity=".65"/>';
$check = static fn (float $x, float $y, float $s = 1): string => '<path d="M' . ($x - 7 * $s) . ' ' . $y . ' l' . (5 * $s) . ' ' . (5 * $s) . ' l' . (10 * $s) . ' -' . (11 * $s) . '" fill="none" stroke="#0b0f17" stroke-width="' . (3.5 * $s) . '" stroke-linecap="round" stroke-linejoin="round"/>';
$spark = static fn (float $x, float $y, string $c = '#0369a1'): string => '<path d="M' . $x . ' ' . ($y - 6) . 'v12M' . ($x - 6) . ' ' . $y . 'h12" stroke="' . $c . '" stroke-width="2.5" stroke-linecap="round"/>';
$shield = static fn (float $x, float $y, float $s, string $fill): string => '<path d="M' . $x . ' ' . ($y - 14 * $s) . ' l' . (12 * $s) . ' ' . (4.5 * $s) . ' v' . (9 * $s) . ' c0 ' . (7.5 * $s) . ' -' . (5 * $s) . ' ' . (12.5 * $s) . ' -' . (12 * $s) . ' ' . (15.5 * $s) . ' c-' . (7 * $s) . ' -' . (3 * $s) . ' -' . (12 * $s) . ' -' . (8 * $s) . ' -' . (12 * $s) . ' -' . (15.5 * $s) . ' v-' . (9 * $s) . 'z" fill="' . $fill . '"/>';
$text = static fn (float $x, float $y, string $t, int $size, string $fill): string => '<text x="' . $x . '" y="' . $y . '" font-family="Space Grotesk, sans-serif" font-size="' . $size . '" font-weight="700" fill="' . $fill . '" text-anchor="middle">' . $t . '</text>';

$art = match ($name) {
    // Topic heroes
    'compliance' => $bg
        . '<rect x="56" y="22" width="80" height="108" rx="10" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="68" y="36" width="40" height="7" rx="3.5" fill="#0369a1"/>'
        . implode('', array_map(static function (int $i): string {
            $y = 54 + $i * 17;
            $done = $i < 3;

            return '<rect x="68" y="' . $y . '" width="11" height="11" rx="3" fill="' . ($done ? '#d4f844' : '#fff') . '" stroke="#0b0f17" stroke-width="2.5"/>'
                . ($done ? '<path d="M70.5 ' . ($y + 5.5) . 'l2.5 2.5 4 -5" fill="none" stroke="#0b0f17" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' : '')
                . '<rect x="86" y="' . ($y + 3) . '" width="' . [36, 30, 38, 24][$i] . '" height="5" rx="2.5" fill="' . ($done ? '#94a3b8' : '#cbd5e1') . '"/>';
        }, [0, 1, 2, 3]))
        . '<path d="M146 66 l24 9 v18 c0 15 -10 26 -24 31 c-14 -5 -24 -16 -24 -31 v-18z" fill="#0369a1" stroke="#fff" stroke-width="4"/>'
        . '<circle cx="146" cy="94" r="11" fill="#d4f844"/>' . $check(146, 94, 0.8)
        . $spark(36, 40) . $spark(176, 34, '#38bdf8') . '<circle cx="34" cy="112" r="5" fill="#d4f844"/>',
    'endpoint' => $bg
        . '<path d="M88 56 L56 84M112 56 L148 76M100 56 L101 90" stroke="#0369a1" stroke-width="2.5" stroke-dasharray="4 5" stroke-linecap="round"/>'
        . '<rect x="72" y="16" width="56" height="40" rx="8" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="80" y="26" width="22" height="5" rx="2.5" fill="#0369a1"/><rect x="80" y="36" width="38" height="5" rx="2.5" fill="#cbd5e1"/><rect x="80" y="45" width="28" height="5" rx="2.5" fill="#cbd5e1"/>'
        . '<circle cx="118" cy="28" r="5" fill="#d4f844" stroke="#0b0f17" stroke-width="2"/>'
        . '<rect x="26" y="84" width="56" height="34" rx="4" fill="#0b0f17"/><rect x="30" y="88" width="48" height="26" rx="2" fill="#0369a1"/>'
        . '<path d="M18 120h72l-6 7H24z" fill="#94a3b8"/>' . $shield(54, 103, 0.75, '#d4f844')
        . '<rect x="122" y="76" width="58" height="38" rx="4" fill="#0b0f17"/><rect x="126" y="80" width="50" height="30" rx="2" fill="#38bdf8"/>'
        . '<path d="M145 114h12l3 10h-18z" fill="#94a3b8"/><rect x="138" y="124" width="26" height="4" rx="2" fill="#94a3b8"/>' . $shield(151, 97, 0.75, '#d4f844')
        . '<rect x="90" y="90" width="22" height="40" rx="5" fill="#0b0f17"/><rect x="93" y="95" width="16" height="28" rx="2" fill="#bae6fd"/>'
        . '<circle cx="101" cy="109" r="4.5" fill="#d4f844"/>'
        . $spark(34, 40, '#38bdf8') . $spark(170, 36),
    'library' => '<circle cx="100" cy="78" r="66" fill="#fff" opacity=".65"/>'
        . '<rect x="38" y="38" width="64" height="84" rx="10" fill="#bae6fd" stroke="#0b0f17" stroke-width="3" transform="rotate(-12 70 80)"/>'
        . '<rect x="98" y="38" width="64" height="84" rx="10" fill="#d4f844" stroke="#0b0f17" stroke-width="3" transform="rotate(10 130 80)"/>'
        . '<rect x="66" y="26" width="68" height="94" rx="10" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="76" y="38" width="34" height="7" rx="3.5" fill="#0369a1"/>'
        . '<rect x="76" y="54" width="48" height="5" rx="2.5" fill="#cbd5e1"/><rect x="76" y="65" width="42" height="5" rx="2.5" fill="#cbd5e1"/><rect x="76" y="76" width="46" height="5" rx="2.5" fill="#cbd5e1"/>'
        . '<path d="M112 86 l14 5 v10 c0 9 -6 15 -14 18 c-8 -3 -14 -9 -14 -18 v-10z" fill="#0369a1" stroke="#fff" stroke-width="3"/>'
        . '<circle cx="112" cy="102" r="6" fill="#d4f844"/>'
        . $spark(28, 34, '#38bdf8') . $spark(176, 120) . '<circle cx="172" cy="30" r="5" fill="#0369a1"/><circle cx="30" cy="122" r="4" fill="#d4f844" stroke="#0b0f17" stroke-width="2"/>',

    // Figures inside guide bodies
    'editions' => $bg
        . implode('', array_map(static function (array $layer) use ($text): string {
            [$yc, $fill, $label] = $layer;

            return '<path d="M30 ' . $yc . ' l54 -18 l54 18 l-54 18z" fill="' . $fill . '" stroke="#fff" stroke-width="3" stroke-linejoin="round"/>'
                . '<path d="M138 ' . $yc . 'h8" stroke="#0b0f17" stroke-width="2"/>'
                . str_replace('text-anchor="middle"', 'text-anchor="start"', $text(150, $yc + 3.5, $label, 10, '#0b0f17'));
        }, [[116, '#0b0f17', 'Endpoint'], [97, '#0369a1', 'EDR'], [78, '#38bdf8', 'XDR'], [59, '#d4f844', 'MDR']]))
        . $spark(176, 34) . '<circle cx="24" cy="40" r="5" fill="#d4f844" stroke="#0b0f17" stroke-width="2"/>',
    'licence' => $bg
        . '<path d="M80 68 L120 48M80 88 L120 102" stroke="#0369a1" stroke-width="2.5" stroke-dasharray="4 5" stroke-linecap="round"/>'
        . '<circle cx="52" cy="54" r="15" fill="#0369a1"/><path d="M26 110 a26 26 0 0 1 52 0z" fill="#0369a1"/>'
        . '<circle cx="74" cy="38" r="11" fill="#d4f844" stroke="#fff" stroke-width="3"/>' . $text(74, 42.5, '1', 13, '#0b0f17')
        . '<rect x="124" y="28" width="50" height="30" rx="4" fill="#0b0f17"/><rect x="128" y="32" width="42" height="22" rx="2" fill="#38bdf8"/><path d="M116 60h66l-5 6h-56z" fill="#94a3b8"/>'
        . '<rect x="126" y="86" width="50" height="32" rx="4" fill="#0b0f17"/><rect x="130" y="90" width="42" height="24" rx="2" fill="#bae6fd"/>'
        . '<path d="M146 118h10l2 8h-14z" fill="#94a3b8"/><rect x="140" y="126" width="22" height="4" rx="2" fill="#94a3b8"/>'
        . $spark(178, 78, '#38bdf8'),
    'rollback' => $bg
        . '<rect x="24" y="40" width="46" height="58" rx="7" fill="#0b0f17"/>'
        . '<path d="M32 52h22M32 60h28" stroke="#475569" stroke-width="4" stroke-linecap="round"/>'
        . '<rect x="36" y="68" width="22" height="18" rx="3" fill="#d4f844"/><path d="M41 68v-5a6 6 0 0 1 12 0v5" fill="none" stroke="#d4f844" stroke-width="3"/>'
        . '<path d="M76 54 C96 20 108 20 126 50" fill="none" stroke="#0369a1" stroke-width="5" stroke-linecap="round"/>'
        . '<path d="M115 47 L126 51 L128 39" fill="none" stroke="#0369a1" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>'
        . '<rect x="130" y="40" width="46" height="58" rx="7" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<path d="M140 54h22M140 63h26M140 72h18" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round"/>'
        . '<circle cx="170" cy="94" r="13" fill="#d4f844" stroke="#fff" stroke-width="3"/>' . $check(170, 94, 0.8)
        . '<path d="M48 118h104" stroke="#bae6fd" stroke-width="3" stroke-dasharray="3 6" stroke-linecap="round"/>'
        . '<circle cx="100" cy="118" r="14" fill="#0369a1"/><path d="M106 112l-7 6 7 6zM98 112l-7 6 7 6z" fill="#fff"/>',
    'pricing' => $bg
        . '<g transform="rotate(-10 92 76)">'
        . '<path d="M40 50 h78 l22 26 -22 26 h-78 a8 8 0 0 1 -8 -8 v-36 a8 8 0 0 1 8 -8z" fill="#fff" stroke="#0b0f17" stroke-width="3" stroke-linejoin="round"/>'
        . '<circle cx="120" cy="76" r="5" fill="none" stroke="#0b0f17" stroke-width="3"/>'
        . $text(78, 89, '₹', 36, '#0369a1')
        . '</g>'
        . '<path d="M140 62 C152 40 160 34 172 30" fill="none" stroke="#0b0f17" stroke-width="2" stroke-linecap="round"/>'
        . implode('', array_map(static fn (int $i): string => '<ellipse cx="160" cy="' . (124 - $i * 9) . '" rx="20" ry="6" fill="#d4f844" stroke="#0b0f17" stroke-width="2.5"/>', [0, 1, 2, 3]))
        . '<circle cx="34" cy="118" r="6" fill="#38bdf8"/><circle cx="50" cy="122" r="4" fill="#0369a1"/>' . $spark(30, 32),
    'support' => $bg
        . '<path d="M64 62 C80 90 84 96 100 100M120 92 C130 92 136 90 142 86" stroke="#0369a1" stroke-width="2.5" stroke-dasharray="4 5" stroke-linecap="round" fill="none"/>'
        . '<ellipse cx="100" cy="124" rx="38" ry="7" fill="#bae6fd"/>'
        . '<path d="M100 22 c-20 0 -34 15 -34 33 c0 24 34 62 34 62 s34 -38 34 -62 c0 -18 -14 -33 -34 -33z" fill="#0369a1" stroke="#fff" stroke-width="4"/>'
        . '<circle cx="100" cy="55" r="13" fill="#d4f844"/>'
        . '<rect x="18" y="34" width="48" height="30" rx="10" fill="#fff" stroke="#0b0f17" stroke-width="3"/><path d="M30 64 l-4 10 12 -10" fill="#fff" stroke="#0b0f17" stroke-width="3" stroke-linejoin="round"/>'
        . '<circle cx="32" cy="49" r="3.5" fill="#0369a1"/><circle cx="42" cy="49" r="3.5" fill="#0369a1"/><circle cx="52" cy="49" r="3.5" fill="#0369a1"/>'
        . '<rect x="140" y="66" width="42" height="38" rx="9" fill="#d4f844" stroke="#0b0f17" stroke-width="3"/>' . $check(161, 85, 0.9)
        . $spark(170, 34, '#38bdf8'),
    'checklist' => $bg
        . '<rect x="54" y="26" width="88" height="108" rx="10" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="80" y="18" width="36" height="14" rx="5" fill="#0369a1"/>'
        . implode('', array_map(static function (int $i): string {
            $x = 68 + ($i % 3) * 24;
            $y = 46 + intdiv($i, 3) * 21;
            $done = $i < 9;

            return '<rect x="' . $x . '" y="' . $y . '" width="14" height="14" rx="4" fill="' . ($done ? '#d4f844' : '#fff') . '" stroke="#0b0f17" stroke-width="2.5"/>'
                . ($done ? '<path d="M' . ($x + 3.5) . ' ' . ($y + 7) . 'l3 3 4.5 -6" fill="none" stroke="#0b0f17" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' : '');
        }, range(0, 11)))
        . '<path d="M150 122 l24 -44 l9 5 l-24 44 l-11 5z" fill="#38bdf8" stroke="#0b0f17" stroke-width="2.5" stroke-linejoin="round"/>'
        . $spark(36, 44) . '<circle cx="32" cy="112" r="5" fill="#d4f844" stroke="#0b0f17" stroke-width="2"/>',
    'breach' => $bg
        . '<circle cx="84" cy="80" r="42" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . implode('', array_map(static function (int $i): string {
            $a = deg2rad($i * 30);

            return '<path d="M' . round(84 + 34 * sin($a), 1) . ' ' . round(80 - 34 * cos($a), 1) . 'L' . round(84 + 38 * sin($a), 1) . ' ' . round(80 - 38 * cos($a), 1) . '" stroke="#94a3b8" stroke-width="2.5" stroke-linecap="round"/>';
        }, range(0, 11)))
        . '<path d="M84 80 V54 M84 80 L102 90" stroke="#0b0f17" stroke-width="4" stroke-linecap="round"/><circle cx="84" cy="80" r="4" fill="#0369a1"/>'
        . '<rect x="122" y="28" width="56" height="28" rx="14" fill="#0369a1"/>' . $text(150, 47, '72h', 15, '#fff')
        . '<path d="M152 82 l18 30 h-36z" fill="#d4f844" stroke="#0b0f17" stroke-width="2.5" stroke-linejoin="round"/>'
        . '<path d="M152 93v9M152 107v.5" stroke="#0b0f17" stroke-width="3" stroke-linecap="round"/>'
        . $spark(32, 36, '#38bdf8'),
    default => '',
};
?>
<svg class="<?= HaloSec\View::e($class) ?>" viewBox="0 0 200 150" fill="none" aria-hidden="true" focusable="false"><?= $art ?></svg>
