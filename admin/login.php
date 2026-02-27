<?php
// ========================================
// LOGIN PAGE
// ========================================

require_once __DIR__ . '/auth.php';

// Already logged in?
if (isLoggedIn()) {
    redirect(BASE_URL . 'admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attemptLogin($username, $password)) {
        redirect(BASE_URL . 'admin/index.php');
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Nada Agen Sosis</title>
    <link rel="icon" type="image/png" href="../img/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: rgba(255, 255, 255, .04);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .08);
            border-radius: 24px;
            padding: 48px 36px;
            text-align: center;
        }

        .login-logo {
            width: 80px;
            height: auto;
            margin-bottom: 16px;
        }

        .login-card h1 {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .login-card h1 span {
            color: #3B82F6;
        }

        .login-card .subtitle {
            color: #94A3B8;
            font-size: .9rem;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .form-group label {
            display: block;
            color: #CBD5E1;
            font-size: .85rem;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748B;
            font-size: 1.1rem;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 16px 13px 44px;
            background: rgba(255, 255, 255, .06);
            border: 1.5px solid rgba(255, 255, 255, .1);
            border-radius: 12px;
            color: #fff;
            font-size: .95rem;
            font-family: inherit;
            transition: all .3s ease;
        }

        .input-wrap input:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, .15);
            background: rgba(255, 255, 255, .08);
        }

        .input-wrap input::placeholder {
            color: #475569;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2563EB, #0EA5E9);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all .3s ease;
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, .4);
        }

        .error-msg {
            background: rgba(239, 68, 68, .15);
            border: 1px solid rgba(239, 68, 68, .3);
            color: #FCA5A5;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: .88rem;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748B;
            font-size: .85rem;
            text-decoration: none;
            margin-top: 24px;
            transition: color .3s;
        }

        .back-link:hover {
            color: #3B82F6;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <img src="../img/logo.png" alt="Logo" class="login-logo">
            <h1>Nada <span>Agen Sosis</span></h1>
            <p class="subtitle">Admin Dashboard</p>

            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    <?= h($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <i class="bi bi-person-fill"></i>
                        <input type="text" id="username" name="username" placeholder="Masukkan username" required
                            autofocus>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <a href="../index.html" class="back-link">
                <i class="bi bi-arrow-left"></i> Kembali ke Website
            </a>
        </div>
    </div>
</body>

</html>