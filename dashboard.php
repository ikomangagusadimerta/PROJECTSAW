<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$query = 'SELECT COUNT(*) AS total_supplier FROM supplier';
$totalSupplier = (int) ($conn->query($query)->fetch_assoc()['total_supplier'] ?? 0);

$query = 'SELECT COUNT(*) AS total_kriteria FROM kriteria';
$totalKriteria = (int) ($conn->query($query)->fetch_assoc()['total_kriteria'] ?? 0);

$query = 'SELECT COUNT(*) AS total_bobot FROM bobot_kriteria';
$totalBobot = (int) ($conn->query($query)->fetch_assoc()['total_bobot'] ?? 0);

$query = 'SELECT COUNT(*) AS total_penilaian FROM penilaian_supplier';
$totalPenilaian = (int) ($conn->query($query)->fetch_assoc()['total_penilaian'] ?? 0);

$completionPercent = 0;
if ($totalSupplier > 0 && $totalKriteria > 0) {
    $completionPercent = min(100, (int) round(($totalPenilaian / max(1, $totalSupplier * $totalKriteria)) * 100));
}

$kriteriaProgress = $totalKriteria > 0 ? 100 : 0;
$penilaianProgress = $totalPenilaian > 0 ? min(100, $completionPercent) : 0;
$laporanProgress = $totalPenilaian > 0 && $totalBobot > 0 ? min(100, $completionPercent + 10) : 20;

$flash = getFlash();
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>DSS Supplier Selection - Dashboard</title>
<!-- Material Symbols -->
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Google Fonts: Inter & JetBrains Mono -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=JetBrains+Mono:wght@500&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary": "#000000",
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
                        "secondary": "#0058be",
                        "surface-container-low": "#f2f4f6",
                        "primary-fixed-dim": "#bec6e0",
                        "on-error-container": "#93000a",
                        "on-primary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "surface-bright": "#f7f9fb",
                        "inverse-on-surface": "#eff1f3",
                        "background": "#f7f9fb",
                        "tertiary-fixed-dim": "#b7c8e1",
                        "error-container": "#ffdad6",
                        "on-secondary-fixed-variant": "#004395",
                        "secondary-fixed-dim": "#adc6ff",
                        "secondary-container": "#2170e4",
                        "outline": "#76777d",
                        "inverse-primary": "#bec6e0",
                        "on-secondary": "#ffffff",
                        "surface": "#f7f9fb",
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
                        "tertiary": "#000000",
                        "inverse-surface": "#2d3133",
                        "error": "#ba1a1a",
                        "primary-container": "#131b2e",
                        "on-tertiary": "#ffffff",
                        "primary-fixed": "#dae2fd"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "spacing": {
                        "nav-width": "260px",
                        "stack-lg": "24px",
                        "container-max": "1440px",
                        "gutter": "24px",
                        "stack-md": "16px",
                        "stack-sm": "8px",
                        "margin-page": "32px"
                    },
                    "fontFamily": {
                        "headline-md": ["Inter"],
                        "label-caps": ["Inter"],
                        "body-md": ["Inter"],
                        "display-lg": ["Inter"],
                        "body-lg": ["Inter"],
                        "headline-lg-mobile": ["Inter"],
                        "headline-sm": ["Inter"],
                        "data-mono": ["JetBrains Mono"]
                    },
                    "fontSize": {
                        "headline-md": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "display-lg": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "700"}],
                        "headline-sm": ["18px", {"lineHeight": "24px", "fontWeight": "600"}],
                        "data-mono": ["13px", {"lineHeight": "18px", "fontWeight": "500"}]
                    }
                },
            },
        }
    </script>
