<?php 
include 'config/koneksi.php'; 
?>

<h2>Alamat Pengiriman - Warung Senja Sore</h2>

<form method="post">
    <input type="text" name="alamat" placeholder="Alamat Lengkap" required><br>
    <input type="text" name="kota" placeholder="Kota" required><br>
    <input type="text" name="kode_pos" placeholder="Kode Pos" required><br>
    <button type="submit" name="submit_alamat">Simpan & Lanjut ke Pembayaran</button>
</form>

<?php
if (isset($_POST['submit_alamat'])) {
    // 1. Ambil data dari form
    $almt = $_POST['alamat'];
    $kta  = $_POST['kota'];
    $kpos = $_POST['kode_pos'];
   session_start();
$uid = $_SESSION['user_id'];// ID User sementara

    // 2. Masukkan ke database
    $query = mysqli_query($conn, "INSERT INTO alamat (user_id, alamat, kota, kode_pos) 
                                  VALUES ('$uid', '$almt', '$kta', '$kpos')");

    if ($query) {
        // 3. Kalau berhasil, langsung pindah ke halaman pembayaran.php
        echo "<script>
                alert('Alamat berhasil disimpan!');
                window.location='pembayaran.php';
              </script>";
    } else {
        echo "Gagal simpan: " . mysqli_error($conn);
    }
}
?>