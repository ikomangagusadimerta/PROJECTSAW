<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id'])) {
    redirect('dashboard.php');
}

$message = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $conn->prepare('SELECT id, username, password, nama FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama'] = $user['nama'];
        redirect('dashboard.php');
    }

    $message = 'Username atau password salah.';
}

if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $successMessage = 'Akun berhasil dibuat. Silakan masuk dengan akun Anda.';
}
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login Kepala Produksi - SAW-DSS</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "#0058be",
              secondary: "#0058be",
              "on-primary": "#ffffff",
              "on-surface": "#191c1e",
              "on-surface-variant": "#45464d",
              "outline-variant": "#c6c6cd",
              background: "#f7f9fb",
              surface: "#ffffff"
            },
            borderRadius: {
              DEFAULT: "0.125rem",
              lg: "0.25rem",
              xl: "0.5rem",
              full: "0.75rem"
            },
            spacing: {
              "stack-lg": "24px",
              "stack-md": "16px",
              "stack-sm": "8px"
            },
            fontFamily: {
              "headline-md": ["Inter"],
              "headline-sm": ["Inter"],
              "label-caps": ["Inter"],
              "body-md": ["Inter"]
            },
            fontSize: {
              "headline-md": ["24px", { lineHeight: "32px", letterSpacing: "-0.01em", fontWeight: "600" }],
              "headline-sm": ["18px", { lineHeight: "24px", fontWeight: "600" }],
              "label-caps": ["12px", { lineHeight: "16px", letterSpacing: "0.05em", fontWeight: "600" }],
              "body-md": ["14px", { lineHeight: "20px", fontWeight: "400" }]
            }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
    </style>
</head>
<body class="bg-background text-on-background min-h-screen flex flex-col items-center justify-center relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] rounded-full bg-secondary/5 blur-[120px]"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] rounded-full bg-primary/5 blur-[120px]"></div>
    </div>

    <main class="relative z-10 w-full max-w-[480px] px-stack-md flex flex-col items-center">
        <div class="mb-stack-lg flex flex-col items-center text-center">
            <div class="mb-4 w-16 h-16 bg-primary flex items-center justify-center rounded-xl shadow-lg">
                <span class="material-symbols-outlined text-on-primary text-[32px]" data-icon="analytics">analytics</span>
            </div>
            <h2 class="font-headline-sm text-headline-sm text-on-surface-variant mb-1">SAW-DSS</h2>
            <p class="font-label-caps text-label-caps text-on-primary-container tracking-widest">Supplier Selection System</p>
        </div>

        <div class="w-full glass-panel border border-outline-variant p-stack-lg rounded-xl shadow-sm">
            <header class="mb-stack-lg">
                <h1 class="font-headline-md text-headline-md text-primary mb-2">Login Kepala Produksi</h1>
                <p class="font-body-md text-body-md text-on-surface-variant">Masukkan kredensial departemen produksi untuk mengakses dashboard analisis supplier.</p>
            </header>

            <?php if ($successMessage): ?>
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    <?php echo e($successMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($message): ?>
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <?php echo e($message); ?>
                </div>
            <?php endif; ?>

            <form class="space-y-stack-md" method="post" action="login.php">
                <div class="flex flex-col gap-1.5">
                    <label class="font-label-caps text-label-caps text-on-surface-variant" for="username">Username</label>
                    <div class="relative group">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-[20px] group-focus-within:text-secondary transition-colors" data-icon="person">person</span>
                        <input class="w-full pl-10 pr-4 py-3 bg-white border border-outline-variant rounded-lg text-body-md focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all" id="username" name="username" placeholder="Masukkan username" required type="text" value="<?php echo e($_POST['username'] ?? ''); ?>"/>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5">
                    <div class="flex justify-between items-center">
                        <label class="font-label-caps text-label-caps text-on-surface-variant" for="password">Password</label>
                        <a class="font-label-caps text-label-caps text-secondary hover:underline" href="register.php">Daftar?</a>
                    </div>
                    <div class="relative group">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-[20px] group-focus-within:text-secondary transition-colors" data-icon="lock">lock</span>
                        <input class="w-full pl-10 pr-4 py-3 bg-white border border-outline-variant rounded-lg text-body-md focus:outline-none focus:ring-2 focus:ring-secondary/20 focus:border-secondary transition-all" id="password" name="password" placeholder="••••••••" required type="password"/>
                    </div>
                </div>

                <div class="flex items-center gap-2 py-1">
                    <input class="w-4 h-4 rounded border-outline-variant text-secondary focus:ring-secondary" id="remember" type="checkbox"/>
                    <label class="font-body-md text-body-md text-on-surface-variant cursor-pointer" for="remember">Tetap masuk pada sesi ini</label>
                </div>

                <button class="w-full mt-2 py-4 bg-primary text-on-primary font-headline-sm text-headline-sm rounded-lg hover:bg-[#004395] transition-all active:scale-[0.98] flex items-center justify-center gap-2 group" type="submit">
                    Masuk
                    <span class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform" data-icon="arrow_forward">arrow_forward</span>
                </button>
            </form>

            <p class="mt-4 text-center text-sm text-on-surface-variant">
                Belum punya akun? <a class="font-semibold text-secondary hover:underline" href="register.php">Buat akun baru</a>
            </p>
            <p class="mt-2 text-center text-xs text-on-surface-variant/70">Default login: admin / admin123</p>
        </div>

        <footer class="mt-stack-lg text-center">
            <p class="font-label-caps text-label-caps text-on-surface-variant/60">
                Decision Support System © 2024 • Security Level: High
            </p>
        </footer>
    </main>
</body>
</html>
