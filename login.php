<?php
session_start();
require_once 'db.php'; // Veritabanı bağlantısı

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $sifre = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM kullanicilar WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($sifre, $user['sifre'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_izin'] = $user['izin'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Hatalı şifre!";
        }
    } else {
        $error = "Kullanıcı bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
        }
        .login-container {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
            text-align: center;
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Giriş Yap</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-posta</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="E-posta adresinizi girin" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Şifrenizi girin" required>
            </div>
            <?php if (isset($error)) { echo "<p class='text-danger text-center'>$error</p>"; } ?>
            <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
