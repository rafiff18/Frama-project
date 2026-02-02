<script setup>
import { ref } from 'vue';

// --- DATA DUMMY STATISTIK ---
const stats = ref([
    { title: 'Total Omzet (Feb)', value: 'Rp 45.250.000', trend: '+12%', isPositive: true, icon: '💰' },
    { title: 'Keuntungan Bersih', value: 'Rp 18.500.000', trend: '+8%', isPositive: true, icon: '📈' },
    { title: 'Pengeluaran (Stok)', value: 'Rp 12.000.000', trend: '-2%', isPositive: false, icon: '💸' },
]);

// --- DATA DUMMY TRANSAKSI ---
const transactions = ref([
    { id: 1, date: '02 Feb 2026', desc: 'Penjualan Obat (Kasir)', type: 'income', amount: 1250000, status: 'Selesai' },
    { id: 2, date: '02 Feb 2026', desc: 'Restock Paracetamol (Supplier A)', type: 'expense', amount: 4500000, status: 'Lunas' },
    { id: 3, date: '01 Feb 2026', desc: 'Penjualan Resep Dr. Sarah', type: 'income', amount: 850000, status: 'Selesai' },
    { id: 4, date: '01 Feb 2026', desc: 'Bayar Listrik & Air', type: 'expense', amount: 1200000, status: 'Lunas' },
    { id: 5, date: '31 Jan 2026', desc: 'Penjualan Alat Kesehatan', type: 'income', amount: 2300000, status: 'Selesai' },
]);

// --- DATA GRAFIK (CSS BARS) ---
const chartData = ref([
    { label: 'Sen', height: '40%' },
    { label: 'Sel', height: '60%' },
    { label: 'Rab', height: '35%' },
    { label: 'Kam', height: '80%' },
    { label: 'Jum', height: '55%' },
    { label: 'Sab', height: '90%' },
    { label: 'Min', height: '70%' },
]);

const formatRp = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
};
</script>

<template>
    <div class="report-container">
        
        <div class="page-header">
            <div>
                <h3>📊 Laporan Keuangan</h3>
                <p>Ringkasan arus kas apotik periode Februari 2026.</p>
            </div>
            <div class="date-filter">
                <span>Periode:</span>
                <select>
                    <option>Bulan Ini</option>
                    <option>Bulan Lalu</option>
                    <option>Tahun Ini</option>
                </select>
                <button class="btn-print">🖨️ Cetak PDF</button>
            </div>
        </div>

        <div class="stats-grid">
            <div v-for="(stat, index) in stats" :key="index" class="stat-card">
                <div class="stat-icon">{{ stat.icon }}</div>
                <div class="stat-details">
                    <span class="stat-title">{{ stat.title }}</span>
                    <h2 class="stat-value">{{ stat.value }}</h2>
                    <span class="stat-trend" :class="stat.isPositive ? 'trend-up' : 'trend-down'">
                        {{ stat.trend }} {{ stat.isPositive ? 'Naik' : 'Turun' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="content-split">
            <div class="chart-section card">
                <div class="card-header">
                    <h4>📈 Tren Pendapatan Mingguan</h4>
                </div>
                <div class="chart-body">
                    <div class="bar-container">
                        <div v-for="(bar, i) in chartData" :key="i" class="bar-wrapper">
                            <div class="bar" :style="{ height: bar.height }">
                                <div class="tooltip">{{ bar.height }}</div>
                            </div>
                            <span class="bar-label">{{ bar.label }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="history-section card">
                <div class="card-header">
                    <h4>📄 Transaksi Terakhir</h4>
                    <a href="#" class="view-all">Lihat Semua</a>
                </div>
                <div class="history-list">
                    <div v-for="trx in transactions" :key="trx.id" class="history-item">
                        <div class="trx-icon" :class="trx.type">
                            {{ trx.type === 'income' ? '↙️' : '↗️' }}
                        </div>
                        <div class="trx-info">
                            <span class="trx-desc">{{ trx.desc }}</span>
                            <span class="trx-date">{{ trx.date }} • {{ trx.status }}</span>
                        </div>
                        <div class="trx-amount" :class="trx.type">
                            {{ trx.type === 'income' ? '+' : '-' }} {{ formatRp(trx.amount) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.report-container { animation: fadeIn 0.5s ease; }

/* HEADER */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
.page-header h3 { margin: 0 0 5px; color: #0f172a; font-size: 20px; font-weight: 800; }
.page-header p { margin: 0; color: #64748b; font-size: 14px; }

.date-filter { display: flex; align-items: center; gap: 10px; font-size: 13px; color: #64748b; }
.date-filter select { padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1; outline: none; }
.btn-print { background: #0f172a; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; }

/* STATS GRID */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
.stat-card { background: white; padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; }
.stat-icon { font-size: 28px; background: #f8fafc; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }

.stat-title { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-value { margin: 5px 0; font-size: 20px; font-weight: 800; color: #0f172a; }
.stat-trend { font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; }
.trend-up { background: #ecfdf5; color: #10b981; }
.trend-down { background: #fef2f2; color: #ef4444; }

/* SPLIT CONTENT */
.content-split { display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; }
.card { background: white; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.card-header { padding: 20px; border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; align-items: center; }
.card-header h4 { margin: 0; font-size: 16px; color: #0f172a; font-weight: 700; }
.view-all { font-size: 12px; color: #10b981; text-decoration: none; font-weight: 600; }

/* CHART (CSS BARS) */
.chart-body { padding: 30px; height: 250px; display: flex; align-items: flex-end; justify-content: center; }
.bar-container { display: flex; justify-content: space-between; width: 100%; height: 100%; align-items: flex-end; padding: 0 20px; }
.bar-wrapper { display: flex; flex-direction: column; align-items: center; gap: 10px; height: 100%; justify-content: flex-end; width: 100%; }
.bar { width: 20px; background: #10b981; border-radius: 10px 10px 0 0; position: relative; transition: height 0.5s ease; opacity: 0.8; }
.bar:hover { opacity: 1; cursor: pointer; }
.bar-label { font-size: 11px; color: #94a3b8; font-weight: 600; }

/* HISTORY LIST */
.history-list { padding: 0; }
.history-item { display: flex; align-items: center; gap: 15px; padding: 15px 20px; border-bottom: 1px solid #f8fafc; }
.history-item:last-child { border-bottom: none; }

.trx-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.trx-icon.income { background: #ecfdf5; color: #10b981; }
.trx-icon.expense { background: #fef2f2; color: #ef4444; }

.trx-info { flex: 1; display: flex; flex-direction: column; }
.trx-desc { font-size: 13px; font-weight: 600; color: #334155; }
.trx-date { font-size: 11px; color: #94a3b8; margin-top: 2px; }

.trx-amount { font-weight: 700; font-size: 13px; }
.trx-amount.income { color: #10b981; }
.trx-amount.expense { color: #ef4444; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>