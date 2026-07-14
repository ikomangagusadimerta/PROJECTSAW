<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $nama = trim($_POST['nama'] ?? '');

    if ($username === '' || $password === '' || $nama === '') {
        $message = 'Semua field harus diisi.';
    } elseif (strlen($password) < 8) {
        $message = 'Password minimal 8 karakter.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->fetch_assoc()) {
            $message = 'Username sudah terdaftar, pilih username lain.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (username, password, nama, role) VALUES (?, ?, ?, ?)');
            $role = 'kepala_produksi';
            $stmt->bind_param('ssss', $username, $passwordHash, $nama, $role);
            $stmt->execute();
            header('Location: login.php?registered=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun Baru</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div style="min-height:100vh;display:grid;place-items:center;padding:24px;background:linear-gradient(135deg,#eef5ff,#f7fbff);">
        <div class="card" style="width:min(500px,100%);">
            <div class="hero" style="margin-bottom:18px;">
                <div>
                    <h2 style="margin:0 0 6px;">Buat Akun Baru</h2>
                    <p class="muted" style="margin:0;">Daftar pengguna untuk masuk ke sistem SAW</p>
                </div>
                <div class="brand-badge">🧑‍💼</div>
            </div>
            <?php if ($message): ?><div class="notice error"><?php echo e($message); ?></div><?php endif; ?>
            <form method="post" data-validate>
                <div style="display:grid;gap:16px;">
                    <div>
                        <label for="nama">Nama Lengkap</label>
                        <input id="nama" name="nama" placeholder="Kepala Produksi" required>
                    </div>
                    <div>
                        <label for="username">Username</label>
                        <input id="username" name="username" placeholder="kepalaproduksi" required>
                    </div>
                    <div>
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Daftar</button>
                </div>
            </form>
            <p class="small" style="margin-top:14px;">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>
        </div>
    </div>
    <script src="assets/js/app.js"></script>
</body>
</html>
