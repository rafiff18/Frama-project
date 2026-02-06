<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import axios from 'axios';

// --- STATE ---
const isLoading = ref(false);
const period = ref('this_month'); // this_month, last_month, this_year
const stats = ref({
    total_income: 0,
    total_expense: 0,
    net_profit: 0,
    is_profitable: true
});
const transactions = ref([]);

// --- API ACTIONS ---

const fetchData = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get('/api/laporan', {
            params: { period: period.value },
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        
        stats.value = response.data.stats;
        transactions.value = response.data.transactions;
    } catch (error) {
        console.error("Gagal load laporan", error);
    } finally {
        isLoading.value = false;
    }
};

const printPdf = () => {
    // Open in new tab which triggers window.print()
    const token = localStorage.getItem('token');
    // Note: Passing token in URL or cookies is needed for simple window.open if middleware checks auth.
    // Since we are using Sanctum API token, window.open via standard GET won't carry the Header.
    // For this MVP, we will assume middleware allows it OR use a trick.
    // Simplest trick: We will utilize Axios to get the HTML blob, then open it.
    
    isLoading.value = true;
    axios.get('/api/laporan/export-pdf', {
        headers: { Authorization: `Bearer ${token}` },
        responseType: 'text' // We expect HTML string
    }).then(response => {
        const newWindow = window.open('', '_blank');
        newWindow.document.write(response.data);
        newWindow.document.close();
    }).catch(e => {
        alert("Gagal mencetak laporan.");
    }).finally(() => {
        isLoading.value = false;
    });
};

// --- WATCHERS ---
watch(period, () => {
    fetchData();
});

onMounted(() => {
    fetchData();
});

// --- FORMATTERS ---
const formatRp = (val) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
};

</script>

<template>
    <div class="report-container">
        
        <div class="page-header">
            <div>
                <h3>📊 Laporan Keuangan</h3>
                <p>Ringkasan arus kas (Pendapatan & Pengeluaran).</p>
            </div>
            <div class="date-filter">
                <span>Periode:</span>
                <select v-model="period">
                    <option value="this_month">Bulan Ini</option>
                    <option value="last_month">Bulan Lalu</option>
                    <option value="this_year">Tahun Ini</option>
                </select>
                <button @click="printPdf" class="btn-print" :disabled="isLoading">print/expport</button>
            </div>
        </div>

        <div v-if="isLoading" class="loading-state">Memuat data laporan...</div>

        <div v-else>
            <!-- STATS GRID -->
            <div class="stats-grid">
                <!-- INCOME -->
                <div class="stat-card">
                    <div class="stat-icon income">💰</div>
                    <div class="stat-details">
                        <span class="stat-title">Total Pemasukan</span>
                        <h2 class="stat-value">{{ formatRp(stats.total_income) }}</h2>
                        <span class="stat-desc">Dari Penjualan Obat</span>
                    </div>
                </div>

                <!-- EXPENSE -->
                <div class="stat-card">
                    <div class="stat-icon expense">💸</div>
                    <div class="stat-details">
                        <span class="stat-title">Total Pengeluaran</span>
                        <h2 class="stat-value">{{ formatRp(stats.total_expense) }}</h2>
                        <span class="stat-desc">Belanja Stok (Supplier)</span>
                    </div>
                </div>

                <!-- PROFIT -->
                <div class="stat-card" :class="stats.is_profitable ? 'bg-green' : 'bg-red'">
                    <div class="stat-icon profit">📈</div>
                    <div class="stat-details">
                        <span class="stat-title">Keuntungan Bersih</span>
                        <h2 class="stat-value">{{ formatRp(stats.net_profit) }}</h2>
                        <span class="stat-desc">{{ stats.is_profitable ? 'Profit Surplus' : 'Defisit' }}</span>
                    </div>
                </div>
            </div>

            <!-- RECENT TRANSACTIONS -->
            <div class="history-section card">
                <div class="card-header">
                    <h4>📄 Riwayat Transaksi (Gabungan)</h4>
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
                    <div v-if="transactions.length === 0" class="empty-list">Belum ada transaksi pada periode ini.</div>
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
.btn-print:disabled { opacity: 0.7; cursor: wait; }

/* STATS GRID */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
.stat-card { background: white; padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid #f1f5f9; }
.bg-green { background: #ecfdf5; border-color: #10b981; }
.bg-red { background: #fef2f2; border-color: #ef4444; }

.stat-icon { font-size: 24px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
.stat-icon.income { background: #ecfdf5; color: #10b981; }
.stat-icon.expense { background: #fef2f2; color: #ef4444; }
.stat-icon.profit { background: white; border: 1px solid #e2e8f0; }

.stat-title { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-value { margin: 5px 0; font-size: 20px; font-weight: 800; color: #0f172a; }
.stat-desc { font-size: 11px; color: #94a3b8; }

/* HISTORY LIST */
.card { background: white; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.card-header { padding: 20px; border-bottom: 1px solid #f8fafc; }
.card-header h4 { margin: 0; font-size: 16px; color: #0f172a; font-weight: 700; }

.history-list { padding: 0; max-height: 400px; overflow-y: auto; }
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

.loading-state, .empty-list { padding: 40px; text-align: center; color: #94a3b8; }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>