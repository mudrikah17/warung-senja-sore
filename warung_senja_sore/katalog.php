<?php
session_start();
include 'config/koneksi.php';

// Inisialisasi keranjang session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Ambil SEMUA produk dari database
$query = mysqli_query($conn, "SELECT * FROM produk ORDER BY id DESC");
$products = [];
while ($row = mysqli_fetch_assoc($query)) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk | Warung Senja Sore</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo">
        <i class="fas fa-sun"></i>
        <span>Senja<span>Sore</span></span>
    </div>
    <div class="nav-links">
        <a href="index.php"><i class="fas fa-home"></i> Beranda</a>
        <a href="katalog.php" class="active"><i class="fas fa-box"></i> Produk</a>
        <a href="javascript:void(0)" class="cart-link" id="cartIcon">
            <i class="fas fa-shopping-cart"></i> Keranjang
            <span class="cart-count" id="cartCount">0</span>
        </a>
    </div>
</nav>

<div class="container">
    <div class="hero-section">
        <h1>Katalog Produk</h1>
        <p>Temukan aneka jajanan, jastip, dan baju terbaik hanya di Senja Sore</p>
    </div>

    <!-- Filter Kategori - TANPA RELOAD -->
    <div class="filter-nav">
        <button class="filter-btn active" data-kategori="all">📦 Semua Produk</button>
        <button class="filter-btn" data-kategori="Jajanan">🍪 Jajanan</button>
        <button class="filter-btn" data-kategori="Jastip">📦 Jastip</button>
        <button class="filter-btn" data-kategori="Baju">👕 Baju</button>
    </div>

    <!-- Grid Produk - akan diisi JavaScript -->
    <div class="produk-grid" id="produkGrid"></div>
</div>

<!-- Sidebar Keranjang -->
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <h3><i class="fas fa-shopping-bag"></i> Keranjang Belanja</h3>
        <button class="close-cart" id="closeCartBtn">&times;</button>
    </div>
    <div class="cart-body" id="cartBody">
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Keranjang masih kosong</p>
        </div>
    </div>
    <div class="cart-footer">
        <div class="cart-total">
            <span>Total:</span>
            <span id="cartTotal">Rp 0</span>
        </div>
        <button class="checkout-btn" id="checkoutBtn">
            <i class="fas fa-credit-card"></i> Checkout
        </button>
    </div>
</div>

<!-- Floating Cart Button untuk Mobile -->
<button class="float-cart-btn" id="floatCartBtn">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-badge" id="floatCartBadge">0</span>
</button>

<script>
// ==================== DATA PRODUK DARI PHP ====================
const products = <?php echo json_encode($products); ?>;

// ==================== STATE ====================
let currentCategory = 'all';
let cart = [];

// ==================== LOAD CART DARI SESSION PHP ====================
async function loadCartFromServer() {
    try {
        const response = await fetch('ajax_cart.php?action=get');
        const data = await response.json();
        if (data.success) {
            cart = data.cart;
            updateCartUI();
        }
    } catch(e) {
        // Fallback ke localStorage
        const saved = localStorage.getItem('senjaCart');
        if (saved) {
            cart = JSON.parse(saved);
            updateCartUI();
        }
    }
}

// ==================== SAVE CART KE SERVER ====================
async function saveCartToServer() {
    try {
        await fetch('ajax_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'sync', cart: cart })
        });
        localStorage.setItem('senjaCart', JSON.stringify(cart));
    } catch(e) {
        localStorage.setItem('senjaCart', JSON.stringify(cart));
    }
}

