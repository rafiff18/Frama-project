<script setup>
import { ref, computed, reactive, onMounted } from 'vue';
import axios from 'axios';

// State
const staffList = ref([]);
const searchQuery = ref("");
const isLoading = ref(false);
const showModal = ref(false);
const isEditMode = ref(false);
const formErrors = ref({});

const form = reactive({
    id: null,
    name: '',
    email: '',
    password: '',
    role: 'kasir', // default
    status: 'Active' // Backend doesnt have status yet, so maybe just UI ? or assume active
});

const errorMsg = ref("");

// Fetch Data
const fetchStaff = async () => {
    isLoading.value = true;
    errorMsg.value = "";
    try {
        const response = await axios.get('/api/users', {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        if (Array.isArray(response.data)) {
            staffList.value = response.data;
        } else {
            console.error("Invalid data format:", response.data);
            throw new Error("Format data tidak valid");
        }
    } catch (error) {
        console.error("Gagal ambil data staff", error);
        errorMsg.value = "Gagal memuat data staf. Silakan coba lagi.";
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchStaff();
});

// Computed
// Computed
const filteredStaff = computed(() => {
    return staffList.value.filter(user => {
        const name = (user.name || '').toLowerCase();
        const email = (user.email || '').toLowerCase();
        const role = (user.role || '').toLowerCase();
        const query = searchQuery.value.toLowerCase();

        return name.includes(query) || email.includes(query) || role.includes(query);
    });
});

// Actions
const openModal = (user = null) => {
    formErrors.value = {};
    if (user) {
        isEditMode.value = true;
        form.id = user.id;
        form.name = user.name;
        form.email = user.email;
        form.role = user.role;
        form.password = ''; // Kosongkan saat edit
    } else {
        isEditMode.value = false;
        form.id = null;
        form.name = '';
        form.email = '';
        form.role = 'kasir';
        form.password = '';
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveUser = async () => {
    formErrors.value = {};
    try {
        const token = localStorage.getItem('token');
        const headers = { Authorization: `Bearer ${token}` };

        if (isEditMode.value) {
            await axios.put(`/api/users/${form.id}`, form, { headers });
        } else {
            await axios.post('/api/users', form, { headers });
        }
        
        await fetchStaff(); // Refresh data
        closeModal();
    } catch (error) {
        if (error.response && error.response.status === 422) {
            formErrors.value = error.response.data;
        } else {
            console.error("Gagal simpan user", error);
            alert("Terjadi kesalahan saat menyimpan data.");
        }
    }
};

const deleteUser = async (id) => {
    if (!confirm("Yakin ingin menghapus user ini?")) return;

    try {
        const token = localStorage.getItem('token');
        await axios.delete(`/api/users/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        await fetchStaff();
    } catch (error) {
        console.error("Gagal hapus user", error);
        alert("Gagal menghapus user.");
    }
};

// Helper Warna Role
const getRoleBadge = (role) => {
    if (role === 'superadmin') return 'role-purple';
    if (role === 'apoteker') return 'role-green';
    if (role === 'kasir') return 'role-blue';
    if (role === 'owner') return 'role-orange';
    return 'role-gray';
};
</script>

<template>
    <div class="staf-container">
        
        <div class="page-header">
            <div class="header-text">
                <h3>👥 Kelola Staf & Akses</h3>
                <p>Atur akun pengguna, role, dan izin akses sistem.</p>
            </div>
            <button @click="openModal()" class="btn-invite">➕ Tambah Karyawan Baru</button>
        </div>

        <div class="toolbar">
            <div class="search-wrapper">
                <span class="icon">🔍</span>
                <input type="text" v-model="searchQuery" placeholder="Cari nama, email, atau role...">
            </div>
            <div class="filter-actions">
                <span class="total-badge">Total: {{ filteredStaff.length }} Staf</span>
            </div>
        </div>

        <div class="table-card">
            <div v-if="errorMsg" class="error-state">{{ errorMsg }}</div>
            <div v-if="isLoading" class="loading-state">Mengambil data...</div>
            
            <table v-else class="staf-table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Role / Jabatan</th>
                        <th>Tanggal Gabung</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in filteredStaff" :key="user.id">
                        <td>
                            <div class="user-info">
                                <div class="avatar-circle">{{ (user.name || '?').charAt(0).toUpperCase() }}</div>
                                <div class="user-text">
                                    <span class="user-name">{{ user.name || 'Tanpa Nama' }}</span>
                                    <span class="user-email">{{ user.email || '-' }}</span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="role-badge" :class="getRoleBadge(user.role)">
                                {{ user.role || 'Unknown' }}
                            </span>
                        </td>

                        <td class="col-date">{{ user.created_at ? new Date(user.created_at).toLocaleDateString() : '-' }}</td>

                        <td class="text-right">
                            <button @click="openModal(user)" class="btn-action edit" title="Edit Akses">✏️</button>
                            <button @click="deleteUser(user.id)" class="btn-action delete" title="Hapus User">🗑️</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!isLoading && filteredStaff.length === 0" class="empty-state">
                <p>Data karyawan tidak ditemukan.</p>
            </div>
        </div>

        <!-- MODAL -->
        <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ isEditMode ? 'Edit User' : 'Tambah User Baru' }}</h3>
                    <button @click="closeModal" class="btn-close">×</button>
                </div>
                <div class="modal-body">
                    <form @submit.prevent="saveUser">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input v-model="form.name" type="text" required class="form-input">
                            <small v-if="formErrors.name" class="text-error">{{ formErrors.name[0] }}</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.email" type="email" required class="form-input">
                            <small v-if="formErrors.email" class="text-error">{{ formErrors.email[0] }}</small>
                        </div>

                        <div class="form-group">
                            <label>Role</label>
                            <select v-model="form.role" class="form-select">
                                <option value="superadmin">Superadmin</option>
                                <option value="apoteker">Apoteker</option>
                                <option value="kasir">Kasir</option>
                                <option value="owner">Owner</option>
                            </select>
                            <small v-if="formErrors.role" class="text-error">{{ formErrors.role[0] }}</small>
                        </div>

                        <div class="form-group">
                            <label>Password <span v-if="isEditMode">(Biarkan kosong jika tidak diganti)</span></label>
                            <input v-model="form.password" type="password" :required="!isEditMode" class="form-input" placeholder="••••••">
                            <small v-if="formErrors.password" class="text-error">{{ formErrors.password[0] }}</small>
                        </div>

                        <div class="modal-actions">
                            <button type="button" @click="closeModal" class="btn-cancel">Batal</button>
                            <button type="submit" class="btn-save">{{ isEditMode ? 'Simpan Perubahan' : 'Buat User' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.staf-container { animation: fadeIn 0.5s ease; position: relative; }

/* HEADER & TOOLBAR same as before */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.header-text h3 { margin: 0 0 5px; color: var(--text-main); font-size: 20px; font-weight: 800; }
.header-text p { margin: 0; color: var(--text-muted); font-size: 14px; }
.btn-invite { background: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); font-size: 13px; }
.btn-invite:hover { background: var(--primary-dark); transform: translateY(-2px); }

.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.search-wrapper { background: var(--input-bg); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 10px; display: flex; align-items: center; gap: 10px; width: 350px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
.search-wrapper input { border: none; outline: none; width: 100%; font-size: 14px; color: var(--text-main); background: transparent; }
.total-badge { font-size: 12px; font-weight: 600; color: var(--text-muted); background: var(--bg-hover); padding: 6px 12px; border-radius: 20px; }

/* TABLE */
.table-card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); min-height: 200px; }
.staf-table { width: 100%; border-collapse: collapse; }
.staf-table th { text-align: left; padding: 16px 24px; background: var(--bg-hover); font-size: 11px; text-transform: uppercase; font-weight: 700; color: var(--text-muted); border-bottom: 1px solid var(--border-color); letter-spacing: 0.5px; }
.staf-table td { padding: 16px 24px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
.staf-table tr:hover { background: var(--bg-hover); }

.user-info { display: flex; align-items: center; gap: 12px; }
.avatar-circle { width: 40px; height: 40px; background: var(--success-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; border: 2px solid var(--bg-card); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
.user-text { display: flex; flex-direction: column; }
.user-name { font-weight: 700; color: var(--text-main); font-size: 14px; }
.user-email { font-size: 12px; color: var(--text-muted); }

.role-badge { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block; }
.role-purple { background: rgba(147, 51, 234, 0.1); color: #a855f7; }
.role-green { background: var(--success-bg); color: var(--success); }
.role-blue { background: var(--info-bg); color: var(--info); }
.role-orange { background: var(--warning-bg); color: var(--warning); } /* Owner */
.role-gray { background: var(--border-color); color: var(--text-muted); }

.col-date { font-size: 13px; color: var(--text-muted); font-family: monospace; }
.text-right { text-align: right; }
.btn-action { width: 32px; height: 32px; border-radius: 8px; border: 1px solid var(--border-color); background: var(--input-bg); cursor: pointer; margin-left: 6px; transition: 0.2s; }
.btn-action:hover { background: var(--bg-hover); border-color: var(--primary); }
.btn-action.delete:hover { background: var(--danger-bg); border-color: var(--danger-border); }

.empty-state { padding: 40px; text-align: center; color: var(--text-muted); }
.loading-state { padding: 40px; text-align: center; color: var(--text-muted); font-weight: 600; }
.error-state { padding: 20px; text-align: center; color: var(--danger); background: var(--danger-bg); font-weight: 500; }

/* MODAL */
.modal-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5); z-index: 100;
    display: flex; align-items: center; justify-content: center;
    backdrop-filter: blur(2px);
    animation: fadeIn 0.2s ease;
}
.modal-content {
    background: var(--bg-card); width: 100%; max-width: 500px; border-radius: 16px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}
.modal-header {
    padding: 20px 24px; border-bottom: 1px solid var(--border-color); background: var(--bg-hover);
    display: flex; justify-content: space-between; align-items: center;
}
.modal-header h3 { margin: 0; font-size: 18px; color: var(--text-main); }
.btn-close { background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted); }

.modal-body { padding: 24px; }
.form-group { margin-bottom: 16px; }
.form-group label { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 600; color: var(--text-muted); }
.form-input, .form-select {
    width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px;
    font-size: 14px; color: var(--text-main); background: var(--input-bg); outline: none; transition: 0.2s;
}
.form-input:focus, .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1); }
.text-error { color: var(--danger); font-size: 12px; margin-top: 4px; display: block; }

.modal-actions { margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px; }
.btn-cancel { padding: 10px 20px; background: var(--input-bg); border: 1px solid var(--border-color); border-radius: 8px; font-weight: 600; cursor: pointer; color: var(--text-muted); }
.btn-save { padding: 10px 20px; background: var(--primary); border: none; border-radius: 8px; font-weight: 600; cursor: pointer; color: white; }
.btn-save:hover { background: var(--primary-dark); }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>