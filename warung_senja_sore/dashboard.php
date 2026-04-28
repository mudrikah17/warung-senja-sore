<?php
session_start();
// Baris ini sangat penting, pastikan folder 'config' dan file 'koneksi.php' ADA.
include 'config/koneksi.php';

// Jika belum login, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Warung Senja Sore</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="dashboard-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="image/logo.png" class="logo-img" width="40" height="36" alt="Logo">
            <b>Senja<span>Sore</span></b>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="active">🏠 Dashboard</a>
            <a href="katalog.php">☕ Lihat Katalog</a>
            <a href="#">📦 Kelola Produk</a>
            <a href="#">📋 Pesanan Masuk</a>
            <a href="#">👥 Pelanggan</a>
        </nav>
        <div class="sidebar-footer">
            <a href="logout.php" style="color: #ff9f9f; text-decoration: none;">🚪 Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <header class="top-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0; font-size: 24px; color: #4b3621;">Ringkasan Bisnis</h1>
                <p style="margin: 5px 0 0; color: #888;">Halo, <b><?php echo $_SESSION['username']; ?></b>. Selamat datang kembali!</p>
            </div>
            <div class="admin-profile" style="background: #efebe9; padding: 10px 20px; border-radius: 8px;">
                Admin: <b>Astria Sari Widya</b>
            </div>
        </header>

        <div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px;">
            <div class="stat-card" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <small style="color: #888; text-transform: uppercase; font-size: 11px;">Total Produk</small>
                <?php 
                // Mengambil data dari database
                $p = mysqli_query($conn, "SELECT id FROM produk");
                $total = ($p) ? mysqli_num_rows($p) : 0;
                echo "<h3 style='margin: 10px 0 0; font-size: 28px; color: #4b3621;'>$total</h3>";
                ?>
            </div>
            <div class="stat-card" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <small style="color: #888; text-transform: uppercase; font-size: 11px;">Pesanan Hari Ini</small>
                <h3 style="margin: 10px 0 0; font-size: 28px; color: #4b3621;">12</h3> 
            </div>
            <div class="stat-card" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                <small style="color: #888; text-transform: uppercase; font-size: 11px;">Pendapatan</small>
                <h3 style="margin: 10px 0 0; font-size: 28px; color: #4b3621;">Rp 450.000</h3>
            </div>
        </div>

        <div class="data-section" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h3 style="margin-top: 0; margin-bottom: 20px; color: #4b3621;">Alamat Pengiriman Terbaru</h3>
            <table class="senja-table" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="text-align: left; border-bottom: 2px solid #f5f5f5;">
                        <th style="padding: 12px;">User ID</th>
                        <th style="padding: 12px;">Alamat</th>
                        <th style="padding: 12px;">Kota</th>
                        <th style="padding: 12px;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $ambil = mysqli_query($conn, "SELECT * FROM alamat ORDER BY id DESC LIMIT 5");
                    if($ambil && mysqli_num_rows($ambil) > 0) {
                        while($row = mysqli_fetch_array($ambil)){
                            echo "<tr style='border-bottom: 1px solid #f9f9f9;'>
                                    <td style='padding: 12px;'>#".$row['user_id']."</td>
                                    <td style='padding: 12px;'>".$row['alamat']."</td>
                                    <td style='padding: 12px;'>".$row['kota']."</td>
                                    <td style='padding: 12px;'><span style='background: #fff3e0; color: #ef6c00; padding: 5px 12px; border-radius: 20px; font-size: 12px;'>Dikemas</span></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='padding: 20px; text-align: center; color: #888;'>Belum ada data pesanan masuk.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

</body>
</html>