<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$kriteriaResult = $conn->query('SELECT k.id, k.nama_kriteria, k.tipe, bk.nilai_bobot FROM kriteria k JOIN bobot_kriteria bk ON bk.id = (SELECT MAX(id) FROM bobot_kriteria WHERE kriteria_id = k.id) ORDER BY k.id');
$kriteria = $kriteriaResult->fetch_all(MYSQLI_ASSOC);
$penilaianResult = $conn->query('SELECT supplier_id, kriteria_id, nilai FROM penilaian_supplier');
$penilaianRows = $penilaianResult->fetch_all(MYSQLI_ASSOC);

$suppliers = [];
$valuesByKriteria = [];
foreach ($penilaianRows as $row) {
    $supplierId = (int) $row['supplier_id'];
    $kriteriaId = (int) $row['kriteria_id'];
    $nilai = (float) str_replace(',', '.', $row['nilai']);
    $valuesByKriteria[$kriteriaId][] = $nilai;
    $suppliers[$supplierId][$kriteriaId] = $nilai;
}

$summary = [];
foreach ($kriteria as $krit) {
    if (empty($valuesByKriteria[$krit['id']])) {
        continue;
    }
    $summary[$krit['id']] = [
        'max' => max($valuesByKriteria[$krit['id']]),
        'min' => min($valuesByKriteria[$krit['id']]),
    ];
}

