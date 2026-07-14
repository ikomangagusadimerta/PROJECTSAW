<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$editSupplier = null;
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    if ($_GET['action'] === 'edit') {
        $stmt = $conn->prepare('SELECT * FROM supplier WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $editSupplier = $stmt->get_result()->fetch_assoc();
    } elseif ($_GET['action'] === 'delete') {
        $stmt = $conn->prepare('DELETE FROM supplier WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        setFlash('success', 'Supplier berhasil dihapus.');
        redirect('supplier.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplierId = (int) ($_POST['supplier_id'] ?? 0);
    $nama = trim($_POST['nama_supplier'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $kontak = trim($_POST['kontak'] ?? '');

    if ($nama !== '' && $alamat !== '' && $kontak !== '') {
        if ($supplierId > 0) {
            $stmt = $conn->prepare('UPDATE supplier SET nama_supplier = ?, alamat = ?, kontak = ? WHERE id = ?');
            $stmt->bind_param('sssi', $nama, $alamat, $kontak, $supplierId);
            $stmt->execute();
            setFlash('success', 'Supplier berhasil diperbarui.');
        } else {
            $stmt = $conn->prepare('INSERT INTO supplier (nama_supplier, alamat, kontak) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $nama, $alamat, $kontak);
            $stmt->execute();
            setFlash('success', 'Supplier berhasil ditambahkan.');
        }
        redirect('supplier.php');
    }

    setFlash('error', 'Semua field harus diisi.');
    redirect('supplier.php');
}

$result = $conn->query('SELECT * FROM supplier ORDER BY id DESC');
$rows = $result->fetch_all(MYSQLI_ASSOC);
$flash = getFlash();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Input Supplier - SAW-DSS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=block" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#0a0a0a",
                        secondary: "#0058be",
                        "on-secondary-container": "#fefcff",
                        "outline-variant": "#c6c6cd",
                        "surface-container-high": "#e6e8ea",
                        "on-surface": "#191c1e",
                        "on-surface-variant": "#45464d",
                        background: "#f7f9fb",
                        surface: "#ffffff",
                        "surface-container-low": "#f2f4f6",
                        "surface-container-lowest": "#ffffff",
                        error: "#ba1a1a"
                    },
                    borderRadius: {
                        lg: "0.25rem",
                        xl: "0.5rem",
                        full: "0.75rem"
                    },
                    spacing: {
                        "nav-width": "260px",
                        "stack-lg": "24px",
                        "container-max": "1440px",
                        "gutter": "24px",
                        "stack-md": "16px",
                        "stack-sm": "8px",
                        "margin-page": "32px"
                    },
                    fontFamily: {
                        "headline-sm": ["Inter"],
                        "label-caps": ["Inter"],
                        "body-md": ["Inter"],
                        "data-mono": ["JetBrains Mono"]
                    },
                    fontSize: {
                        "headline-sm": ["18px", { lineHeight: "24px", fontWeight: "600" }],
                        "label-caps": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
                        "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }],
                        "data-mono": ["13px", { lineHeight: "18px", fontWeight: "500" }]
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="flex min-h-screen bg-background text-on-surface font-body-md">
<div class="flex h-screen w-full">
    <aside class="w-nav-width h-screen sticky top-0 left-0 bg-white text-black border-r border-outline-variant flex flex-col py-stack-lg px-stack-md shrink-0">
        <div class="flex items-center gap-stack-sm mb-stack-lg px-2">
            <div class="w-10 h-10 bg-black rounded flex items-center justify-center text-white">
                <span class="material-symbols-outlined">analytics</span>
            </div>
            <div>
                <h1 class="font-headline-sm text-headline-sm font-bold text-primary">SAW-DSS</h1>
                <p class="text-[10px] uppercase tracking-widest text-on-surface-variant font-semibold">Supplier Selection</p>
            </div>
        </div>
        <nav class="flex-1 space-y-1 overflow-y-auto">
            <a class="flex items-center gap-3 px-3 py-2.5 text-black-300 hover:bg-gray-800 transition-colors duration-200 rounded-lg" href="dashboard.php">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-body-md text-body-md">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 bg-gray-900 text-white rounded-lg font-semibold transform duration-200" href="supplier.php">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">inventory_2</span>
                <span class="font-body-md text-body-md">Input Supplier</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-black-300 hover:bg-gray-800 transition-colors duration-200 rounded-lg" href="kriteria.php">
                <span class="material-symbols-outlined">fact_check</span>
                <span class="font-body-md text-body-md">Input Kriteria</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-black-300 hover:bg-gray-800 transition-colors duration-200 rounded-lg" href="bobot.php">
                <span class="material-symbols-outlined">straighten</span>
                <span class="font-body-md text-body-md">Input Bobot</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-black-300 hover:bg-gray-800 transition-colors duration-200 rounded-lg" href="penilaian.php">
                <span class="material-symbols-outlined">thumbs_up_down</span>
                <span class="font-body-md text-body-md">Penilaian Supplier</span>
            </a>
            <div class="py-2"><div class="h-[1px] bg-outline-variant/30 mx-3"></div></div>
            <a class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant dark:text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200 rounded-lg" href="normalisasi.php">
                <span class="material-symbols-outlined">calculate</span>
                <span class="font-body-md text-body-md">Normalisasi</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant dark:text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200 rounded-lg" href="preferensi.php">
                <span class="material-symbols-outlined">star</span>
                <span class="font-body-md text-body-md">Preferensi</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant dark:text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200 rounded-lg" href="ranking.php">
                <span class="material-symbols-outlined">leaderboard</span>
                <span class="font-body-md text-body-md">Ranking</span>
            </a>
            <a class="flex items-center gap-3 px-3 py-2.5 text-on-surface-variant dark:text-on-surface-variant hover:bg-surface-container-high transition-colors duration-200 rounded-lg" href="laporan.php">
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

    <main class="flex-1 flex flex-col min-w-0">
        <header class="flex justify-between items-center h-16 px-margin-page sticky top-0 z-50 w-full bg-surface/80 backdrop-blur-md border-b border-outline-variant">
            <div class="flex items-center gap-4">
                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-surface">DSS Supplier Selection</h2>
                <div class="h-6 w-[1px] bg-outline-variant"></div>
                <span class="font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">Input Supplier</span>
            </div>
            <div class="flex items-center gap-stack-md">
                <div class="flex items-center gap-3 pl-2">
                    <div class="text-right">
                        <p class="font-label-caps text-label-caps text-on-surface font-bold"><?php echo e($_SESSION['nama']); ?></p>
                        <p class="text-[10px] text-on-surface-variant">Active Now</p>
                    </div>
                    <a class="px-4 py-1.5 bg-primary text-white rounded font-label-caps text-label-caps hover:opacity-80 transition-opacity uppercase" href="logout.php">
                        Logout
                    </a>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-margin-page space-y-gutter">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter max-w-container-max mx-auto w-full">
                <div class="lg:col-span-4 bg-surface-container-lowest border border-outline-variant rounded-xl p-6 flex flex-col">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface mb-6"><?php echo $editSupplier ? 'Perbarui Supplier' : 'Tambah Supplier Baru'; ?></h3>
                    <?php if ($flash): ?>
                        <div class="mb-4 rounded-lg border px-3 py-2 text-sm <?php echo $flash['type'] === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700'; ?>">
                            <?php echo e($flash['message']); ?>
                        </div>
                    <?php endif; ?>
                    <form class="space-y-6" method="post" action="supplier.php">
                        <input type="hidden" name="supplier_id" value="<?php echo e($editSupplier['id'] ?? ''); ?>" />
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2" for="nama_supplier">Nama Supplier</label>
                                <input class="w-full px-4 py-2.5 border border-outline-variant rounded bg-surface-container-low focus:border-secondary outline-none transition-all" id="nama_supplier" name="nama_supplier" type="text" value="<?php echo e($editSupplier['nama_supplier'] ?? ''); ?>" required/>
                            </div>
                            <div>
                                <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2" for="kontak">Kontak</label>
                                <input class="w-full px-4 py-2.5 border border-outline-variant rounded bg-surface-container-low focus:border-secondary outline-none transition-all" id="kontak" name="kontak" type="text" value="<?php echo e($editSupplier['kontak'] ?? ''); ?>" required/>
                            </div>
                        </div>
                        <div>
                            <label class="block font-label-caps text-label-caps text-on-surface-variant mb-2" for="alamat">Alamat</label>
                            <textarea class="w-full px-4 py-2.5 border border-outline-variant rounded bg-surface-container-low focus:border-secondary outline-none transition-all resize-none" id="alamat" name="alamat" rows="6" required><?php echo e($editSupplier['alamat'] ?? ''); ?></textarea>
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button class="bg-secondary text-white px-6 py-2.5 rounded font-semibold hover:opacity-80 transition-all active:scale-95" type="submit"><?php echo $editSupplier ? 'Perbarui Supplier' : 'Simpan Supplier'; ?></button>
                            <?php if ($editSupplier): ?>
                                <a class="px-6 py-2.5 border border-outline-variant rounded font-semibold hover:bg-surface-container-high transition-all" href="supplier.php">Batal</a>
                            <?php else: ?>
                                <button class="px-6 py-2.5 border border-outline-variant rounded font-semibold hover:bg-surface-container-high transition-all" type="reset">Reset</button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-8 bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-outline-variant">
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Daftar Supplier</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low">
                                    <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">NO</th>
                                    <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">NAMA SUPPLIER</th>
                                    <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">ALAMAT</th>
                                    <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">KONTAK</th>
                                    <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant border-b border-outline-variant">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant">
                                <?php if ($rows): foreach ($rows as $idx => $row): ?>
                                    <tr class="hover:bg-surface-container-low/50 transition-colors">
                                        <td class="px-6 py-6 font-data-mono text-data-mono"><?php echo (int) ($idx + 1); ?></td>
                                        <td class="px-6 py-6 font-semibold"><?php echo e($row['nama_supplier']); ?></td>
                                        <td class="px-6 py-6 text-on-surface-variant"><?php echo e($row['alamat']); ?></td>
                                        <td class="px-6 py-6 font-data-mono"><?php echo e($row['kontak']); ?></td>
                                        <td class="px-6 py-6">
                                            <div class="flex flex-col gap-2">
                                                <a class="px-4 py-1 border border-outline-variant rounded text-xs font-bold hover:bg-surface-container-high" href="supplier.php?action=edit&id=<?php echo (int) $row['id']; ?>">Edit</a>
                                                <a class="px-4 py-1 bg-error text-white rounded text-xs font-bold hover:opacity-80" href="supplier.php?action=delete&id=<?php echo (int) $row['id']; ?>" onclick="return confirm('Hapus supplier ini?');">Hapus</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant">Belum ada supplier.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>
