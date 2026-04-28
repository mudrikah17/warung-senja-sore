<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    $data = mysqli_fetch_assoc($query);

    if ($data && $password == $data['password']) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        header("location:dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Warung Senja Sore</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #5c3d2e, #a47148);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-box {
            background: #fff8f2;
            padding: 40px;
            border-radius: 20px;
            width: 320px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
            animation: fadeIn 0.8s ease;
        }

        .login-box h2 {
            margin-bottom: 20px;
            color: #5c3d2e;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .logo span {
            color: #ffb74d;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            background: #f5e6da;
            outline: none;
        }

        .login-box input:focus {
            border: 2px solid #a47148;
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background: #5c3d2e;
            color: white;
            border: none;
            border-radius: 10px;
            margin-top: 10px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-box button:hover {
            background: #a47148;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-top: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="login-box">
    <div class="logo">Senja<span>Sore</span></div>
    <h2>Selamat Datang ☕</h2>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Masuk</button>
    </form>

    <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
</div>

</body>
</html>