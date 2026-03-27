<script setup>
import { ref, reactive, onMounted, computed, watch } from 'vue';
import axios from 'axios';

const products = ref([]);
const isLoading = ref(false);
const isModalOpen = ref(false);
const isEditing = ref(false); // boolean to track edit mode
const errorMsg = ref("");
const searchQuery = ref("");
const selectedCategory = ref("Semua");

// Form Data
const form = reactive({
    id: null,
    kode_obat: "",
    nama_obat: "",
    kategori: "Obat Bebas",
    stok: 0,
    unit: "Pcs", // satuan -> unit renaming handled in fetch/save
    stok_minimal: 5,
    harga_beli: 0,
    harga_jual: 0,
    tgl_kadaluarsa: ""
});

// Category Options
const categories = ['Obat Bebas', 'Obat Keras', 'Obat Terbatas', 'Suplemen', 'Alkes', 'Obat Luar', 'Herbal'];
const units = ['Pcs', 'Strip', 'Botol', 'Box', 'Tube', 'Sachet'];

// --- API ACTIONS ---

// Fetch Data
const fetchProducts = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/obat', {
            params: { search: searchQuery.value },
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        // Handle response format from controller (wrapper data)
        products.value = response.data.data;
    } catch (error) {
        console.error("Fetch Error", error);
    } finally {
        isLoading.value = false;
    }
};

// Computed property for filtering by search and selectedCategory
const filteredProducts = computed(() => {
    let result = products.value;

    if (selectedCategory.value !== "Semua") {
        result = result.filter(p => p.kategori === selectedCategory.value);
    }
    
    // API already handles search, but we fall back client side for speed
    if (searchQuery.value) {
        const lower = searchQuery.value.toLowerCase();
        result = result.filter(p => 
            (p.nama_obat && p.nama_obat.toLowerCase().includes(lower)) || 
            (p.kode_obat && p.kode_obat.toLowerCase().includes(lower))
        );
    }

    return result;
});

// Categories for filter chips
const filterCategories = computed(() => {
    const cats = new Set(products.value.map(p => p.kategori));
    return ["Semua", ...Array.from(cats)].filter(Boolean);
});

// Export Excel
const exportExcel = () => {
    isLoading.value = true;
    const token = localStorage.getItem('token');
    
    axios.get('/api/obat/export', {
        headers: { Authorization: `Bearer ${token}` },
        responseType: 'blob'
    }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `inventory_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }).catch(e => {
        console.error(e);
        alert("Gagal export data. Pastikan anda sudah login.");
    }).finally(() => {
        isLoading.value = false;
    });
};

// Search Watcher
watch(searchQuery, () => {
    fetchProducts();
});

onMounted(() => {
    fetchProducts();
});

// --- MODAL & FORM LOGIC ---

const openModal = (item = null) => {
    errorMsg.value = "";
    if (item) {
        isEditing.value = true;
        form.id = item.id;
        form.kode_obat = item.kode_obat;
        form.nama_obat = item.nama_obat;
        form.kategori = item.kategori;
        form.stok = item.stok; // Note: usually stock is not edited directly here if we have Penerimaan, but allowed for correction
        form.unit = item.satuan;
        form.stok_minimal = item.stok_minimal;
        form.harga_beli = item.harga_beli;
        form.harga_jual = item.harga_jual;
        form.tgl_kadaluarsa = item.tgl_kadaluarsa;
    } else {
        isEditing.value = false;
        form.id = null;
        form.kode_obat = "";
        form.nama_obat = "";
        form.kategori = "Obat Bebas";
        form.stok = 0;
        form.unit = "Pcs";
        form.stok_minimal = 5;
        form.harga_beli = 0;
        form.harga_jual = 0;
        form.tgl_kadaluarsa = "";
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const saveProduct = async () => {
    isLoading.value = true;
    errorMsg.value = "";

    // Mapping payload keys to backend expectations
    const payload = {
        kode_obat: form.kode_obat,
        nama_obat: form.nama_obat,
        kategori: form.kategori,
        stok: form.stok,
        satuan: form.unit,
        stok_minimal: form.stok_minimal,
        harga_beli: form.harga_beli,
        harga_jual: form.harga_jual,
        tgl_kadaluarsa: form.tgl_kadaluarsa
    };

    try {
        const url = isEditing.value ? `/api/obat/${form.id}` : '/api/obat';
        const method = isEditing.value ? 'put' : 'post';

        await axios[method](url, payload, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        await fetchProducts();
        closeModal();
    } catch (error) {
        console.error(error);
        if (error.response && error.response.data.message) {
            errorMsg.value = error.response.data.message;
        } else if (error.response && error.response.data) {
            // Laravel Validation errors usually come as object
            errorMsg.value = Object.values(error.response.data).flat().join(', ');
        } else {
            errorMsg.value = "Gagal menyimpan data.";
        }
    } finally {
        isLoading.value = false;
    }
};

const deleteProduct = async (id) => {
    if (!confirm("Yakin ingin menghapus obat ini?")) return;
    isLoading.value = true;
    try {
        await axios.delete(`/api/obat/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        await fetchProducts();
    } catch (e) {
        alert("Gagal menghapus obat.");
    } finally {
        isLoading.value = false;
    }
};

