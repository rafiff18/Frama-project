<script setup>
import { ref, computed } from 'vue';

// --- DATA DUMMY OBAT ---
const products = ref([
    { id: 1, code: 'MED-001', name: 'Paracetamol 500mg', category: 'Obat Bebas', stock: 120, unit: 'Strip', price: 15000, status: 'Aman' },
    { id: 2, code: 'MED-002', name: 'Amoxicillin 500mg', category: 'Obat Keras', stock: 15, unit: 'Strip', price: 45000, status: 'Menipis' },
    { id: 3, code: 'MED-003', name: 'Vitamin C 1000mg', category: 'Suplemen', stock: 85, unit: 'Botol', price: 25000, status: 'Aman' },
    { id: 4, code: 'MED-004', name: 'Cetirizine', category: 'Obat Terbatas', stock: 40, unit: 'Strip', price: 12000, status: 'Aman' },
    { id: 5, code: 'MED-005', name: 'Obat Batuk Sirup', category: 'Obat Bebas', stock: 0, unit: 'Botol', price: 35000, status: 'Kosong' },
    { id: 6, code: 'ALK-001', name: 'Masker Medis (Box)', category: 'Alkes', stock: 200, unit: 'Box', price: 50000, status: 'Aman' },
    { id: 7, code: 'MED-006', name: 'Betadine 30ml', category: 'Obat Luar', stock: 8, unit: 'Botol', price: 18000, status: 'Menipis' },
]);

const searchQuery = ref("");

// Filter Pencarian
const filteredProducts = computed(() => {
    return products.value.filter(item => 
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        item.code.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Warna Status Stok
const getStatusClass = (status) => {
    if (status === 'Aman') return 'status-green';
    if (status === 'Menipis') return 'status-orange';
    return 'status-red';
};

const formatRp = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <div class="inventory-container">
        
        <div class="page-header">
            <div class="search-wrapper">
                <span class="search-icon">🔍</span>
                <input type="text" v-model="searchQuery" placeholder="Cari kode atau nama obat...">
            </div>
            
            <div class="action-buttons">
                <button class="btn-export">📥 Export Excel</button>
                <button class="btn-add">➕ Tambah Obat Baru</button>
            </div>
        </div>

        <div class="table-card">
            <table class="data-table">
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
                        <td class="col-code">{{ item.code }}</td>
                        <td class="col-name">
                            <span class="name-text">{{ item.name }}</span>
                        </td>
                        <td>
                            <span class="category-pill">{{ item.category }}</span>
                        </td>
                        <td class="col-stock">{{ item.stock }}</td>
                        <td>{{ item.unit }}</td>
                        <td class="col-price">{{ formatRp(item.price) }}</td>
                        <td>
                            <span class="status-badge" :class="getStatusClass(item.status)">
                                {{ item.status }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <button class="btn-icon edit" title="Edit">✏️</button>
                                <button class="btn-icon delete" title="Hapus">🗑️</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="filteredProducts.length === 0" class="empty-state">
                <p>Data obat tidak ditemukan.</p>
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
    background: white; border: 1px solid #e2e8f0; padding: 10px 16px; border-radius: 10px;
    display: flex; align-items: center; gap: 10px; width: 350px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.search-wrapper input { border: none; outline: none; width: 100%; font-size: 14px; color: #334155; }

.action-buttons { display: flex; gap: 12px; }

.btn-add {
    background: #10b981; color: white; border: none; padding: 10px 20px;
    border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;
    transition: 0.2s; font-size: 13px;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}
.btn-add:hover { background: #059669; transform: translateY(-2px); }

.btn-export {
    background: white; color: #334155; border: 1px solid #cbd5e1; padding: 10px 20px;
    border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px;
}
.btn-export:hover { background: #f8fafc; border-color: #94a3b8; }

/* --- TABEL --- */
.table-card {
    background: white; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
}

.data-table { width: 100%; border-collapse: collapse; }
.data-table th {
    background: #f8fafc; text-align: left; padding: 16px; font-size: 12px;
    font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
}
.data-table td {
    padding: 16px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9;
}
.data-table tr:hover { background: #fcfcfc; }

/* Kolom Spesifik */
.col-code { font-family: 'Courier New', monospace; font-weight: 700; color: #475569; }
.col-name { font-weight: 600; color: #0f172a; }
.col-price { font-weight: 600; color: #0f172a; }
.col-stock { font-weight: 700; }

/* Badges */
.category-pill {
    background: #eff6ff; color: #3b82f6; font-size: 11px; padding: 4px 10px;
    border-radius: 20px; font-weight: 600;
}

.status-badge {
    font-size: 11px; padding: 4px 10px; border-radius: 6px; font-weight: 700;
    display: inline-block; min-width: 60px; text-align: center;
}
.status-green { background: #ecfdf5; color: #10b981; }
.status-orange { background: #fff7ed; color: #f97316; }
.status-red { background: #fef2f2; color: #ef4444; }

/* Actions */
.action-group { display: flex; gap: 8px; }
.btn-icon {
    width: 32px; height: 32px; border-radius: 6px; border: 1px solid #e2e8f0;
    background: white; cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: 0.2s;
}
.btn-icon:hover { background: #f1f5f9; }
.btn-icon.delete:hover { background: #fef2f2; border-color: #fecaca; }

.empty-state { padding: 40px; text-align: center; color: #94a3b8; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>