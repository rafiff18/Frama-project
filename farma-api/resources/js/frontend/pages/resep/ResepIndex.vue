<script setup>
import { ref, computed } from 'vue';

// --- DUMMY DATA RESEP ---
const prescriptions = ref([
    { id: 1, no_resep: 'RSP-2026-001', patient: 'Bpk. Budi Santoso', doctor: 'Dr. Sarah Sp.PD', date: '02 Feb 2026', status: 'pending', notes: 'Alergi Penicillin' },
    { id: 2, no_resep: 'RSP-2026-002', patient: 'An. Dinda (5 Th)', doctor: 'Dr. Andri Sp.A', date: '02 Feb 2026', status: 'processing', notes: 'Sirup racikan' },
    { id: 3, no_resep: 'RSP-2026-003', patient: 'Ibu Siti Aminah', doctor: 'Dr. Sarah Sp.PD', date: '01 Feb 2026', status: 'completed', notes: '-' },
    { id: 4, no_resep: 'RSP-2026-004', patient: 'Sdr. Kevin', doctor: 'Dr. Junaedi', date: '01 Feb 2026', status: 'cancelled', notes: 'Obat kosong' },
    { id: 5, no_resep: 'RSP-2026-005', patient: 'Bpk. Joko', doctor: 'Dr. Sarah Sp.PD', date: '02 Feb 2026', status: 'pending', notes: '-' },
]);

const searchQuery = ref("");
const filterStatus = ref("all");

// Filter Logika
const filteredList = computed(() => {
    return prescriptions.value.filter(item => {
        const matchSearch = item.patient.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                            item.no_resep.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchStatus = filterStatus.value === 'all' || item.status === filterStatus.value;
        return matchSearch && matchStatus;
    });
});

// Helper Warna Badge
const getStatusBadge = (status) => {
    switch(status) {
        case 'pending': return 'badge-yellow'; // Menunggu
        case 'processing': return 'badge-blue'; // Meracik
        case 'completed': return 'badge-green'; // Selesai
        case 'cancelled': return 'badge-red'; // Batal
        default: return 'badge-gray';
    }
};

// Helper Teks Status
const getStatusText = (status) => {
    switch(status) {
        case 'pending': return 'Menunggu Konfirmasi';
        case 'processing': return 'Sedang Diracik';
        case 'completed': return 'Selesai / Diambil';
        case 'cancelled': return 'Dibatalkan';
        default: return status;
    }
};
</script>

<template>
    <div class="resep-container">
        
        <div class="page-header">
            <div class="header-left">
                <h3>📄 Daftar Resep Masuk</h3>
                <p>Kelola penebusan resep obat dari dokter.</p>
            </div>
            <button class="btn-add">➕ Input Resep Manual</button>
        </div>

        <div class="filter-bar">
            <div class="search-box">
                <span class="icon">🔍</span>
                <input type="text" v-model="searchQuery" placeholder="Cari nama pasien atau No. Resep...">
            </div>
            
            <div class="status-tabs">
                <button :class="{ active: filterStatus === 'all' }" @click="filterStatus = 'all'">Semua</button>
                <button :class="{ active: filterStatus === 'pending' }" @click="filterStatus = 'pending'">Menunggu</button>
                <button :class="{ active: filterStatus === 'processing' }" @click="filterStatus = 'processing'">Diproses</button>
                <button :class="{ active: filterStatus === 'completed' }" @click="filterStatus = 'completed'">Selesai</button>
            </div>
        </div>

        <div class="resep-grid">
            <div v-for="item in filteredList" :key="item.id" class="resep-card">
                <div class="card-header">
                    <span class="resep-id">#{{ item.no_resep }}</span>
                    <span class="date">{{ item.date }}</span>
                </div>
                
                <div class="card-body">
                    <div class="info-row">
                        <span class="label">Pasien:</span>
                        <span class="value bold">{{ item.patient }}</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Dokter:</span>
                        <span class="value">{{ item.doctor }}</span>
                    </div>
                    <div class="info-row notes" v-if="item.notes !== '-'">
                        <span class="label">Catatan:</span>
                        <span class="value warning">{{ item.notes }}</span>
                    </div>
                </div>

                <div class="card-footer">
                    <span class="status-pill" :class="getStatusBadge(item.status)">
                        {{ getStatusText(item.status) }}
                    </span>
                    <button class="btn-detail">Lihat Detail →</button>
                </div>
            </div>

            <div v-if="filteredList.length === 0" class="empty-state">
                <div class="empty-icon">📂</div>
                <p>Tidak ada data resep yang ditemukan.</p>
            </div>
        </div>

    </div>
</template>

<style scoped>
.resep-container { animation: fadeIn 0.5s ease; }

/* HEADER */
.page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; }
.header-left h3 { margin: 0 0 5px; color: #0f172a; font-size: 20px; font-weight: 700; }
.header-left p { margin: 0; color: #64748b; font-size: 14px; }

.btn-add {
    background: #10b981; color: white; border: none; padding: 12px 20px;
    border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.2s;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}
.btn-add:hover { background: #059669; transform: translateY(-2px); }

/* FILTER BAR */
.filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }

.search-box {
    background: white; border: 1px solid #e2e8f0; padding: 10px 16px; border-radius: 10px;
    display: flex; align-items: center; gap: 10px; width: 320px;
}
.search-box input { border: none; outline: none; width: 100%; font-size: 14px; color: #334155; }

.status-tabs { display: flex; background: #f1f5f9; padding: 4px; border-radius: 10px; gap: 4px; }
.status-tabs button {
    border: none; background: transparent; padding: 8px 16px; border-radius: 8px;
    font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer; transition: 0.2s;
}
.status-tabs button.active { background: white; color: #10b981; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

/* GRID LAYOUT */
.resep-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

/* CARD STYLE */
.resep-card {
    background: white; border-radius: 16px; padding: 20px; border: 1px solid #f1f5f9;
    transition: 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); display: flex; flex-direction: column;
}
.resep-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05); border-color: #10b981; }

.card-header { display: flex; justify-content: space-between; margin-bottom: 15px; border-bottom: 1px solid #f8fafc; padding-bottom: 12px; }
.resep-id { font-weight: 700; color: #10b981; font-family: monospace; font-size: 14px; }
.date { font-size: 12px; color: #94a3b8; }

.card-body { flex: 1; display: flex; flex-direction: column; gap: 8px; margin-bottom: 20px; }
.info-row { display: flex; justify-content: space-between; font-size: 13px; }
.label { color: #64748b; }
.value { color: #334155; font-weight: 500; text-align: right; }
.value.bold { font-weight: 700; color: #0f172a; font-size: 14px; }
.value.warning { color: #f59e0b; font-weight: 600; }

.card-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f8fafc; padding-top: 15px; }

/* BADGES */
.status-pill { font-size: 11px; padding: 6px 12px; border-radius: 20px; font-weight: 700; text-transform: uppercase; }
.badge-yellow { background: #fffbeb; color: #d97706; }
.badge-blue { background: #eff6ff; color: #3b82f6; }
.badge-green { background: #ecfdf5; color: #10b981; }
.badge-red { background: #fef2f2; color: #ef4444; }
.badge-gray { background: #f8fafc; color: #64748b; }

.btn-detail { background: none; border: none; color: #10b981; font-weight: 600; font-size: 13px; cursor: pointer; }
.btn-detail:hover { text-decoration: underline; }

.empty-state { grid-column: 1 / -1; text-align: center; padding: 50px; color: #cbd5e1; }
.empty-icon { font-size: 40px; margin-bottom: 10px; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>