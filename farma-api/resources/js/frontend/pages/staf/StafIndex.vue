<script setup>
import { ref, computed } from 'vue';

// --- DUMMY DATA STAF ---
const staffList = ref([
    { id: 1, name: 'Dr. Sarah Amalia', email: 'sarah@medicare.com', role: 'Superadmin', status: 'Active', joined: '10 Jan 2025', avatar: '👩‍⚕️' },
    { id: 2, name: 'Budi Santoso, S.Farm', email: 'budi.apotek@medicare.com', role: 'Apoteker', status: 'Active', joined: '12 Feb 2025', avatar: '👨‍🔬' },
    { id: 3, name: 'Siti Aminah', email: 'siti.kasir@medicare.com', role: 'Kasir', status: 'Active', joined: '01 Mar 2025', avatar: '🧕' },
    { id: 4, name: 'Joko Anwar', email: 'joko.gudang@medicare.com', role: 'Staf Gudang', status: 'Inactive', joined: '15 Mar 2025', avatar: '👨‍🔧' },
    { id: 5, name: 'Rina Wati', email: 'rina.kasir@medicare.com', role: 'Kasir', status: 'Active', joined: '20 Mar 2025', avatar: '👩‍💼' },
]);

const searchQuery = ref("");

// Filter Pencarian
const filteredStaff = computed(() => {
    return staffList.value.filter(user => 
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.email.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.role.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Helper Warna Role
const getRoleBadge = (role) => {
    if (role === 'Superadmin') return 'role-purple';
    if (role === 'Apoteker') return 'role-green';
    if (role === 'Kasir') return 'role-blue';
    return 'role-gray';
};

// Helper Warna Status
const getStatusBadge = (status) => {
    return status === 'Active' ? 'status-active' : 'status-inactive';
};
</script>

<template>
    <div class="staf-container">
        
        <div class="page-header">
            <div class="header-text">
                <h3>👥 Kelola Staf & Akses</h3>
                <p>Atur akun pengguna, role, dan izin akses sistem.</p>
            </div>
            <button class="btn-invite">➕ Tambah Karyawan Baru</button>
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
            <table class="staf-table">
                <thead>
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Role / Jabatan</th>
                        <th>Tanggal Gabung</th>
                        <th>Status Akun</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in filteredStaff" :key="user.id">
                        <td>
                            <div class="user-info">
                                <div class="avatar-circle">{{ user.avatar }}</div>
                                <div class="user-text">
                                    <span class="user-name">{{ user.name }}</span>
                                    <span class="user-email">{{ user.email }}</span>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="role-badge" :class="getRoleBadge(user.role)">
                                {{ user.role }}
                            </span>
                        </td>

                        <td class="col-date">{{ user.joined }}</td>

                        <td>
                            <div class="status-indicator" :class="getStatusBadge(user.status)">
                                <div class="dot"></div>
                                <span>{{ user.status === 'Active' ? 'Aktif' : 'Non-Aktif' }}</span>
                            </div>
                        </td>

                        <td class="text-right">
                            <button class="btn-action edit" title="Edit Akses">✏️</button>
                            <button class="btn-action delete" title="Hapus User">🗑️</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="filteredStaff.length === 0" class="empty-state">
                <p>Data karyawan tidak ditemukan.</p>
            </div>
        </div>

    </div>
</template>

<style scoped>
.staf-container { animation: fadeIn 0.5s ease; }

/* HEADER */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.header-text h3 { margin: 0 0 5px; color: #0f172a; font-size: 20px; font-weight: 800; }
.header-text p { margin: 0; color: #64748b; font-size: 14px; }

.btn-invite {
    background: #10b981; color: white; border: none; padding: 12px 20px;
    border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3); font-size: 13px;
}
.btn-invite:hover { background: #059669; transform: translateY(-2px); }

/* TOOLBAR */
.toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.search-wrapper {
    background: white; border: 1px solid #e2e8f0; padding: 10px 16px; border-radius: 10px;
    display: flex; align-items: center; gap: 10px; width: 350px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.search-wrapper input { border: none; outline: none; width: 100%; font-size: 14px; color: #334155; }
.total-badge { font-size: 12px; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 6px 12px; border-radius: 20px; }

/* TABLE CARD */
.table-card { background: white; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.staf-table { width: 100%; border-collapse: collapse; }

.staf-table th {
    text-align: left; padding: 16px 24px; background: #f8fafc; font-size: 11px;
    text-transform: uppercase; font-weight: 700; color: #64748b; border-bottom: 1px solid #e2e8f0; letter-spacing: 0.5px;
}

.staf-table td { padding: 16px 24px; border-bottom: 1px solid #f8fafc; vertical-align: middle; }
.staf-table tr:hover { background: #fcfcfc; }

/* USER INFO */
.user-info { display: flex; align-items: center; gap: 12px; }
.avatar-circle {
    width: 40px; height: 40px; background: #ecfdf5; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px;
    border: 2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}
.user-text { display: flex; flex-direction: column; }
.user-name { font-weight: 700; color: #0f172a; font-size: 14px; }
.user-email { font-size: 12px; color: #64748b; }

/* ROLES BADGE */
.role-badge { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block; }
.role-purple { background: #f3e8ff; color: #9333ea; } /* Superadmin */
.role-green { background: #ecfdf5; color: #10b981; } /* Apoteker */
.role-blue { background: #eff6ff; color: #3b82f6; }   /* Kasir */
.role-gray { background: #f1f5f9; color: #64748b; }   /* Gudang */

/* STATUS */
.status-indicator { display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 600; }
.dot { width: 6px; height: 6px; border-radius: 50%; }
.status-active .dot { background: #10b981; }
.status-active { color: #0f172a; }
.status-inactive .dot { background: #cbd5e1; }
.status-inactive { color: #94a3b8; }

.col-date { font-size: 13px; color: #64748b; font-family: monospace; }

/* ACTIONS */
.text-right { text-align: right; }
.btn-action {
    width: 32px; height: 32px; border-radius: 8px; border: 1px solid #e2e8f0;
    background: white; cursor: pointer; margin-left: 6px; transition: 0.2s;
}
.btn-action:hover { background: #f8fafc; border-color: #cbd5e1; }
.btn-action.delete:hover { background: #fef2f2; border-color: #fca5a5; }

.empty-state { padding: 40px; text-align: center; color: #94a3b8; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>