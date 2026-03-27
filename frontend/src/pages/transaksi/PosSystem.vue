<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

// State
const products = ref([]);
const cart = ref([]);
const searchQuery = ref("");
const selectedCategory = ref("Semua");
const isListView = ref(false); // Default to Grid view
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

const categories = computed(() => {
    // Collect all unique categories from products array dynamically, + "Semua"
    const cats = new Set(products.value.map(p => p.kategori));
    return ["Semua", ...Array.from(cats)].filter(Boolean);
});

// Filter by search AND category
const filteredProducts = computed(() => {
    let result = products.value;

    if (selectedCategory.value !== "Semua") {
        result = result.filter(p => p.kategori === selectedCategory.value);
    }
    
    // API already handles search via backend, but just in case user types fast
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(p => 
            p.nama_obat.toLowerCase().includes(query) || 
            p.kode_obat.toLowerCase().includes(query)
        );
    }

    return result;
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

const addQuickCash = (amount) => {
    paymentAmount.value = amount;
};

const setUangPas = () => {
    paymentAmount.value = total.value;
};

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
    if (!category) return 'badge-blue';
    if (category.toLowerCase().includes('keras')) return 'badge-red';
    if (category.toLowerCase().includes('bebas')) return 'badge-green';
    return 'badge-blue';
};

const getStockClass = (stok) => {
    if (stok > 20) return 'stock-safe';
    if (stok > 5) return 'stock-warning';
    return 'stock-critical';
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
                <div class="search-toolbar">
                    <div class="search-box">
                        <span class="search-icon">🔍</span>
                        <input type="text" v-model="searchQuery" placeholder="Cari obat (kode/nama)...">
                    </div>
                    <button class="btn-toggle-view" @click="isListView = !isListView" :title="isListView ? 'Ubah ke Grid' : 'Ubah ke List'">
                        <span v-if="isListView">🔲</span>
                        <span v-else>📄</span>
                    </button>
                </div>
                
                <div class="category-chips">
                    <button 
                        v-for="cat in categories" 
                        :key="cat"
                        class="chip"
                        :class="{'active': selectedCategory === cat}"
                        @click="selectedCategory = cat"
                    >
                        {{ cat }}
                    </button>
                </div>
            </div>

            <div v-if="isLoading && products.length === 0" class="loading">Memuat obat...</div>

            <div v-else class="product-wrapper" :class="{'list-layout': isListView, 'grid-layout': !isListView}">
                <div 
                    v-for="product in filteredProducts" 
                    :key="product.id" 
                    class="product-card" 
                    @click="addToCart(product)"
                    :class="{ 'disabled': product.stok <= 0 }"
                >
                    <div class="card-left" v-if="isListView">
                        <div class="prod-icon-small">{{ getIcon(product.satuan) }}</div>
                        <div class="card-info-list">
                            <h4 class="prod-name">{{ product.nama_obat }}</h4>
                            <span class="category-badge" :class="getBadgeClass(product.kategori)">{{ product.kategori }}</span>
                        </div>
                    </div>

                    <div class="card-top" v-else>
                        <div class="prod-icon">
                            {{ getIcon(product.satuan) }}
                        </div>
                        <span class="stock-badge" :class="getStockClass(product.stok)">
                            Stok: {{ product.stok }}
                        </span>
                    </div>
                    
                    <div class="card-info" v-if="!isListView">
                        <h4 class="prod-name">{{ product.nama_obat }}</h4>
                        <span class="category-badge" :class="getBadgeClass(product.kategori)">
                            {{ product.kategori }}
                        </span>
                        <h3 class="prod-price">{{ formatRp(product.harga_jual) }}</h3>
                    </div>

                    <div class="card-right" v-if="isListView">
                        <span class="stock-badge" :class="getStockClass(product.stok)">Stok: {{ product.stok }}</span>
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
                <button class="btn-reset" @click="cart = []">
                    <span class="icon">🗑️</span> Kosongkan
                </button>
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
                    <div class="quick-cash-buttons">
                        <button type="button" @click="setUangPas" class="quick-cash-btn">Uang Pas</button>
                        <button type="button" @click="addQuickCash(50000)" class="quick-cash-btn">50rb</button>
                        <button type="button" @click="addQuickCash(100000)" class="quick-cash-btn">100rb</button>
                    </div>
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
    flex: 3; background: var(--bg-card); border-radius: 16px; padding: 20px; 
    display: flex; flex-direction: column; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
    min-width: 320px; border: 1px solid var(--border-color);
    /* Sticky Fixes */
    height: calc(100vh - 150px);
    position: sticky;
    top: 20px; overflow: hidden;
}

