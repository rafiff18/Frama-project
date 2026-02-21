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
const selectedFilter = ref("Semua");

// Alphabet groups for filtering
const alphabetGroups = ["Semua", "A-E", "F-J", "K-O", "P-T", "U-Z"];

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
    let result = suppliers.value;

    // Apply Alphabet Filter
    if (selectedFilter.value !== "Semua") {
        const range = selectedFilter.value.split("-"); // ["A", "E"]
        const start = range[0];
        const end = range[1];
        
        result = result.filter(s => {
            const firstLetter = (s.nama_suppliers || "").charAt(0).toUpperCase();
            return firstLetter >= start && firstLetter <= end;
        });
    }

    if (searchQuery.value) {
        const lower = searchQuery.value.toLowerCase();
        result = result.filter(s => 
            (s.nama_suppliers || '').toLowerCase().includes(lower) || 
            (s.alamat || '').toLowerCase().includes(lower)
        );
    }
    
    return result;
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
        <div class="filter-toolbar">
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input v-model="searchQuery" type="text" placeholder="Cari nama supplier atau alamat...">
            </div>
            <div class="category-chips">
                <button 
                    v-for="grp in alphabetGroups" 
                    :key="grp"
                    class="chip"
                    :class="{'active': selectedFilter === grp}"
                    @click="selectedFilter = grp"
                >
                    {{ grp }}
                </button>
            </div>
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
.page-header h3 { margin: 0; color: var(--text-main); font-size: 24px; }
.page-header p { margin: 5px 0 0; color: var(--text-muted); font-size: 14px; }

.btn-primary { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); }
.btn-primary:hover { background: var(--primary-dark); }

.filter-toolbar { margin-bottom: 20px; }
.search-bar { position: relative; margin-bottom: 12px; }
.search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); opacity: 0.5; color: var(--text-muted); }
.search-bar input { width: 100%; padding: 12px 12px 12px 40px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); border-radius: 10px; outline: none; transition: 0.2s; box-sizing: border-box; }
.search-bar input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }

.category-chips { display: flex; gap: 8px; flex-wrap: wrap; }
.chip {
    padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border-color); background: var(--input-bg); font-size: 13px; 
    font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.chip:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-1px); }
.chip.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }

.table-container { background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: var(--bg-hover); padding: 15px; text-align: left; font-size: 13px; color: var(--text-muted); font-weight: 600; border-bottom: 1px solid var(--border-color); }
.data-table td { padding: 15px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-main); }
.data-table tr:last-child td { border-bottom: none; }

.action-buttons { display: flex; gap: 8px; }
.btn-icon { width: 32px; height: 32px; border-radius: 6px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
.btn-icon.edit { background: var(--info-bg); color: var(--info); }
.btn-icon.edit:hover { opacity: 0.8; }
.btn-icon.delete { background: var(--danger-bg); color: var(--danger); }
.btn-icon.delete:hover { background: var(--danger-border); color: white; }

.loading-state, .empty-state { padding: 40px; text-align: center; color: var(--text-muted); }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100; backdrop-filter: blur(2px); animation: fadeIn 0.2s; }
.modal-content { background: var(--bg-card); width: 100%; max-width: 500px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); overflow: hidden; animation: slideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1); }

.modal-header { padding: 20px 25px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-hover); }
.modal-header h4 { margin: 0; font-size: 18px; color: var(--text-main); }
.btn-close { background: none; border: none; font-size: 24px; color: var(--text-muted); cursor: pointer; }

.modal-body { padding: 25px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: var(--text-muted); }
.required { color: var(--danger); }
.form-input { width: 100%; padding: 10px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); border-radius: 8px; outline: none; transition: 0.2s; font-size: 14px; box-sizing: border-box; }
.form-input:focus { border-color: var(--primary); }

.modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 30px; }
.btn-cancel { padding: 10px 20px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; font-weight: 600; color: var(--text-muted); }
.btn-save { padding: 10px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
.btn-save:hover { background: var(--primary-dark); }

.alert-error { background: var(--danger-bg); color: var(--danger); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; border: 1px solid var(--danger-border); }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>