// ==================== FORMAT RUPIAH ====================
function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// ==================== RENDER PRODUK (FILTER LANGSUNG) ====================
function renderProducts() {
    const grid = document.getElementById('produkGrid');
    
    let filteredProducts = products;
    if (currentCategory !== 'all') {
        filteredProducts = products.filter(p => p.kategori === currentCategory);
    }
    
    if (filteredProducts.length === 0) {
        grid.innerHTML = `<div class="empty-message">
                            <i class="fas fa-box-open"></i>
                            <p>Belum ada produk di kategori ${currentCategory}</p>
                          </div>`;
        return;
    }
    
    grid.innerHTML = filteredProducts.map(product => `
        <div class="card">
            <div class="foto-produk">
                ${product.foto ? 
                    `<img src="images/${product.foto}" alt="${product.nama_produk}">` : 
                    `<div class="no-image">📷</div>`
                }
            </div>
            <div class="info-produk">
                <span class="badge-kategori">${product.kategori}</span>
                <h3>${product.nama_produk}</h3>
                <p class="harga">${formatRupiah(product.harga)}</p>
                <div class="btn-group">
                    <a href="detail.php?id=${product.id}" class="btn-detail">Detail</a>
                    <button class="btn-cart" onclick="addToCart(${product.id})">
                        <i class="fas fa-cart-plus"></i> Keranjang
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// ==================== TAMBAH KE KERANJANG ====================
function addToCart(productId) {
    const product = products.find(p => p.id === productId);
    if (!product) return;
    
    const existing = cart.find(item => item.id === productId);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({
            id: product.id,
            name: product.nama_produk,
            price: product.harga,
            quantity: 1,
            foto: product.foto
        });
    }
    
    saveCartToServer();
    updateCartUI();
    showNotification(`${product.nama_produk} ditambahkan ke keranjang`);
}

// ==================== UPDATE UI KERANJANG ====================
function updateCartUI() {
    const cartBody = document.getElementById('cartBody');
    const cartTotalSpan = document.getElementById('cartTotal');
    const cartCountSpans = document.querySelectorAll('#cartCount, #floatCartBadge');
    
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    cartCountSpans.forEach(span => {
        if (span) span.textContent = totalItems;
    });
    
    cartTotalSpan.textContent = formatRupiah(totalPrice);
    
    if (cart.length === 0) {
        cartBody.innerHTML = `<div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <p>Keranjang masih kosong</p>
                              </div>`;
        return;
    }
    
    cartBody.innerHTML = cart.map(item => `
        <div class="cart-item">
            <div class="cart-item-img">
                ${item.foto ? 
                    `<img src="images/${item.foto}" alt="${item.name}">` : 
                    `<span>📷</span>`
                }
            </div>
            <div class="cart-item-details">
                <div class="cart-item-title">${item.name}</div>
                <div class="cart-item-price">${formatRupiah(item.price)}</div>
            </div>
            <div class="cart-item-actions">
                <button class="quantity-btn" onclick="updateQuantity(${item.id}, -1)">-</button>
                <span class="item-quantity">${item.quantity}</span>
                <button class="quantity-btn" onclick="updateQuantity(${item.id}, 1)">+</button>
                <i class="fas fa-trash remove-item" onclick="removeFromCart(${item.id})"></i>
            </div>
        </div>
    `).join('');
}

// ==================== UPDATE JUMLAH ====================
function updateQuantity(productId, change) {
    const item = cart.find(i => i.id === productId);
    if (item) {
        const newQty = item.quantity + change;
        if (newQty <= 0) {
            removeFromCart(productId);
        } else {
            item.quantity = newQty;
            saveCartToServer();
            updateCartUI();
        }
    }
}

// ==================== HAPUS DARI KERANJANG ====================
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    saveCartToServer();
    updateCartUI();
    showNotification('Produk dihapus dari keranjang');
}

// ==================== NOTIFIKASI ====================
function showNotification(msg) {
    const notif = document.createElement('div');
    notif.className = 'notification';
    notif.innerHTML = `<i class="fas fa-check-circle"></i> ${msg}`;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 2000);
}

// ==================== TOGGLE SIDEBAR ====================
function toggleCart() {
    document.getElementById('cartSidebar').classList.toggle('open');
}

// ==================== CHECKOUT ====================
function checkout() {
    if (cart.length === 0) {
        showNotification('Keranjang masih kosong!');
        return;
    }
    window.location.href = 'keranjang.php';
}

// ==================== EVENT LISTENERS ====================
document.addEventListener('DOMContentLoaded', () => {
    loadCartFromServer();
    renderProducts();
    
    // Filter Kategori - TANPA RELOAD
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.getAttribute('data-kategori');
            renderProducts(); // LANGSUNG RENDER ULANG
        });
    });
    
    // Event keranjang
    document.getElementById('cartIcon').addEventListener('click', toggleCart);
    document.getElementById('floatCartBtn').addEventListener('click', toggleCart);
    document.getElementById('closeCartBtn').addEventListener('click', toggleCart);
    document.getElementById('checkoutBtn').addEventListener('click', checkout);
});
</script>

</body>
</html>