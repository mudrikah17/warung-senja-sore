<?php
include 'config/koneksi.php';

$user_id = 1;

$data = mysqli_query($conn, "
    SELECT keranjang.*, produk.harga 
    FROM keranjang 
    JOIN produk ON keranjang.produk_id = produk.id
");

$total = 0;

while ($d = mysqli_fetch_array($data)) {
    $total += $d['harga'] * $d['qty'];
}

mysqli_query($conn, "INSERT INTO pesanan (user_id,total,status) VALUES ('$user_id','$total','Menunggu')");

echo "Total bayar: Rp " . number_format($total);
?>

<br>
<a href="alamat.php">Lanjut isi alamat</a>