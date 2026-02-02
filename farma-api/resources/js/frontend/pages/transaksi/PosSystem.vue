<script setup>
import { ref, computed } from 'vue';

// --- DATA DUMMY OBAT ---
const products = ref([
    { id: 1, name: 'Paracetamol 500mg', category: 'Obat Bebas', price: 15000, stock: 120, type: 'tablet' },
    { id: 2, name: 'Amoxicillin 500mg', category: 'Obat Keras', price: 45000, stock: 15, type: 'capsule' },
    { id: 3, name: 'Vitamin C 1000mg', category: 'Suplemen', price: 25000, stock: 85, type: 'tablet' },
    { id: 4, name: 'Cetirizine', category: 'Obat Terbatas', price: 12000, stock: 40, type: 'tablet' },
    { id: 5, name: 'Obat Batuk Sirup', category: 'Obat Bebas', price: 35000, stock: 8, type: 'syrup' },
    { id: 6, name: 'Masker Medis (Box)', category: 'Alkes', price: 50000, stock: 200, type: 'box' },
    { id: 7, name: 'Betadine 30ml', category: 'Obat Luar', price: 18000, stock: 15, type: 'liquid' },
    { id: 8, name: 'Termometer Digital', category: 'Alkes', price: 125000, stock: 5, type: 'device' },
]);

// State untuk Keranjang & Pencarian
const cart = ref([]);
const searchQuery = ref("");

// --- COMPUTED PROPERTIES ---
// 1. Filter Obat berdasarkan pencarian
const filteredProducts = computed(() => {
    return products.value.filter(p => 
        p.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// 2. Hitung Total Belanja
const subtotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + (item.price * item.qty), 0);
});

const total = computed(() => subtotal.value); // Bisa ditambah pajak jika perlu

// --- METHODS ---
// Masukkan ke Keranjang
const addToCart = (product) => {
    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        existingItem.qty++;
    } else {
        cart.value.push({ ...product, qty: 1 });
    }
};

// Kurangi Qty / Hapus
const decreaseQty = (item) => {
    if (item.qty > 1) {
        item.qty--;
    } else {
        cart.value = cart.value.filter(i => i.id !== item.id);
    }
};

// Format Rupiah
const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

// Simpan Transaksi (Dummy)
const checkout = () => {
    if(cart.value.length === 0) return alert("Keranjang masih kosong!");
    alert(`Total Transaksi: ${formatRp(total.value)}\nStruk berhasil dicetak!`);
    cart.value = []; // Kosongkan keranjang
};

// Helper Class untuk Warna Kategori
const getBadgeClass = (category) => {
    if (category === 'Obat Keras') return 'badge-red';
    if (category === 'Suplemen') return 'badge-blue';
    return 'badge-gray';
};
</script>

<template>
    <div class="pos-container">
        
        <div class="catalog-section">
            <div class="catalog-header">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" v-model="searchQuery" placeholder="Cari nama obat atau kategori...">
                </div>
                <div class="filter-tags">
                    <span class="tag active">Semua</span>
                    <span class="tag">Obat Keras</span>
                    <span class="tag">Vitamin</span>
                </div>
            </div>

            <div class="product-grid">
                <div 
                    v-for="product in filteredProducts" 
                    :key="product.id" 
                    class="product-card" 
                    @click="addToCart(product)"
                >
                    <div class="card-top">
                        <div class="prod-icon" :class="{'liquid': product.type === 'syrup'}">
                            {{ product.type === 'syrup' ? '🧴' : (product.type === 'box' ? '📦' : '💊') }}
                        </div>
                        <span class="stock-badge" :class="{'low': product.stock < 10}">
                            Stok: {{ product.stock }}
                        </span>
                    </div>
                    
                    <div class="card-info">
                        <h4 class="prod-name">{{ product.name }}</h4>
                        <span class="category-badge" :class="getBadgeClass(product.category)">
                            {{ product.category }}
                        </span>
                        <h3 class="prod-price">{{ formatRp(product.price) }}</h3>
                    </div>

                    <div class="hover-add">
                        + TAMBAH
                    </div>
                </div>
            </div>
        </div>

        <div class="cart-section">
            <div class="cart-header">
                <h3>🛒 Penjualan Baru</h3>
                <button class="btn-reset" @click="cart = []">Reset</button>
            </div>

            <div class="cart-items">
                <div v-if="cart.length === 0" class="empty-state">
                    <div class="empty-icon">🛒</div>
                    <p>Keranjang masih kosong</p>
                </div>

                <div v-else v-for="item in cart" :key="item.id" class="cart-item">
                    <div class="item-info">
                        <span class="item-name">{{ item.name }}</span>
                        <span class="item-price">{{ formatRp(item.price) }}</span>
                    </div>
                    <div class="qty-control">
                        <button @click="decreaseQty(item)">−</button>
                        <span>{{ item.qty }}</span>
                        <button @click="item.qty++">+</button>
                    </div>
                </div>
            </div>

            <div class="cart-footer">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>{{ formatRp(subtotal) }}</span>
                </div>
                <div class="total-row">
                    <span>Total Akhir</span>
                    <span class="total-val">{{ formatRp(total) }}</span>
                </div>
                
                <button class="btn-checkout" @click="checkout">
                    Cetak Struk & Selesai
                </button>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* --- LAYOUT UTAMA --- */
