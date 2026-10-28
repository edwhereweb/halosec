<?php
/**
 * Flat brand illustrations for the free audit, one per question plus "hero". Decorative (aria-hidden).
 * Expects $name; optional $class. Palette: ink #0b0f17, blue #0369a1, sky #38bdf8 / #bae6fd / #e0f2fe,
 * lime #d4f844, slate #cbd5e1.
 */
$name = $name ?? 'hero';
$class = $class ?? 'h-full w-full';
$bg = '<circle cx="100" cy="78" r="60" fill="#e0f2fe"/><circle cx="34" cy="34" r="4" fill="#bae6fd"/><circle cx="170" cy="120" r="5" fill="#d4f844"/><circle cx="168" cy="30" r="3" fill="#38bdf8"/>';
$check = static fn (float $x, float $y, float $s = 1): string => '<path d="M' . ($x - 7 * $s) . ' ' . $y . ' l' . (5 * $s) . ' ' . (5 * $s) . ' l' . (10 * $s) . ' -' . (11 * $s) . '" fill="none" stroke="#0b0f17" stroke-width="' . (3.5 * $s) . '" stroke-linecap="round" stroke-linejoin="round"/>';

$art = match ($name) {
    'q_firewall' => $bg
        . implode('', array_map(static function (int $row): string {
            $out = '';
            $offset = $row % 2 === 0 ? 0 : 14;
            for ($i = 0; $i < 5; $i++) {
                $x = 42 + $offset + $i * 29;
                if ($x + 26 > 162) {
                    continue;
                }
                $out .= '<rect x="' . $x . '" y="' . (52 + $row * 16) . '" width="26" height="13" rx="3" fill="' . ($row === 1 && $i === 3 ? '#38bdf8' : '#94a3b8') . '"/>';
            }

            return $out;
        }, [0, 1, 2, 3]))
        . '<path d="M100 40 l26 10 v20 c0 17 -11 29 -26 35 c-15 -6 -26 -18 -26 -35 v-20z" fill="#0369a1" stroke="#fff" stroke-width="4"/>'
        . '<circle cx="100" cy="72" r="13" fill="#d4f844"/>' . $check(100, 72, 0.8),
    'q_segmentation' => $bg
        . '<path d="M76 46v68M124 46v68" stroke="#0369a1" stroke-width="3" stroke-dasharray="5 6" stroke-linecap="round"/>'
        . '<rect x="34" y="58" width="36" height="44" rx="9" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<path d="M43 76a14 14 0 0 1 18 0M46 82a8 8 0 0 1 12 0" fill="none" stroke="#0369a1" stroke-width="3" stroke-linecap="round"/><circle cx="52" cy="88" r="3" fill="#0369a1"/>'
        . '<rect x="82" y="58" width="36" height="44" rx="9" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="90" y="67" width="20" height="8" rx="2" fill="#38bdf8"/><rect x="90" y="78" width="20" height="8" rx="2" fill="#38bdf8"/><rect x="90" y="89" width="20" height="5" rx="2" fill="#bae6fd"/>'
        . '<rect x="130" y="58" width="36" height="44" rx="9" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="138" y="70" width="20" height="13" rx="2" fill="#0369a1"/><path d="M134 88h28" stroke="#0b0f17" stroke-width="3" stroke-linecap="round"/>'
        . '<rect x="90" y="30" width="20" height="16" rx="4" fill="#d4f844"/><path d="M94 30v-4a6 6 0 0 1 12 0v4" fill="none" stroke="#0b0f17" stroke-width="3"/>',
    'q_endpoint' => $bg
        . '<rect x="56" y="40" width="88" height="58" rx="7" fill="#0b0f17"/><rect x="62" y="46" width="76" height="46" rx="3" fill="#0369a1"/>'
        . '<path d="M46 102h108l-8 10H54z" fill="#94a3b8"/>'
        . '<path d="M100 52 l15 6 v11 c0 10 -6 16 -15 20 c-9 -4 -15 -10 -15 -20 v-11z" fill="#d4f844"/>' . $check(100, 70, 0.75)
        . '<path d="M40 52l5 5M45 52l-5 5M160 64l4 4M164 64l-4 4" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>',
    'q_patching' => $bg
        . '<rect x="50" y="40" width="100" height="72" rx="10" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<path d="M50 56h100" stroke="#0b0f17" stroke-width="3"/><circle cx="62" cy="48" r="3" fill="#94a3b8"/><circle cx="72" cy="48" r="3" fill="#94a3b8"/><circle cx="82" cy="48" r="3" fill="#94a3b8"/>'
        . '<path d="M86 92a16 16 0 0 1 26 -20M114 76a16 16 0 0 1 -26 20" fill="none" stroke="#0369a1" stroke-width="6" stroke-linecap="round"/>'
        . '<path d="M111 64l2 9 -9 1M89 104l-2 -9 9 -1" fill="none" stroke="#0369a1" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>'
        . '<circle cx="148" cy="42" r="14" fill="#d4f844" stroke="#fff" stroke-width="3"/>' . $check(148, 42, 0.75),
    'q_mfa' => $bg
        . '<rect x="74" y="28" width="50" height="94" rx="10" fill="#0b0f17"/><rect x="80" y="38" width="38" height="70" rx="4" fill="#fff"/>'
        . '<rect x="86" y="50" width="26" height="6" rx="3" fill="#bae6fd"/>'
        . '<circle cx="88" cy="74" r="4" fill="#0369a1"/><circle cx="99" cy="74" r="4" fill="#0369a1"/><circle cx="110" cy="74" r="4" fill="#0369a1"/>'
        . '<circle cx="88" cy="88" r="4" fill="#38bdf8"/><circle cx="99" cy="88" r="4" fill="#38bdf8"/><circle cx="110" cy="88" r="4" fill="#38bdf8"/>'
        . '<circle cx="146" cy="96" r="12" fill="none" stroke="#d4f844" stroke-width="6"/><path d="M146 108v16M146 116h7M146 122h5" stroke="#d4f844" stroke-width="6" stroke-linecap="round"/>'
        . '<path d="M50 60l8 -6M48 74h10M50 88l8 6" stroke="#0369a1" stroke-width="3" stroke-linecap="round"/>',
    'q_backup' => $bg
        . '<path d="M58 50v50c0 6 14 11 30 11s30 -5 30 -11V50" fill="#0369a1"/>'
        . '<ellipse cx="88" cy="50" rx="30" ry="10" fill="#38bdf8"/>'
        . '<path d="M58 68c0 6 14 11 30 11s30 -5 30 -11M58 85c0 6 14 11 30 11s30 -5 30 -11" fill="none" stroke="#bae6fd" stroke-width="3"/>'
        . '<rect x="120" y="84" width="34" height="28" rx="6" fill="#d4f844" stroke="#fff" stroke-width="3"/>'
        . '<path d="M128 84v-7a9 9 0 0 1 18 0v7" fill="none" stroke="#0b0f17" stroke-width="4"/><circle cx="137" cy="97" r="3.5" fill="#0b0f17"/>'
        . '<path d="M140 42l4 4M144 42l-4 4M44 104l4 4M48 104l-4 4" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>',
    'q_inventory' => $bg
        . '<rect x="62" y="34" width="76" height="90" rx="9" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="86" y="27" width="28" height="13" rx="5" fill="#0369a1"/>'
        . implode('', array_map(static function (int $i) use ($check): string {
            $y = 54 + $i * 17;
            $done = $i < 3;

            return '<rect x="72" y="' . $y . '" width="11" height="11" rx="3" fill="' . ($done ? '#d4f844' : '#fff') . '" stroke="#0b0f17" stroke-width="2.5"/>'
                . ($done ? '<path d="M74.5 ' . ($y + 5.5) . 'l2.5 2.5 4 -5" fill="none" stroke="#0b0f17" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>' : '')
                . '<rect x="90" y="' . ($y + 3) . '" width="' . ($i === 3 ? 26 : 38) . '" height="5" rx="2.5" fill="' . ($done ? '#94a3b8' : '#cbd5e1') . '"/>';
        }, [0, 1, 2, 3]))
        . '<rect x="140" y="92" width="26" height="18" rx="3" fill="#38bdf8" stroke="#fff" stroke-width="3"/><path d="M136 114h34" stroke="#0b0f17" stroke-width="3" stroke-linecap="round"/>',
    'q_monitoring' => $bg
        . '<circle cx="100" cy="80" r="46" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<circle cx="100" cy="80" r="31" fill="none" stroke="#bae6fd" stroke-width="2.5"/><circle cx="100" cy="80" r="16" fill="none" stroke="#bae6fd" stroke-width="2.5"/>'
        . '<path d="M100 80 L100 34 A46 46 0 0 1 140 57 Z" fill="#38bdf8" opacity=".35"/>'
        . '<path d="M100 80 L140 57" stroke="#0369a1" stroke-width="3" stroke-linecap="round"/>'
        . '<circle cx="118" cy="52" r="5" fill="#0369a1"/><circle cx="80" cy="100" r="4" fill="#94a3b8"/><circle cx="128" cy="96" r="6" fill="#d4f844" stroke="#0b0f17" stroke-width="2"/>'
        . '<circle cx="100" cy="80" r="4" fill="#0b0f17"/>',
    'q_training' => $bg
        . '<path d="M100 14v34" stroke="#0b0f17" stroke-width="2.5"/>'
        . '<path d="M100 44v14a8 8 0 1 1 -10 -8" fill="none" stroke="#0369a1" stroke-width="5" stroke-linecap="round"/>'
        . '<rect x="52" y="70" width="96" height="58" rx="8" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<path d="M54 74l46 30 46 -30" fill="none" stroke="#0b0f17" stroke-width="3" stroke-linejoin="round"/>'
        . '<circle cx="150" cy="66" r="13" fill="#d4f844" stroke="#fff" stroke-width="3"/><path d="M150 59v8M150 72v.5" stroke="#0b0f17" stroke-width="3.5" stroke-linecap="round"/>',
    'q_incident' => $bg
        . '<rect x="56" y="44" width="70" height="82" rx="8" fill="#fff" stroke="#0b0f17" stroke-width="3"/>'
        . '<rect x="66" y="58" width="34" height="6" rx="3" fill="#0369a1"/>'
        . '<rect x="66" y="74" width="48" height="5" rx="2.5" fill="#cbd5e1"/><rect x="66" y="86" width="42" height="5" rx="2.5" fill="#cbd5e1"/><rect x="66" y="98" width="46" height="5" rx="2.5" fill="#cbd5e1"/>'
        . '<circle cx="70" cy="113" r="3" fill="#d4f844" stroke="#0b0f17" stroke-width="1.5"/><rect x="78" y="111" width="30" height="5" rx="2.5" fill="#cbd5e1"/>'
        . '<path d="M126 60a16 16 0 0 1 32 0v8h-32z" fill="#38bdf8" stroke="#0b0f17" stroke-width="3" stroke-linejoin="round"/>'
        . '<rect x="120" y="66" width="44" height="9" rx="3" fill="#0b0f17"/>'
        . '<path d="M142 30v-8M122 38l-6 -5M162 38l6 -5" stroke="#d4f844" stroke-width="4" stroke-linecap="round"/>',
    default => '<circle cx="100" cy="78" r="64" fill="#e0f2fe"/>'
        . '<ellipse cx="100" cy="40" rx="44" ry="11" fill="none" stroke="#d4f844" stroke-width="6"/>'
        . '<path d="M56 96l-18 18M144 96l18 18M46 70H22M154 70h24" stroke="#38bdf8" stroke-width="3" stroke-linecap="round" stroke-dasharray="2 7"/>'
        . '<circle cx="34" cy="118" r="7" fill="#0369a1"/><circle cx="166" cy="118" r="7" fill="#0369a1"/><circle cx="18" cy="70" r="6" fill="#38bdf8"/><circle cx="182" cy="70" r="6" fill="#38bdf8"/>'
        . '<path d="M100 50 l34 13 v26 c0 22 -14 37 -34 45 c-20 -8 -34 -23 -34 -45 v-26z" fill="#0369a1" stroke="#fff" stroke-width="5"/>'
        . '<path d="M100 62 l22 8 v18 c0 14 -9 24 -22 29z" fill="#38bdf8" opacity=".55"/>'
        . '<circle cx="100" cy="90" r="17" fill="#d4f844"/>' . $check(100, 90, 1.05),
};
?>
<svg class="<?= HaloSec\View::e($class) ?>" viewBox="0 0 200 150" fill="none" aria-hidden="true" focusable="false"><?= $art ?></svg>
