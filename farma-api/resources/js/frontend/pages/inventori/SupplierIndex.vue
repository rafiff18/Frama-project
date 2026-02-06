<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';

const suppliers = ref([]);
const isLoading = ref(false);
const isModalOpen = ref(false);
const isEditing = ref(false);
const errorMsg = ref("");

const form = reactive({
    id: null,
    nama_suppliers: "",
    telepon: "",
    alamat: ""
});

const searchQuery = ref("");

// Fetch Suppliers
const fetchSuppliers = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/suppliers', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        suppliers.value = response.data;
    } catch (error) {
        console.error(error);
        errorMsg.value = "Gagal memuat data supplier.";
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchSuppliers();
});

// Filtered List
const filteredSuppliers = computed(() => {
    if (!searchQuery.value) return suppliers.value;
    const lower = searchQuery.value.toLowerCase();
    return suppliers.value.filter(s => 
        (s.nama_suppliers || '').toLowerCase().includes(lower) || 
        (s.alamat || '').toLowerCase().includes(lower)
    );
});

// Modal Logic
const openModal = (supplier = null) => {
    errorMsg.value = "";
    if (supplier) {
        isEditing.value = true;
        form.id = supplier.id;
        form.nama_suppliers = supplier.nama_suppliers;
        form.telepon = supplier.telepon;
        form.alamat = supplier.alamat;
    } else {
        isEditing.value = false;
        form.id = null;
        form.nama_suppliers = "";
        form.telepon = "";
        form.alamat = "";
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

// CRUD Actions
const saveSupplier = async () => {
    isLoading.value = true;
    errorMsg.value = "";
    try {
        const url = isEditing.value ? `/api/suppliers/${form.id}` : '/api/suppliers';
        const method = isEditing.value ? 'put' : 'post';
        
        await axios[method](url, form, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });

        await fetchSuppliers();
        closeModal();
    } catch (error) {
        console.error(error);
    if (error.response && error.response.data.message) {
            errorMsg.value = error.response.data.message;
        } else {
            errorMsg.value = "Gagal menyimpan data.";
        }
    } finally {
        isLoading.value = false;
    }
};

const deleteSupplier = async (id) => {
    if (!confirm("Yakin ingin menghapus supplier ini?")) return;

    isLoading.value = true;
    try {
        await axios.delete(`/api/suppliers/${id}`, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        await fetchSuppliers();
    } catch (error) {
        console.error(error);
        alert("Gagal menghapus supplier.");
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="page-container">
        
        <div class="page-header">
            <div>
                <h3>🚚 Kelola Supplier</h3>
                <p>Manajemen data pemasok obat.</p>
            </div>
            <button @click="openModal()" class="btn-primary">+ Tambah Supplier</button>
        </div>

        <!-- Search Bar -->
        <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input v-model="searchQuery" type="text" placeholder="Cari nama supplier atau alamat...">
        </div>

        <!-- Table -->
        <div class="table-container">
            <div v-if="isLoading" class="loading-state">Memuat data...</div>
            
            <table v-else class="data-table">
                <thead>
                    <tr>
                        <th>Nama Supplier</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th width="100">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="sup in filteredSuppliers" :key="sup.id">
                        <td><strong>{{ sup.nama_suppliers }}</strong></td>
                        <td>{{ sup.telepon || '-' }}</td>
                        <td>{{ sup.alamat || '-' }}</td>
                        <td>
                            <div class="action-buttons">
                                <button @click="openModal(sup)" class="btn-icon edit">✏️</button>
                                <button @click="deleteSupplier(sup.id)" class="btn-icon delete">🗑️</button>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredSuppliers.length === 0">
                        <td colspan="4" class="empty-state">Tidak ada data supplier.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>{{ isEditing ? 'Edit Supplier' : 'Tambah Supplier Baru' }}</h4>
                    <button @click="closeModal" class="btn-close">×</button>
                </div>
                
                <form @submit.prevent="saveSupplier" class="modal-body">
                    
                    <div v-if="errorMsg" class="alert-error">{{ errorMsg }}</div>

                    <div class="form-group">
                        <label>Nama Supplier <span class="required">*</span></label>
                        <input v-model="form.nama_suppliers" type="text" required placeholder="PT. Farma Sejahtera" class="form-input">
                    </div>

                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input v-model="form.telepon" type="text" placeholder="0812..." class="form-input">
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea v-model="form.alamat" rows="3" placeholder="Jl. Raya No. 123" class="form-input"></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" @click="closeModal" class="btn-cancel">Batal</button>
                        <button type="submit" class="btn-save" :disabled="isLoading">
                            {{ isLoading ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>

<style scoped>
.page-container { animation: fadeIn 0.3s ease; }
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.page-header h3 { margin: 0; color: #0f172a; font-size: 24px; }
.page-header p { margin: 5px 0 0; color: #64748b; font-size: 14px; }

.btn-primary { background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }
.btn-primary:hover { background: #059669; }

.search-bar { position: relative; margin-bottom: 20px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.5; }
.search-bar input { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid #e2e8f0; border-radius: 10px; outline: none; transition: 0.2s; box-sizing: border-box; }
.search-bar input:focus { border-color: #10b981; box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }

.table-container { background: white; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #f8fafc; padding: 15px; text-align: left; font-size: 13px; color: #64748b; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
.data-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155; }
.data-table tr:last-child td { border-bottom: none; }

.action-buttons { display: flex; gap: 8px; }
.btn-icon { width: 32px; height: 32px; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.btn-icon.edit { background: #e0f2fe; color: #0284c7; }
.btn-icon.edit:hover { background: #bae6fd; }
.btn-icon.delete { background: #fee2e2; color: #ef4444; }
.btn-icon.delete:hover { background: #fecaca; }

.loading-state, .empty-state { padding: 40px; text-align: center; color: #94a3b8; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; backdrop-filter: blur(2px); animation: fadeIn 0.2s; }
.modal-content { background: white; width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }

.modal-header { padding: 20px 25px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #f8fafc; }
.modal-header h4 { margin: 0; font-size: 18px; color: #0f172a; }
.btn-close { background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; }

.modal-body { padding: 25px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: #475569; }
.required { color: #ef4444; }
.form-input { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.2s; font-size: 14px; box-sizing: border-box; }
.form-input:focus { border-color: #10b981; }

.modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; }
.btn-cancel { padding: 10px 20px; background: white; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; font-weight: 600; color: #64748b; }
.btn-save { padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
.btn-save:hover { background: #059669; }

.alert-error { background: #fef2f2; color: #ef4444; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fca5a5; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