// Helpers
const getStatusClass = (item) => {
    if (item.stok <= 0) return 'status-red';
    if (item.stok <= item.stok_minimal) return 'status-orange';
    return 'status-green';
};

const getStatusLabel = (item) => {
    if (item.stok <= 0) return 'Kosong';
    if (item.stok <= item.stok_minimal) return 'Menipis';
    return 'Aman';
};

const formatRp = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

</script>

<template>
    <div class="inventory-container">
        
        <div class="page-header">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" v-model="searchQuery" placeholder="Cari kode atau nama obat...">
            </div>
            
            <div class="action-buttons">
                <button @click="exportExcel" class="btn-export">📥 Export Excel</button>
                <button @click="openModal()" class="btn-add">➕ Tambah Obat Baru</button>
            </div>
        </div>

        <div class="category-chips">
            <button 
                v-for="cat in filterCategories" 
                :key="cat"
                class="chip"
                :class="{'active': selectedCategory === cat}"
                @click="selectedCategory = cat"
            >
                {{ cat }}
            </button>
        </div>

        <div class="table-card">
            <div v-if="isLoading && products.length === 0" class="loading-state">Memuat data inventori...</div>

            <table v-else class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Obat</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Harga Jual</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in filteredProducts" :key="item.id">
                        <td class="col-code">{{ item.kode_obat }}</td>
                        <td class="col-name">
                            <span class="name-text">{{ item.nama_obat }}</span>
                            <div class="expire-date">Exp: {{ item.tgl_kadaluarsa }}</div>
                        </td>
                        <td>
                            <span class="category-pill">{{ item.kategori }}</span>
                        </td>
                        <td class="col-stock">{{ item.stok }}</td>
                        <td>{{ item.satuan }}</td>
                        <td class="col-price">{{ formatRp(item.harga_jual) }}</td>
                        <td>
                            <span class="status-badge" :class="getStatusClass(item)">
                                {{ getStatusLabel(item) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <button @click="openModal(item)" class="btn-icon edit" title="Edit">✏️</button>
                                <button @click="deleteProduct(item.id)" class="btn-icon delete" title="Hapus">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredProducts.length === 0 && !isLoading">
                        <td colspan="8" class="empty-state">Data obat tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ isEditing ? 'Edit Data Obat' : 'Tambah Obat Baru' }}</h4>
                    <button @click="closeModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="saveProduct" class="modal-body">
                    
                    <div v-if="errorMsg" class="alert-error">{{ errorMsg }}</div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Kode Obat <span class="required">*</span></label>
                            <input v-model="form.kode_obat" type="text" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Kategori</label>
                            <select v-model="form.kategori" class="form-input">
                                <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Obat <span class="required">*</span></label>
                        <input v-model="form.nama_obat" type="text" required class="form-input">
                    </div>

                    <div class="grid-3">
                        <div class="form-group">
                            <label>Stok Awal</label>
                            <input v-model.number="form.stok" type="number" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Satuan</label>
                            <select v-model="form.unit" class="form-input">
                                <option v-for="u in units" :key="u" :value="u">{{ u }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Min. Stok</label>
                            <input v-model.number="form.stok_minimal" type="number" class="form-input">
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label>Harga Beli (Rp)</label>
                            <input v-model.number="form.harga_beli" type="number" class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Harga Jual (Rp)</label>
                            <input v-model.number="form.harga_jual" type="number" required class="form-input">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Tanggal Kadaluarsa</label>
                        <input v-model="form.tgl_kadaluarsa" type="date" required class="form-input">
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="closeModal" class="btn-cancel">Batal</button>
                        <button type="submit" class="btn-save" :disabled="isLoading">
                            {{ isLoading ? 'Menyimpan...' : 'Simpan Data' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</template>

<style scoped>
/* --- CONTAINER --- */
.inventory-container {
    animation: fadeIn 0.5s ease;
}

/* --- HEADER --- */
.page-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;
}

.search-wrapper {
    background: var(--input-bg); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 10px;
    display: flex; align-items: center; gap: 10px; width: 350px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.search-wrapper input { border: none; outline: none; width: 100%; font-size: 14px; background: transparent; color: var(--text-main); }

.action-buttons { display: flex; gap: 12px; }

.btn-add {
    background: var(--primary); color: white; border: none; padding: 10px 20px;
    border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: 0.2s; font-size: 13px;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}
.btn-add:hover { background: var(--primary-dark); transform: translateY(-2px); }

.btn-export {
    background: var(--input-bg); color: var(--text-main); border: 1px solid var(--border-color); padding: 10px 20px;
    border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px;
}
.btn-export:hover { background: var(--bg-hover); border-color: var(--text-muted); }

.category-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
.chip {
    padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border-color); background: var(--input-bg); font-size: 13px; 
    font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.chip:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-1px); }
.chip.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }

