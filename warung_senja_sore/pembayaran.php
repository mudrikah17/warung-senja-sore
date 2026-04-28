<?php include 'config/koneksi.php'; ?>

<h2>Pembayaran</h2>

<form method="post" enctype="multipart/form-data">
    Upload Bukti:
    <input type="file" name="bukti"><br>
    <button type="submit">Kirim</button>
</form>

<?php
if (isset($_FILES['bukti'])) {
    $nama = $_FILES['bukti']['name'];
    move_uploaded_file($_FILES['bukti']['tmp_name'], "upload/".$nama);

    mysqli_query($conn, "INSERT INTO pembayaran (pesanan_id,bukti,status)
    VALUES (1,'$nama','Menunggu Verifikasi')");

    echo "Pembayaran berhasil dikirim";
}
?>