$ranking = [];
foreach ($suppliers as $supplierId => $kriteriaValues) {
    $score = 0.0;
    foreach ($kriteria as $krit) {
        if (!isset($kriteriaValues[$krit['id']]) || !isset($summary[$krit['id']])) {
            continue;
        }
        $nilai = (float) str_replace(',', '.', $kriteriaValues[$krit['id']]);
        $weight = (float) str_replace(',', '.', $krit['nilai_bobot']);
        $tipe = strtolower(trim($krit['tipe']));
        $normalized = $tipe === 'cost'
            ? ($nilai > 0 ? $summary[$krit['id']]['min'] / $nilai : 0)
            : ($summary[$krit['id']]['max'] > 0 ? $nilai / $summary[$krit['id']]['max'] : 0);
        $score += $weight * $normalized;
    }
    $ranking[$supplierId] = $score;
}
arsort($ranking);
$supplierQuery = $conn->query('SELECT id, nama_supplier FROM supplier ORDER BY nama_supplier');
$supplierNames = [];
while ($row = $supplierQuery->fetch_assoc()) {
    $supplierNames[$row['id']] = $row['nama_supplier'];
}
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet"/>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#000000",
                        "on-secondary-container": "#fefcff",
                        "outline-variant": "#c6c6cd",
                        "tertiary-fixed": "#d3e4fe",
                        "on-primary-fixed": "#131b2e",
                        "surface-container-highest": "#e0e3e5",
                        "on-error": "#ffffff",
                        "surface-container-high": "#e6e8ea",
                        "on-surface": "#191c1e",
                        "surface-tint": "#565e74",
                        "tertiary-container": "#0b1c30",
                        "on-primary-container": "#7c839b",
                        secondary: "#0058be",
                        "surface-container-low": "#f2f4f6",
                        "primary-fixed-dim": "#bec6e0",
                        "on-error-container": "#93000a",
                        "on-primary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "surface-bright": "#f7f9fb",
                        "inverse-on-surface": "#eff1f3",
                        background: "#f7f9fb",
                        "tertiary-fixed-dim": "#b7c8e1",
                        "error-container": "#ffdad6",
                        "on-secondary-fixed-variant": "#004395",
                        "secondary-fixed-dim": "#adc6ff",
                        "secondary-container": "#2170e4",
                        outline: "#76777d",
                        "inverse-primary": "#bec6e0",
                        "on-secondary": "#ffffff",
                        surface: "#f7f9fb",
                        "on-tertiary-container": "#75859d",
                        "secondary-fixed": "#d8e2ff",
                        "on-secondary-fixed": "#001a42",
                        "surface-dim": "#d8dadc",
                        "on-background": "#191c1e",
                        "surface-container": "#eceef0",
                        "on-tertiary-fixed-variant": "#38485d",
                        "surface-variant": "#e0e3e5",
                        "on-surface-variant": "#45464d",
                        "on-tertiary-fixed": "#0b1c30",
                        "on-primary-fixed-variant": "#3f465c",
                        tertiary: "#000000",
                        "inverse-surface": "#2d3133",
                        error: "#ba1a1a",
                        "primary-container": "#131b2e",
                        "on-tertiary": "#ffffff",
                        "primary-fixed": "#dae2fd"
                    },
                    borderRadius: {
                        DEFAULT: "0.125rem",
                        lg: "0.25rem",
                        xl: "0.5rem",
                        full: "0.75rem"
                    },
                    spacing: {
                        "nav-width": "260px",
                        "stack-lg": "24px",
                        "container-max": "1440px",
                        gutter: "24px",
                        "stack-md": "16px",
                        "stack-sm": "8px",
                        "margin-page": "32px"
                    },
                    fontFamily: {
                        "headline-md": ["Inter"],
                        "label-caps": ["Inter"],
                        "body-md": ["Inter"],
                        "display-lg": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-lg-mobile": ["Inter"],
                        "headline-sm": ["Inter"],
                        "data-mono": ["JetBrains Mono"]
                    },
                    fontSize: {
                        "headline-md": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "label-caps": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
                        "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "display-lg": ["36px", { lineHeight: "44px", letterSpacing: "-0.02em", fontWeight: "700" }],
                        "body-lg": ["16px", { lineHeight: "24px", fontWeight: "400" }],
                        "headline-lg-mobile": ["28px", { lineHeight: "36px", fontWeight: "700" }],
                        "headline-sm": ["18px", { lineHeight: "24px", fontWeight: "600" }],
                        "data-mono": ["13px", { lineHeight: "18px", fontWeight: "500" }]
                    }
                }
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body>
<div class="app-shell">
    <aside class="w-nav-width h-screen sticky top-0 left-0 bg-white text-black border-r border-outline-variant flex flex-col h-full py-stack-lg px-stack-md shrink-0">
        <div class="flex items-center gap-stack-sm mb-stack-lg px-2">
            <div class="w-10 h-10 bg-black rounded flex items-center justify-center text-white">
                <span class="material-symbols-outlined">analytics</span>
            </div>
            <div>
                <h1 class="font-headline-sm text-headline-sm font-bold text-primary dark:text-on-primary-fixed">SAW-DSS</h1>
                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Supplier Selection</p>
            </div>
        </div>
        <?php $current = basename($_SERVER['PHP_SELF']); ?>
        <nav class="flex-1 space-y-1 overflow-y-auto">
            <a class="<?php echo ($current === 'dashboard.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="dashboard.php">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-body-md text-body-md">Dashboard</span>
            </a>
            <a class="<?php echo ($current === 'supplier.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="supplier.php">
                <span class="material-symbols-outlined">inventory_2</span>
                <span class="font-body-md text-body-md">Input Supplier</span>
            </a>
            <a class="<?php echo ($current === 'kriteria.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="kriteria.php">
                <span class="material-symbols-outlined">fact_check</span>
                <span class="font-body-md text-body-md">Input Kriteria</span>
            </a>
            <a class="<?php echo ($current === 'bobot.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="bobot.php">
                <span class="material-symbols-outlined">straighten</span>
                <span class="font-body-md text-body-md">Input Bobot</span>
            </a>
            <a class="<?php echo ($current === 'penilaian.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="penilaian.php">
                <span class="material-symbols-outlined">thumbs_up_down</span>
                <span class="font-body-md text-body-md">Penilaian Supplier</span>
            </a>
            <div class="py-2"><div class="h-[1px] bg-outline-variant/30 mx-3"></div></div>
            <a class="<?php echo ($current === 'normalisasi.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="normalisasi.php">
                <span class="material-symbols-outlined">calculate</span>
                <span class="font-body-md text-body-md">Normalisasi</span>
            </a>
            <a class="<?php echo ($current === 'preferensi.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="preferensi.php">
                <span class="material-symbols-outlined">star</span>
                <span class="font-body-md text-body-md">Preferensi</span>
            </a>
            <a class="<?php echo ($current === 'ranking.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="ranking.php">
                <span class="material-symbols-outlined">leaderboard</span>
                <span class="font-body-md text-body-md">Ranking</span>
            </a>
            <a class="<?php echo ($current === 'laporan.php') ? 'flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold' : 'flex items-center gap-3 px-3 py-2.5 text-black hover:bg-gray-100 transition-colors duration-200 rounded-lg'; ?>" href="laporan.php">
                <span class="material-symbols-outlined">assessment</span>
                <span class="font-body-md text-body-md">Laporan</span>
            </a>
        </nav>
        <div class="mt-auto pt-stack-lg border-t border-outline-variant/30 px-2 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-gray-800 overflow-hidden flex items-center justify-center">
                <?php if (!empty($_SESSION['nama'])): ?>
                    <span class="material-symbols-outlined text-white">account_circle</span>
                <?php else: ?>
                    <span class="material-symbols-outlined text-secondary">account_circle</span>
                <?php endif; ?>
            </div>
            <div class="overflow-hidden">
                <p class="font-body-md text-body-md font-bold truncate"><?php echo e($_SESSION['nama']); ?></p>
                <p class="text-xs text-on-surface-variant truncate"><?php echo e($_SESSION['username']); ?></p>
            </div>
        </div>
    </aside>
    <main class="main-content">
        <header class="flex justify-between items-center h-16 px-margin-page sticky top-0 z-50 w-full bg-surface/80 backdrop-blur-md border-b border-outline-variant dark:border-outline">
            <div class="flex items-center gap-4">
                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface dark:text-on-surface">Ranking</h2>
                <div class="h-6 w-[1px] bg-outline-variant"></div>
                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">Urutkan dari nilai tertinggi</span>
            </div>
            <div class="flex items-center gap-stack-md">
                <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors">
                    <span class="material-symbols-outlined">settings</span>
                </button>
                <button class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-full transition-colors relative">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-error rounded-full border-2 border-surface"></span>
                </button>
                <div class="h-8 w-[1px] bg-outline-variant mx-2"></div>
                <div class="flex items-center gap-3 pl-2 group cursor-pointer">
                    <div class="text-right">
                        <p class="font-label-caps text-label-caps text-on-surface font-bold"><?php echo e($_SESSION['nama']); ?></p>
                        <p class="text-[10px] text-on-surface-variant">Active Now</p>
                    </div>
                    <a class="px-4 py-1.5 bg-primary text-on-primary rounded font-label-caps text-label-caps hover:opacity-80 transition-opacity uppercase" href="logout.php">Logout</a>
                </div>
            </div>
        </header>
        <div class="page-content">
            <?php if ($flash): ?><div class="notice <?php echo e($flash['type']); ?>"><?php echo e($flash['message']); ?></div><?php endif; ?>
            <div class="card">
                <h3 style="margin-top:0;">Daftar Ranking Supplier</h3>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Rank</th><th>Supplier</th><th>Skor Preferensi</th><th>Keterangan</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($ranking): $place = 1; foreach ($ranking as $supplierId => $score): ?>
                                <tr>
                                    <td><?php echo $place++; ?></td>
                                    <td><?php echo e($supplierNames[$supplierId] ?? 'Supplier #'.$supplierId); ?></td>
                                    <td><?php echo e(number_format($score, 4, ',', '')); ?></td>
                                    <td><?php echo e($score >= 0.8 ? 'Terbaik' : ($score >= 0.6 ? 'Baik' : 'Perlu Tindak Lanjut')); ?></td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" class="muted">Data ranking belum tersedia karena belum cukup input.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="assets/js/app.js"></script>
</body>
</html>
