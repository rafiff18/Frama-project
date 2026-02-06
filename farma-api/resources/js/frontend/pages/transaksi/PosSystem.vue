<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

// State
const products = ref([]);
const cart = ref([]);
const searchQuery = ref("");
const isLoading = ref(false);
const paymentAmount = ref(0); // Uang yang dibayar
const errorMsg = ref("");

// --- API ACTIONS ---

// Fetch Obat (Real Data)
const fetchProducts = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/obat', {
            params: { search: searchQuery.value },
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        // API returns { success: true, data: [...] } or just [...] depend on controller
        // Based on ObatController: return response()->json(['success'=>true, 'data'=>...])
        products.value = response.data.data || response.data;
    } catch (error) {
        console.error("Gagal load obat", error);
    } finally {
        isLoading.value = false;
    }
};

watch(searchQuery, () => {
    fetchProducts();
});

onMounted(() => {
    fetchProducts();
});

// --- COMPUTED ---

// Filter (Client side fallback or reliance on API search)
// Since we have API search, we can rely on that, but for faster UX on small lists:
const filteredProducts = computed(() => {
    return products.value; // Already filtered by API or show all
});

const subtotal = computed(() => {
    return cart.value.reduce((sum, item) => sum + (item.harga_jual * item.qty), 0);
});

const total = computed(() => subtotal.value);

const kembalian = computed(() => {
    return paymentAmount.value - total.value;
});

// --- METHODS ---

const addToCart = (product) => {
    if (product.stok <= 0) return alert("Stok habis!");

    const existingItem = cart.value.find(item => item.id === product.id);
    if (existingItem) {
        if (existingItem.qty < product.stok) {
            existingItem.qty++;
        } else {
            alert("Stok tidak mencukupi!");
        }
    } else {
        cart.value.push({ 
            id: product.id, 
            name: product.nama_obat, 
            price: product.harga_jual, 
            harga_jual: product.harga_jual, // backend need consistency
            qty: 1, 
            stock: product.stok 
        });
    }
};

const decreaseQty = (item) => {
    if (item.qty > 1) {
        item.qty--;
    } else {
        cart.value = cart.value.filter(i => i.id !== item.id);
    }
};

const formatRp = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

const checkout = async () => {
    if (cart.value.length === 0) return alert("Keranjang kosong!");
    if (paymentAmount.value < total.value) return alert("Uang pembayaran kurang!");

    if (!confirm("Proses transaksi ini?")) return;

    isLoading.value = true;
    errorMsg.value = "";

    const payload = {
        items: cart.value.map(item => ({
            obat_id: item.id,
            jumlah: item.qty
        })),
        bayar: paymentAmount.value
    };

    try {
        await axios.post('/api/penjualan', payload, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        
        alert(`Transaksi Berhasil!\nKembalian: ${formatRp(kembalian.value)}`);
        
        cart.value = [];
        paymentAmount.value = 0;
        fetchProducts(); // Refresh stock
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data.message) {
            errorMsg.value = error.response.data.message;
        } else {
            errorMsg.value = "Transaksi gagal.";
        }
    } finally {
        isLoading.value = false;
    }
};

// UI Helpers
const getBadgeClass = (category) => {
    // Simple mapping
    if (category.includes('Keras')) return 'badge-red';
    if (category.includes('Bebas')) return 'badge-green';
    return 'badge-blue';
};

const getIcon = (unit) => {
    if (!unit) return '💊';
    const u = unit.toLowerCase();
    if (u.includes('botol') || u.includes('syrup')) return '🧴';
    if (u.includes('box')) return '📦';
    return '💊';
};

</script>

<template>
    <div class="pos-container">
        
        <!-- CATALOG -->
        <div class="catalog-section">
            <div class="catalog-header">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" v-model="searchQuery" placeholder="Cari obat (kode/nama)...">
                </div>
            </div>

            <div v-if="isLoading && products.length === 0" class="loading">Memuat obat...</div>

            <div v-else class="product-grid">
                <div 
                    v-for="product in filteredProducts" 
                    :key="product.id" 
                    class="product-card" 
                    @click="addToCart(product)"
                    :class="{ 'disabled': product.stok <= 0 }"
                >
                    <div class="card-top">
                        <div class="prod-icon">
                            {{ getIcon(product.satuan) }}
                        </div>
                        <span class="stock-badge" :class="{'low': product.stok < 5}">
                            Stok: {{ product.stok }}
                        </span>
                    </div>
                    
                    <div class="card-info">
                        <h4 class="prod-name">{{ product.nama_obat }}</h4>
                        <span class="category-badge" :class="getBadgeClass(product.kategori)">
                            {{ product.kategori }}
                        </span>
                        <h3 class="prod-price">{{ formatRp(product.harga_jual) }}</h3>
                    </div>

                    <div class="hover-add" v-if="product.stok > 0">
                        + TAMBAH
                    </div>
                    <div class="out-stock" v-else>HABIS</div>
                </div>
            </div>
        </div>

        <!-- CART -->
        <div class="cart-section">
            <div class="cart-header">
                <h3>🛒 Keranjang Belanja</h3>
                <button class="btn-reset" @click="cart = []">Reset</button>
            </div>

            <div v-if="errorMsg" class="cart-error">{{ errorMsg }}</div>

            <div class="cart-items">
                <div v-if="cart.length === 0" class="empty-state">
                    <div class="empty-icon">🛒</div>
                    <p>Belum ada barang</p>
                </div>

                <div v-else v-for="item in cart" :key="item.id" class="cart-item">
                    <div class="item-info">
                        <span class="item-name">{{ item.name }}</span>
                        <span class="item-price">{{ formatRp(item.price) }}</span>
                    </div>
                    <div class="qty-control">
                        <button @click="decreaseQty(item)">−</button>
                        <span>{{ item.qty }}</span>
                        <button @click="item.qty < item.stock ? item.qty++ : null">+</button>
                    </div>
                    <div class="item-subtotal">
                        {{ formatRp(item.price * item.qty) }}
                    </div>
                </div>
            </div>

            <div class="cart-footer">
                <div class="summary-group">
                    <div class="row">
                        <span>Total Terhutang</span>
                        <span class="val-big">{{ formatRp(total) }}</span>
                    </div>
                </div>

                <div class="payment-group">
                    <label>Bayar Tunai (Rp)</label>
                    <input type="number" v-model.number="paymentAmount" class="payment-input" placeholder="0">
                </div>

                <div class="row return">
                    <span>Kembali</span>
                    <span :class="{'minus': kembalian < 0, 'plus': kembalian >= 0}">
                        {{ formatRp(kembalian) }}
                    </span>
                </div>
                
                <button class="btn-checkout" @click="checkout" :disabled="isLoading || cart.length === 0">
                    {{ isLoading ? 'Memproses...' : 'Proses Pembayaran' }}
                </button>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* LAYOUT */