.pos-container {
    display: flex; gap: 24px; height: calc(100vh - 100px); /* Full height minus navbar */
}

/* --- BAGIAN KIRI (KATALOG) --- */
.catalog-section {
    flex: 2; display: flex; flex-direction: column; overflow: hidden;
}

.catalog-header { margin-bottom: 20px; }
.search-box {
    background: white; border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 12px;
    display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.search-box input { border: none; outline: none; width: 100%; font-size: 14px; color: #334155; }

.filter-tags { display: flex; gap: 10px; margin-top: 15px; }
.tag { 
    font-size: 12px; padding: 6px 14px; border-radius: 20px; background: #f1f5f9; 
    color: #64748b; cursor: pointer; font-weight: 600; transition: 0.2s; 
}
.tag:hover, .tag.active { background: #10b981; color: white; }

/* Grid Produk */
.product-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 16px;
    overflow-y: auto; padding-right: 5px; padding-bottom: 20px;
}

.product-card {
    background: white; border-radius: 16px; padding: 16px; border: 1px solid #f1f5f9;
    cursor: pointer; position: relative; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.product-card:hover { transform: translateY(-3px); border-color: #10b981; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.1); }

.card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.prod-icon { 
    width: 40px; height: 40px; background: #ecfdf5; border-radius: 10px; 
    display: flex; align-items: center; justify-content: center; font-size: 20px; color: #10b981;
}
.prod-icon.liquid { background: #fff7ed; color: #f97316; }

.stock-badge { font-size: 10px; font-weight: 700; color: #94a3b8; }
.stock-badge.low { color: #ef4444; }

.card-info { display: flex; flex-direction: column; gap: 4px; }
.prod-name { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0; line-height: 1.4; }
.prod-price { font-size: 15px; font-weight: 800; color: #10b981; margin: 8px 0 0; }

.category-badge { 
    display: inline-block; font-size: 9px; padding: 3px 6px; border-radius: 4px; 
    font-weight: 700; width: fit-content; text-transform: uppercase; letter-spacing: 0.5px;
}
.badge-red { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
.badge-blue { background: #eff6ff; color: #3b82f6; border: 1px solid #dbeafe; }
.badge-gray { background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; }

.hover-add {
    position: absolute; bottom: 0; left: 0; width: 100%; background: #10b981; color: white;
    text-align: center; padding: 8px; font-size: 12px; font-weight: 700;
    opacity: 0; transition: 0.2s; border-radius: 0 0 16px 16px;
}
.product-card:hover .hover-add { opacity: 1; }

/* --- BAGIAN KANAN (KERANJANG) --- */
.cart-section {
    flex: 1; background: white; border-radius: 20px; padding: 24px;
    display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    max-width: 400px;
}

.cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
.cart-header h3 { margin: 0; font-size: 16px; font-weight: 800; color: #0f172a; }
.btn-reset { background: none; border: none; color: #ef4444; font-size: 12px; cursor: pointer; font-weight: 600; }

.cart-items { flex: 1; overflow-y: auto; display: flex; flex-direction: column; gap: 12px; }
.empty-state { text-align: center; margin-top: 50px; color: #cbd5e1; }
.empty-icon { font-size: 40px; margin-bottom: 10px; opacity: 0.5; }

.cart-item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f8fafc; padding-bottom: 12px; }
.item-info { display: flex; flex-direction: column; }
.item-name { font-size: 13px; font-weight: 600; color: #334155; }
.item-price { font-size: 12px; color: #10b981; font-weight: 700; margin-top: 2px; }

.qty-control { display: flex; align-items: center; gap: 8px; background: #f1f5f9; padding: 4px; border-radius: 8px; }
.qty-control button { width: 24px; height: 24px; border-radius: 6px; border: none; background: white; cursor: pointer; color: #0f172a; font-weight: bold; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
.qty-control button:hover { background: #e2e8f0; }
.qty-control span { font-size: 13px; font-weight: 700; width: 20px; text-align: center; }

.cart-footer { margin-top: auto; padding-top: 20px; border-top: 2px dashed #e2e8f0; }
.summary-row, .total-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; color: #64748b; }
.total-row { margin-top: 10px; margin-bottom: 20px; align-items: center; }
.total-val { font-size: 20px; font-weight: 800; color: #10b981; }

.btn-checkout {
    width: 100%; background: #10b981; color: white; border: none; padding: 14px;
    border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4);
}
.btn-checkout:hover { background: #059669; transform: translateY(-2px); }
</style>