/* CATALOG */
.catalog-header { margin-bottom: 20px; display: flex; flex-direction: column; gap: 15px; }

.search-toolbar { display: flex; gap: 10px; align-items: center; }
.search-box {
    flex: 1; background: var(--input-bg); border: 1px solid var(--border-color); padding: 12px; border-radius: 10px;
    display: flex; align-items: center; gap: 10px; font-size: 14px;
}
.search-box input { width: 100%; border: none; outline: none; background: transparent; color: var(--text-main); }
.btn-toggle-view {
    height: 44px; width: 44px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 10px;
    cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; color: var(--text-muted);
    transition: all 0.2s;
}
.btn-toggle-view:hover { background: var(--bg-hover); color: var(--primary); }

.category-chips { display: flex; gap: 8px; flex-wrap: wrap; }
.chip {
    padding: 6px 14px; border-radius: 20px; border: 1px solid var(--border-color); background: var(--input-bg); font-size: 12px; 
    font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all 0.2s;
}
.chip:hover { border-color: var(--primary); color: var(--primary); }
.chip.active { background: var(--primary); color: white; border-color: var(--primary); }

/* LAYOUTS */
.product-wrapper { overflow-y: auto; padding-bottom: 20px; padding-right: 5px; height: 100%; }

.grid-layout {
    display: grid; 
    grid-template-columns: repeat(4, 1fr); 
    gap: 16px;
    align-content: start;
}
@media (min-width: 1400px) {
    .grid-layout { grid-template-columns: repeat(5, 1fr); gap: 20px; }
}

.list-layout {
    display: flex; flex-direction: column; gap: 12px;
}

.product-card {
    background: var(--bg-card); border-radius: 16px; padding: 16px; border: 1px solid var(--border-color);
    cursor: pointer; position: relative; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden;
    display: flex; flex-direction: column; justify-content: space-between;
}
.product-card:hover { transform: translateY(-4px); border-color: var(--primary); box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.15), 0 4px 6px -2px rgba(16, 185, 129, 0.05); }
.product-card.disabled { opacity: 0.6; cursor: not-allowed; background: var(--bg-hover); transform: none; box-shadow: none; border-color: var(--border-color); }