.pos-container { display: flex; gap: 20px; height: calc(100vh - 100px); }
.catalog-section { flex: 7; display: flex; flex-direction: column; overflow: hidden; }
.cart-section { 
    flex: 3; background: white; border-radius: 16px; padding: 20px; 
    display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
    min-width: 320px;
}

/* CATALOG */
.catalog-header { margin-bottom: 20px; }
.search-box {
    background: white; border: 1px solid #e2e8f0; padding: 12px; border-radius: 10px;
    display: flex; align-items: center; gap: 10px; font-size: 14px;
}
.search-box input { width: 100%; border: none; outline: none; color: #334155; }

.product-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 15px;
    overflow-y: auto; padding-bottom: 20px; padding-right: 5px;
}

.product-card {
    background: white; border-radius: 12px; padding: 15px; border: 1px solid #f1f5f9;
    cursor: pointer; position: relative; transition: 0.2s;
}
.product-card:hover { transform: translateY(-3px); border-color: #10b981; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
.product-card.disabled { opacity: 0.6; cursor: not-allowed; background: #f8fafc; }

.card-top { display: flex; justify-content: space-between; margin-bottom: 10px; }
.prod-icon { font-size: 24px; }
.stock-badge { font-size: 10px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; height: fit-content; }
.stock-badge.low { color: #ef4444; background: #fef2f2; }

.card-info h4 { margin: 0 0 5px; font-size: 13px; color: #0f172a; line-height: 1.4; height: 36px; overflow: hidden; }
.category-badge { font-size: 9px; padding: 2px 6px; border-radius: 4px; background: #e0f2fe; color: #0284c7; }
.prod-price { margin: 8px 0 0; color: #10b981; font-size: 15px; font-weight: 800; }

.hover-add, .out-stock {
    position: absolute; bottom: 0; left: 0; width: 100%; text-align: center; 
    padding: 8px; font-size: 11px; font-weight: 700; border-radius: 0 0 12px 12px;
}
.hover-add { background: #10b981; color: white; opacity: 0; transition: 0.2s; }
.product-card:hover .hover-add { opacity: 1; }
.out-stock { background: #cbd5e1; color: white; }

/* CART */
.cart-header { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; }
.cart-items { flex: 1; overflow-y: auto; }
.empty-state { text-align: center; margin-top: 40px; color: #cbd5e1; }
.cart-error { background: #fef2f2; color: #ef4444; padding: 10px; font-size: 12px; margin-bottom: 10px; border-radius: 8px; }

.cart-item { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px dashed #f1f5f9; padding-bottom: 8px; }
.item-info { width: 40%; }
.item-name { display: block; font-size: 12px; font-weight: 600; color: #334155; }
.item-price { font-size: 11px; color: #64748b; }

.qty-control { display: flex; align-items: center; gap: 5px; }
.qty-control button { width: 20px; height: 20px; background: #f1f5f9; border: none; border-radius: 4px; cursor: pointer; }
.qty-control span { font-size: 12px; font-weight: 600; min-width: 15px; text-align: center; }

.item-subtotal { font-size: 12px; font-weight: 700; color: #0f172a; width: 25%; text-align: right; }

/* FOOTER */
.cart-footer { margin-top: auto; padding-top: 15px; border-top: 2px solid #f1f5f9; }
.summary-group { margin-bottom: 15px; }
.row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 13px; color: #64748b; }
.val-big { font-size: 18px; font-weight: 800; color: #10b981; }

.payment-group { background: #f8fafc; padding: 10px; border-radius: 8px; margin-bottom: 10px; }
.payment-group label { display: block; font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 5px; }
.payment-input { width: 100%; border: 1px solid #cbd5e1; padding: 8px; border-radius: 6px; font-weight: 700; color: #0f172a; font-size: 16px; outline: none; box-sizing: border-box; }
.payment-input:focus { border-color: #10b981; }

.return .plus { color: #10b981; font-weight: 700; }
.return .minus { color: #ef4444; font-weight: 700; }

.btn-checkout { 
    width: 100%; padding: 12px; background: #0f172a; color: white; border: none; 
    border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 10px; 
}
.btn-checkout:hover { background: #1e293b; }
.btn-checkout:disabled { background: #cbd5e1; cursor: not-allowed; }
</style>