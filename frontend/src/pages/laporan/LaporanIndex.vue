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
const filterTransaction = ref('semua'); // semua, income, expense

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

// --- COMPUTED ---
const filteredTransactions = computed(() => {
    if (filterTransaction.value === 'semua') {
        return transactions.value;
    }
    return transactions.value.filter(trx => trx.type === filterTransaction.value);
});

</script>

<template>
    <div class="report-container">
        
        <div class="page-header">
            <div>
                <h3>📊 Laporan Keuangan</h3>
                <p>Ringkasan arus kas (Pendapatan & Pengeluaran).</p>
            </div>
            <div class="header-actions">
                <button @click="printPdf" class="btn-print" :disabled="isLoading">🖨️ Cetak PDF</button>
            </div>
        </div>

        <div class="category-chips">
            <button class="chip" :class="{'active': period === 'this_month'}" @click="period = 'this_month'">Bulan Ini</button>
            <button class="chip" :class="{'active': period === 'last_month'}" @click="period = 'last_month'">Bulan Lalu</button>
            <button class="chip" :class="{'active': period === 'this_year'}" @click="period = 'this_year'">Tahun Ini</button>
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
                <div class="card-header flex-between">
                    <h4>📄 Riwayat Transaksi (Gabungan)</h4>
                    <div class="category-chips small-chips">
                        <button class="chip" :class="{'active': filterTransaction === 'semua'}" @click="filterTransaction = 'semua'">Semua</button>
                        <button class="chip" :class="{'active': filterTransaction === 'income'}" @click="filterTransaction = 'income'">Penjualan</button>
                        <button class="chip" :class="{'active': filterTransaction === 'expense'}" @click="filterTransaction = 'expense'">Restock</button>
                    </div>
                </div>
                <div class="history-list">
                    <div v-for="trx in filteredTransactions" :key="trx.id" class="history-item">
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
                    <div v-if="filteredTransactions.length === 0" class="empty-list">Belum ada transaksi pada periode/filter ini.</div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.report-container { animation: fadeIn 0.5s ease; }

/* HEADER */
.page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.page-header h3 { margin: 0 0 5px; color: var(--text-main); font-size: 20px; font-weight: 800; }
.page-header p { margin: 0; color: var(--text-muted); font-size: 14px; }

.header-actions { display: flex; align-items: center; gap: 10px; }
.btn-print { background: var(--sidebar-bg); color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.2s; box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.2); }
.btn-print:hover { background: var(--sidebar-hover); transform: translateY(-2px); }
.btn-print:disabled { opacity: 0.7; cursor: wait; transform: none; box-shadow: none; }

.category-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 25px; }
.chip {
    padding: 8px 16px; border-radius: 20px; border: 1px solid var(--border-color); background: var(--input-bg); font-size: 13px; 
    font-weight: 600; color: var(--text-muted); cursor: pointer; transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}
.chip:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-1px); }
.chip.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2); }

/* STATS GRID */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
.stat-card { background: var(--bg-card); padding: 20px; border-radius: 16px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid var(--border-color); }
.bg-green { background: var(--success-bg); border-color: var(--success); }
.bg-red { background: var(--danger-bg); border-color: var(--danger); }

.stat-icon { font-size: 24px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }
.stat-icon.income { background: var(--success-bg); color: var(--success); }
.stat-icon.expense { background: var(--danger-bg); color: var(--danger); }
.stat-icon.profit { background: var(--bg-card); border: 1px solid var(--border-color); }

.stat-title { font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
.stat-value { margin: 5px 0; font-size: 20px; font-weight: 800; color: var(--text-main); }
.stat-desc { font-size: 11px; color: var(--text-muted); }

/* HISTORY LIST */
.card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.card-header { padding: 20px; border-bottom: 1px solid var(--border-color); }
.card-header h4 { margin: 0; font-size: 16px; color: var(--text-main); font-weight: 700; }

.flex-between { display: flex; justify-content: space-between; align-items: center; }
.small-chips { margin-bottom: 0; }
.small-chips .chip { padding: 6px 12px; font-size: 12px; }

.history-list { padding: 0; max-height: 400px; overflow-y: auto; }
.history-item { display: flex; align-items: center; gap: 15px; padding: 15px 20px; border-bottom: 1px solid var(--border-color); }
.history-item:last-child { border-bottom: none; }

.trx-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.trx-icon.income { background: var(--success-bg); color: var(--success); }
.trx-icon.expense { background: var(--danger-bg); color: var(--danger); }

.trx-info { flex: 1; display: flex; flex-direction: column; }
.trx-desc { font-size: 13px; font-weight: 600; color: var(--text-main); }
.trx-date { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

.trx-amount { font-weight: 700; font-size: 13px; }
.trx-amount.income { color: var(--success); }
.trx-amount.expense { color: var(--danger); }

.loading-state, .empty-list { padding: 40px; text-align: center; color: var(--text-muted); }

@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>