/* Grid Specific */
.card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
.prod-icon { font-size: 28px; background: var(--bg-hover); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
.stock-badge { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 8px; height: fit-content; text-transform: uppercase; letter-spacing: 0.5px; }

/* Dynamic Stock Colors */
.stock-safe { background: var(--success-bg); color: var(--success); }
.stock-warning { background: var(--warning-bg); color: var(--warning); }
.stock-critical { background: var(--danger-bg); color: var(--danger); }

/* List Specific */
.list-layout .product-card { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; }
.card-left { display: flex; align-items: center; gap: 15px; }
.prod-icon-small { font-size: 20px; background: var(--bg-hover); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 8px;}
.card-info-list { display: flex; flex-direction: column; gap: 4px; }
.card-right { display: flex; align-items: center; gap: 20px; }
.list-layout .prod-price { margin: 0; }
.list-layout .hover-add { display: none; } /* Hide full width hover add for list view */

.card-info h4 { margin: 0 0 8px; font-size: 14px; color: var(--text-main); line-height: 1.4; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; }
.category-badge { font-size: 10px; font-weight: 600; padding: 3px 8px; border-radius: 6px; background: var(--bg-hover); color: var(--text-muted); display: inline-block; margin-bottom: 8px; }
.badge-red { background: var(--danger-bg); color: var(--danger); }
.badge-green { background: var(--success-bg); color: var(--success); }
.badge-blue { background: var(--info-bg); color: var(--info); }
.prod-price { margin: auto 0 0; color: var(--primary); font-size: 16px; font-weight: 800; }

.hover-add, .out-stock {
    position: absolute; bottom: 0; left: 0; width: 100%; text-align: center; 
    padding: 10px; font-size: 12px; font-weight: 700; border-radius: 0 0 16px 16px;
}
.hover-add { background: var(--primary); color: white; opacity: 0; transition: all 0.25s ease; transform: translateY(10px); }
.product-card:hover .hover-add { opacity: 1; transform: translateY(0); }
.out-stock { background: var(--text-muted); color: white; }

/* CART */
.cart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px; flex-shrink: 0; }
.cart-header h3 { font-size: 16px; color: var(--text-main); margin: 0; }
.btn-reset { 
    display: flex; align-items: center; gap: 6px;
    background: var(--danger-bg); color: var(--danger); border: 1px solid var(--danger-border); 
    padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; 
    cursor: pointer; transition: all 0.2s; 
}
.btn-reset:hover { border-color: var(--danger); }
.btn-reset .icon { font-size: 14px; }

.cart-items { flex: 1; overflow-y: auto; padding-right: 5px; }
.empty-state { text-align: center; margin-top: 40px; color: var(--text-muted); }
.cart-error { background: var(--danger-bg); color: var(--danger); padding: 10px; font-size: 12px; margin-bottom: 10px; border-radius: 8px; border: 1px solid var(--danger-border); }

.cart-item { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px dashed var(--border-color); padding-bottom: 8px; }
.item-info { width: 40%; }
.item-name { display: block; font-size: 12px; font-weight: 600; color: var(--text-main); }
.item-price { font-size: 11px; color: var(--text-muted); }

.qty-control { display: flex; align-items: center; gap: 5px; }
.qty-control button { width: 20px; height: 20px; background: var(--bg-hover); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 4px; cursor: pointer; }
.qty-control span { font-size: 12px; font-weight: 600; color: var(--text-main); min-width: 15px; text-align: center; }

.item-subtotal { font-size: 12px; font-weight: 700; color: var(--text-main); width: 25%; text-align: right; }

/* FOOTER */
.cart-footer { margin-top: auto; padding-top: 15px; border-top: 2px solid var(--border-color); }
.summary-group { margin-bottom: 15px; }
.row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; font-size: 13px; color: var(--text-muted); }
.val-big { font-size: 18px; font-weight: 800; color: var(--primary); }

.payment-group { background: var(--bg-hover); padding: 10px; border-radius: 8px; margin-bottom: 10px; }
.payment-group label { display: block; font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; }
.payment-input { width: 100%; border: 1px solid var(--border-color); background: var(--input-bg); padding: 8px; border-radius: 6px; font-weight: 700; color: var(--text-main); font-size: 16px; outline: none; box-sizing: border-box; }
.payment-input:focus { border-color: var(--primary); }

.quick-cash-buttons { display: flex; gap: 8px; margin-top: 8px; }
.quick-cash-btn { 
    flex: 1; padding: 6px; font-size: 11px; font-weight: 600; color: var(--info); 
    background: var(--info-bg); border: 1px solid var(--border-color); border-radius: 4px; cursor: pointer; transition: 0.2s;
}
.quick-cash-btn:hover { border-color: var(--info); }

.return .plus { color: var(--success); font-weight: 700; }
.return .minus { color: var(--danger); font-weight: 700; }

.btn-checkout { 
    width: 100%; padding: 12px; background: var(--sidebar-bg); color: white; border: none; 
    border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 10px; 
}
.btn-checkout:hover { background: var(--sidebar-hover); }
.btn-checkout:disabled { background: var(--border-color); cursor: not-allowed; }
</style>