/* --- TABEL --- */
.table-card {
    background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
    background: var(--bg-hover); text-align: left; padding: 16px; font-size: 12px;
    font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px;
    border-bottom: 1px solid var(--border-color);
}
.data-table td {
    padding: 16px; font-size: 14px; color: var(--text-main); border-bottom: 1px solid var(--border-color);
}
.data-table tr:hover { background: var(--bg-hover); }

/* Kolom Spesifik */
.col-code { font-family: 'Courier New', monospace; font-weight: 700; color: var(--text-muted); }
.col-name { font-weight: 600; color: var(--text-main); }
.col-price { font-weight: 600; color: var(--text-main); }
.col-stock { font-weight: 700; }
.expire-date { font-size: 11px; color: var(--text-muted); margin-top: 4px; }

/* Badges */
.category-pill {
    background: var(--info-bg); color: var(--info); font-size: 11px; padding: 4px 10px;
    border-radius: 20px; font-weight: 600;
}

.status-badge {
    font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 700;
    display: inline-block; min-width: 60px; text-align: center;
}
.status-green { background: var(--success-bg); color: var(--success); }
.status-orange { background: var(--warning-bg); color: var(--warning); }
.status-red { background: var(--danger-bg); color: var(--danger); }

/* Actions */
.action-group { display: flex; gap: 8px; }
.btn-icon {
    width: 32px; height: 32px; border-radius: 6px; border: 1px solid var(--border-color);
    background: var(--bg-card); cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: 0.2s; color: var(--text-muted); padding: 0;
}
.btn-icon:hover { background: var(--bg-hover); color: var(--primary); }
.btn-icon.delete:hover { background: var(--danger-bg); border-color: var(--danger-border); color: var(--danger); }

.empty-state, .loading-state { padding: 40px; text-align: center; color: var(--text-muted); }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; backdrop-filter: blur(2px); animation: fadeIn 0.2s; }
.modal-content { background: var(--bg-card); width: 100%; max-width: 600px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }

.modal-header { padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-hover); }
.modal-header h4 { margin: 0; font-size: 18px; color: var(--text-main); }
.btn-close { background: none; border: none; font-size: 24px; color: var(--text-muted); cursor: pointer; }

.modal-body { padding: 25px; max-height: 80vh; overflow-y: auto; }
.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: var(--text-muted); }
.required { color: var(--danger); }
.form-input { width: 100%; padding: 10px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); border-radius: 8px; outline: none; transition: 0.2s; font-size: 14px; box-sizing: border-box; }
.form-input:focus { border-color: var(--primary); }

.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }

.modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; }
.btn-cancel { padding: 10px 20px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; font-weight: 600; color: var(--text-muted); }
.btn-save { padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
.btn-save:hover { background: var(--primary-dark); }

.alert-error { background: var(--danger-bg); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid var(--danger-border); }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>