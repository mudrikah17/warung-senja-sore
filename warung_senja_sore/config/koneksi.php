<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "warung_senja_sore";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>   