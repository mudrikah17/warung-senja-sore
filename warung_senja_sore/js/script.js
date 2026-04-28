// State
let currentCategory = 'all';
let cart = [];
let products = [];

// Load dari localStorage
function loadCart() {
    const savedCart = localStorage.getItem('senjaCart');
    if (savedCart) {
        cart = JSON.parse(savedCart);
    }
    updateCartUI();
}

function saveCart() {
    localStorage.setItem('senjaCart', JSON.stringify(cart));
    updateCartUI();
}

function formatRupiah(amount) {
    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

function renderProducts() {
    const grid = document.getElementById('produkGrid');
    const filtered = currentCategory === 'all' 
        ? products 
        : products.filter(p => p.kategori === currentCategory);
    
    if (filtered.length === 0) {
        grid.innerHTML = `<div class="empty-message" style="grid-column:1/-1; text-align:center; padding:3rem;">
                            <i class="fas fa-box-open"></i>
                            <p>Belum ada produk di kategori ini</p>
                          </div>`;
        return;
    }
    
    grid.innerHTML = filtered.map(product => `
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
    
    saveCart();
    showNotification(`${product.nama_produk} ditambahkan ke keranjang`);
}

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

function updateQuantity(productId, change) {
    const item = cart.find(i => i.id === productId);
    if (item) {
        const newQty = item.quantity + change;
        if (newQty <= 0) {
            removeFromCart(productId);
        } else {
            item.quantity = newQty;
            saveCart();
        }
    }
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    saveCart();
    showNotification('Produk dihapus dari keranjang');
}

function showNotification(msg) {
    const notif = document.createElement('div');
    notif.className = 'notification';
    notif.innerHTML = `<i class="fas fa-check-circle"></i> ${msg}`;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 2000);
}

function toggleCart() {
    const sidebar = document.getElementById('cartSidebar');
    sidebar.classList.toggle('open');
}

function checkout() {
    if (cart.length === 0) {
        showNotification('Keranjang masih kosong!');
        return;
    }
    window.location.href = 'keranjang.php';
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    products = productsData || [];
    loadCart();
    renderProducts();
    
    // Filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentCategory = btn.dataset.category;
            renderProducts();
        });
    });
    
    // Cart sidebar
    const floatBtn = document.getElementById('floatCartBtn');
    const closeBtn = document.getElementById('closeCartBtn');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    if (floatBtn) floatBtn.addEventListener('click', toggleCart);
    if (closeBtn) closeBtn.addEventListener('click', toggleCart);
    if (checkoutBtn) checkoutBtn.addEventListener('click', checkout);
    
    // Close cart when clicking outside (optional)
    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('cartSidebar');
        const floatBtn = document.getElementById('floatCartBtn');
        if (sidebar && sidebar.classList.contains('open')) {
            if (!sidebar.contains(e.target) && !floatBtn.contains(e.target)) {
                sidebar.classList.remove('open');
            }
        }
    });
});