<style>
        body {
            background-color: #f7f9fb;
            color: #191c1e;
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .active-nav-item {
            /* Handled by style_active_navigation from JSON */
        }
        /* Custom scrollbar for DSS feel */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f2f4f6; }
        ::-webkit-scrollbar-thumb { background: #c6c6cd; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #76777d; }
    </style>
</head>
<body class="flex min-h-screen">
<!-- SideNavBar Component -->
<aside class="w-nav-width h-screen sticky top-0 left-0 bg-white text-black border-r border-outline-variant flex flex-col h-full py-stack-lg px-stack-md shrink-0">
<div class="flex items-center gap-stack-sm mb-stack-lg px-2">
<div class="w-10 h-10 bg-primary rounded flex items-center justify-center text-on-primary">
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
<!-- Main Content Area -->
<main class="flex-1 flex flex-col min-w-0">
<!-- TopNavBar Component -->
<header class="flex justify-between items-center h-16 px-margin-page sticky top-0 z-50 w-full bg-surface/80 backdrop-blur-md border-b border-outline-variant dark:border-outline">
<div class="flex items-center gap-4">
<h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface dark:text-on-surface">DSS Supplier Selection</h2>
<div class="h-6 w-[1px] bg-outline-variant"></div>
<span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">Dashboard Utama</span>
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
<a class="px-4 py-1.5 bg-primary text-on-primary rounded font-label-caps text-label-caps hover:opacity-80 transition-opacity uppercase" href="logout.php">
    Logout
</a>
</div>
</div>
</header>
<div class="p-margin-page space-y-stack-lg max-w-container-max mx-auto w-full">
<?php if ($flash): ?>
<div class="rounded-lg border px-4 py-3 <?php echo $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700'; ?>">
    <?php echo e($flash['message']); ?>
</div>
<?php endif; ?>
<!-- Welcome Header Section -->
<section class="grid grid-cols-1 lg:grid-cols-3 gap-stack-lg items-center">
<div class="lg:col-span-2">
<h3 class="font-display-lg text-display-lg text-primary mb-2">Selamat Datang, <?php echo e($_SESSION['nama']); ?></h3>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">
    Sistem Pendukung Keputusan (DSS) siap membantu Anda menyeleksi supplier terbaik berdasarkan kriteria yang telah ditentukan menggunakan metode <span class="font-bold">Simple Additive Weighting (SAW)</span>.
</p>
</div>
<div class="hidden lg:block relative h-32 rounded-xl overflow-hidden shadow-sm">
<div class="absolute inset-0 flex items-center justify-center p-4 bg-gradient-to-br from-secondary/10 to-transparent">
<div class="text-center">
<span class="material-symbols-outlined text-secondary text-4xl mb-1">precision_manufacturing</span>
<p class="font-label-caps text-label-caps text-secondary uppercase">Status Data: <?php echo $completionPercent >= 100 ? 'Lengkap' : 'Berjalan'; ?></p>
</div>
</div>
</div>
</section>
<!-- Stats Grid -->
<section class="grid grid-cols-1 md:grid-cols-3 gap-stack-lg">
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-stack-lg flex items-start gap-4 transition-all hover:shadow-md">
<div class="w-12 h-12 rounded-lg bg-secondary-container/20 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-3xl">groups</span>
</div>
<div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Supplier</p>
<h4 class="font-display-lg text-display-lg text-primary"><?php echo e($totalSupplier); ?></h4>
<div class="flex items-center gap-1 text-emerald-600 text-xs mt-1">
<span class="material-symbols-outlined text-sm">trending_up</span>
<span>Data supplier aktif di sistem</span>
</div>
</div>
</div>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-stack-lg flex items-start gap-4 transition-all hover:shadow-md">
<div class="w-12 h-12 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-600">
<span class="material-symbols-outlined text-3xl">list_alt</span>
</div>
<div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Total Kriteria</p>
<h4 class="font-display-lg text-display-lg text-primary"><?php echo e($totalKriteria); ?></h4>
<p class="text-xs text-on-surface-variant mt-1">Kriteria aktif dan bobot tersimpan: <?php echo e($totalBobot); ?></p>
</div>
</div>
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-stack-lg flex items-start gap-4 transition-all hover:shadow-md">
<div class="w-12 h-12 rounded-lg bg-emerald-600/10 flex items-center justify-center text-emerald-600">
<span class="material-symbols-outlined text-3xl">task_alt</span>
</div>
<div>
<p class="font-label-caps text-label-caps text-on-surface-variant uppercase">Penilaian Selesai</p>
<h4 class="font-display-lg text-display-lg text-primary"><?php echo e($totalPenilaian); ?></h4>
<div class="w-full bg-surface-container-high h-1.5 rounded-full mt-2">
<div class="bg-emerald-600 h-full rounded-full" style="width: <?php echo e($completionPercent); ?>%"></div>
</div>
</div>
</div>
</section>
<!-- Main Bento Section -->
<section class="grid grid-cols-1 lg:grid-cols-12 gap-stack-lg">
<!-- Top Suppliers List (Bento Large) -->
<div class="lg:col-span-8 bg-surface-container-lowest border border-outline-variant rounded-xl p-stack-lg shadow-sm flex flex-col">
<div class="mb-6">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Langkah-Langkah Proses SAW</h3>
<p class="text-xs text-on-surface-variant">Alur kerja Sistem Pendukung Keputusan (DSS)</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<!-- Step 1 -->
<div class="p-4 border border-outline-variant/50 rounded-lg hover:bg-surface-container-low/30 transition-colors">
<div class="flex items-center gap-3 mb-2">
<div class="w-8 h-8 rounded bg-secondary-container/20 flex items-center justify-center text-secondary">
<span class="material-symbols-outlined text-xl">inventory_2</span>
</div>
<h4 class="font-semibold text-body-lg">1. Input Data Supplier</h4>
</div>
<p class="text-xs text-on-surface-variant">Kelola daftar kandidat supplier yang akan dievaluasi dalam sistem.</p>
</div>
<!-- Step 2 -->
<div class="p-4 border border-outline-variant/50 rounded-lg hover:bg-surface-container-low/30 transition-colors">
<div class="flex items-center gap-3 mb-2">
<div class="w-8 h-8 rounded bg-amber-500/10 flex items-center justify-center text-amber-600">
<span class="material-symbols-outlined text-xl">fact_check</span>
</div>
<h4 class="font-semibold text-body-lg">2. Input Kriteria &amp; Bobot</h4>
</div>
<p class="text-xs text-on-surface-variant">Tentukan kriteria penilaian dan tingkat kepentingan (bobot) masing-masing.</p>
</div>
<!-- Step 3 -->
<div class="p-4 border border-outline-variant/50 rounded-lg hover:bg-surface-container-low/30 transition-colors">
<div class="flex items-center gap-3 mb-2">
<div class="w-8 h-8 rounded bg-emerald-600/10 flex items-center justify-center text-emerald-600">
<span class="material-symbols-outlined text-xl">thumbs_up_down</span>
</div>
<h4 class="font-semibold text-body-lg">3. Penilaian Supplier</h4>
</div>
<p class="text-xs text-on-surface-variant">Berikan skor untuk setiap supplier berdasarkan kriteria yang telah ditetapkan.</p>
</div>
<!-- Step 4 -->
<div class="p-4 border border-outline-variant/50 rounded-lg hover:bg-surface-container-low/30 transition-colors">
<div class="flex items-center gap-3 mb-2">
<div class="w-8 h-8 rounded bg-primary-container text-on-primary-container flex items-center justify-center">
<span class="material-symbols-outlined text-xl">calculate</span>
</div>
<h4 class="font-semibold text-body-lg">4. Proses Perhitungan</h4>
</div>
<p class="text-xs text-on-surface-variant">Kalkulasi otomatis menggunakan metode Normalisasi dan Preferensi SAW.</p>
</div>
<!-- Step 5 (Full Width) -->
<div class="md:col-span-2 p-4 border border-outline-variant/50 rounded-lg hover:bg-surface-container-low/30 transition-colors flex items-center justify-between">
<div class="flex items-center gap-3">
<div class="w-8 h-8 rounded bg-secondary text-on-secondary flex items-center justify-center">
<span class="material-symbols-outlined text-xl">assessment</span>
</div>
<div>
<h4 class="font-semibold text-body-lg">5. Hasil &amp; Laporan</h4>
<p class="text-xs text-on-surface-variant">Lihat peringkat akhir dan unduh laporan hasil seleksi terbaik.</p>
</div>
</div>
<a class="px-4 py-2 bg-secondary text-on-secondary rounded font-label-caps text-label-caps hover:opacity-90 transition-opacity" href="ranking.php">MULAI ANALISIS</a>
</div>
</div>
</div>
<!-- Analysis Overview (Bento Small) -->
<div class="lg:col-span-4 space-y-stack-lg">
<!-- Progress Card -->
<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-stack-lg shadow-sm">
<div class="flex justify-between items-start mb-4">
<h3 class="font-headline-sm text-headline-sm text-on-surface">Analisis Berjalan</h3>
<span class="material-symbols-outlined text-secondary">analytics</span>
</div>
<div class="space-y-4">
<div>
<div class="flex justify-between text-xs mb-1">
<span class="text-on-surface-variant">Input Data Kriteria</span>
<span class="font-bold"><?php echo e($kriteriaProgress); ?>%</span>
</div>
<div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
<div class="bg-primary h-full" style="width: <?php echo e($kriteriaProgress); ?>%"></div>
</div>
</div>
<div>
<div class="flex justify-between text-xs mb-1">
<span class="text-on-surface-variant">Penilaian Alternatif</span>
<span class="font-bold"><?php echo e($penilaianProgress); ?>%</span>
</div>
<div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
<div class="bg-secondary h-full" style="width: <?php echo e($penilaianProgress); ?>%"></div>
</div>
</div>
<div>
<div class="flex justify-between text-xs mb-1">
<span class="text-on-surface-variant">Laporan Akhir</span>
<span class="font-bold"><?php echo e($laporanProgress); ?>%</span>
</div>
<div class="w-full bg-surface-container-high h-2 rounded-full overflow-hidden">
<div class="bg-outline-variant h-full" style="width: <?php echo e($laporanProgress); ?>%"></div>
</div>
</div>
</div>
<a class="w-full mt-6 block text-center py-2 border border-secondary text-secondary rounded font-label-caps text-label-caps hover:bg-secondary/5 transition-colors" href="penilaian.php">
    LANJUTKAN PROSES
</a>
</div>
<!-- Quick Actions Card -->
<div class="bg-primary text-on-primary rounded-xl p-stack-lg shadow-md relative overflow-hidden group">
<div class="relative z-10">
<h3 class="font-headline-sm text-headline-sm mb-4">Aksi Cepat</h3>
<div class="grid grid-cols-2 gap-2">
<a class="p-3 bg-white/10 hover:bg-white/20 rounded flex flex-col items-center justify-center text-center transition-colors" href="supplier.php">
<span class="material-symbols-outlined mb-1">add_box</span>
<span class="text-[10px] uppercase font-bold tracking-tight">Tambah Supplier</span>
</a>
<a class="p-3 bg-white/10 hover:bg-white/20 rounded flex flex-col items-center justify-center text-center transition-colors" href="laporan.php">
<span class="material-symbols-outlined mb-1">download</span>
<span class="text-[10px] uppercase font-bold tracking-tight">Cetak Laporan</span>
</a>
</div>
</div>
<!-- Abstract deco element -->
<div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-2xl group-hover:bg-white/10 transition-all duration-700"></div>
</div>
</div>
</section>
<!-- Bottom Information Bar -->
<footer class="mt-8 pt-8 border-t border-outline-variant/30 flex flex-col md:flex-row justify-between items-center text-on-surface-variant gap-4">
<p class="font-body-md text-body-md">© 2024 SAW-DSS Supplier Selection. All analytics verified.</p>
<div class="flex gap-6 font-label-caps text-label-caps">
<a class="hover:text-primary transition-colors" href="#">Pusat Bantuan</a>
<a class="hover:text-primary transition-colors" href="#">Panduan Metode SAW</a>
<a class="hover:text-primary transition-colors" href="#">Log Sistem</a>
</div>
</footer>
</div>
</main>
<!-- FAB for Global Action - Contextual for Dashboard -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-secondary text-on-secondary rounded-full shadow-lg flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-[60]">
<span class="material-symbols-outlined text-2xl">add</span>
</button>
<script>
        // Simple Micro-interactions
        document.querySelectorAll('a, button').forEach(elem => {
            elem.addEventListener('click', (e) => {
                const ripple = document.createElement('div');
                ripple.className = 'ripple';
                // Logic for ripple effect could be added here if needed, 
                // but keeping it minimal as requested.
            });
        });

        // Mock chart animation or state changes
        setTimeout(() => {
            console.log("Dashboard analytics initialized.");
        }, 500);
    </script>
</body></html>