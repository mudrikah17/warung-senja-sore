<?php
session_start();
include 'config/koneksi.php';

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Proses action dari form atau AJAX
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action) {
    header('Content-Type: application/json');
    
    switch($action) {
        case 'add':
            $id = $_POST['id'];
            $name = $_POST['name'];
            $price = $_POST['price'];
            $foto = $_POST['foto'] ?? '';
            
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    $item['quantity']++;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => 1,
                    'foto' => $foto
                ];
            }
            echo json_encode(['success' => true, 'cart' => $_SESSION['cart']]);
            exit;
            
        case 'get':
            echo json_encode($_SESSION['cart']);
            exit;
            
        case 'update':
            $id = $_POST['id'];
            $quantity = (int)$_POST['quantity'];
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) {
                    if ($quantity <= 0) {
                        $item['quantity'] = 0;
                    } else {
                        $item['quantity'] = $quantity;
                    }
                    break;
                }
            }
            // Hapus item dengan quantity 0
            $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], fn($item) => $item['quantity'] > 0));
            echo json_encode(['success' => true]);
            exit;
            
        case 'remove':
            $id = $_GET['id'];
            $_SESSION['cart'] = array_values(array_filter($_SESSION['cart'], fn($item) => $item['id'] != $id));
            echo json_encode(['success' => true]);
            exit;
            
        case 'clear':
            $_SESSION['cart'] = [];
            echo json_encode(['success' => true]);
            exit;
    }
}

// Proses checkout (form submit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    // Simpan pesanan ke database (opsional)
    $nama_customer = $_POST['nama_customer'] ?? '';
    $alamat = $_POST['alamat'] ?? '';
    $total = $_POST['total'] ?? 0;
    
    // Di sini Anda bisa menyimpan ke tabel pesanan
    // Contoh sederhana: tampilkan pesan sukses dan kosongkan keranjang
    
    $_SESSION['cart'] = [];
    $checkout_success = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | Warung Senja Sore</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style tambahan untuk halaman keranjang */
        .cart-page {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .cart-table {
            width: 100%;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .cart-table th {
            background: #8b5e3c;
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .cart-product-img {
            width: 70px;
            height: 70px;
            background: #f0e4d0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .cart-product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        
        .btn-update, .btn-remove {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 0 3px;
        }
        
        .btn-update {
            background: #3498db;
            color: white;
        }
        
        .btn-remove {
            background: #e74c3c;
            color: white;
        }
        
        .cart-summary {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .checkout-form {
            margin-top: 20px;
        }
        
        .checkout-form input, .checkout-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 10px;
            margin-bottom: 15px;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-checkout {
            background: #27ae60;
            color: white;
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
        }
        
        .empty-cart-large {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 20px;
        }
        
        .empty-cart-large i {
            font-size: 4rem;
            color: #c9a87b;
            margin-bottom: 20px;
        }
        
        .btn-continue {
            display: inline-block;
            background: #8b5e3c;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            text-decoration: none;
            margin-top: 20px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <i class="fas fa-sun"></i>
        <span>Senja<span>Sore</span></span>
    </div>
    <div class="nav-links">
        <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
        <a href="katalog.php"><i class="fas fa-box"></i> Produk</a>
        <a href="keranjang.php" class="active"><i class="fas fa-shopping-cart"></i> Keranjang</a>
    </div>
</nav>

<div class="container cart-page">
    <h1><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h1>
    
    <?php if (isset($checkout_success) && $checkout_success): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> Terima kasih! Pesanan Anda telah diproses.
        </div>
    <?php endif; ?>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <div class="empty-cart-large">
            <i class="fas fa-shopping-cart"></i>
            <h3>Keranjang Anda kosong</h3>
            <p>Yuk, belanja dulu di katalog produk kami!</p>
            <a href="katalog.php" class="btn-continue"><i class="fas fa-store"></i> Lanjut Belanja</a>
        </div>
    <?php else: 
        $total = 0;
    ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Nama</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $item): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <tr id="row-<?php echo $item['id']; ?>">
                        <td>
                            <div class="cart-product-img">
                                <?php if ($item['foto']): ?>
                                    <img src="images/<?php echo $item['foto']; ?>" alt="<?php echo $item['name']; ?>">
                                <?php else: ?>
                                    <span>📷</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                        <td>
                            <input type="number" class="quantity-input" id="qty-<?php echo $item['id']; ?>" 
                                   value="<?php echo $item['quantity']; ?>" min="1">
                        </td>
                        <td class="subtotal-<?php echo $item['id']; ?>">
                            Rp <?php echo number_format($subtotal, 0, ',', '.'); ?>
                        </td>
                        <td>
                            <button class="btn-update" onclick="updateCart(<?php echo $item['id']; ?>)">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                            <button class="btn-remove" onclick="removeFromCart(<?php echo $item['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="cart-summary">
            <h3>Ringkasan Belanja</h3>
            <div style="display: flex; justify-content: space-between; margin: 15px 0;">
                <span>Total Belanja:</span>
                <strong style="font-size: 1.5rem; color: #d2691e;" id="totalAmount">
                    Rp <?php echo number_format($total, 0, ',', '.'); ?>
                </strong>
            </div>
            
            <form method="POST" class="checkout-form" onsubmit="return validateForm()">
                <input type="text" name="nama_customer" placeholder="Nama Lengkap" required>
                <input type="email" name="email" placeholder="Email">
                <textarea name="alamat" rows="3" placeholder="Alamat Lengkap" required></textarea>
                <input type="hidden" name="total" id="totalHidden" value="<?php echo $total; ?>">
                <input type="hidden" name="checkout" value="1">
                <button type="submit" class="btn-checkout">
                    <i class="fas fa-credit-card"></i> Checkout Sekarang
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
// Fungsi untuk update keranjang via AJAX (tanpa reload halaman)
function updateCart(id) {
    const qtyInput = document.getElementById('qty-' + id);
    const quantity = qtyInput.value;
    
    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&id=${id}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload untuk refresh tampilan
        }
    });
}

function removeFromCart(id) {
    if (confirm('Hapus produk dari keranjang?')) {
        fetch(window.location.href + '?action=remove&id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function validateForm() {
    const nama = document.querySelector('input[name="nama_customer"]').value;
    const alamat = document.querySelector('textarea[name="alamat"]').value;
    
    if (!nama || !alamat) {
        alert('Harap isi nama dan alamat lengkap!');
        return false;
    }
    return true;
}
</script>

</body>
</html>