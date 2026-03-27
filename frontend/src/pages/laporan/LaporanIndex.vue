<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { supabase } from '../../lib/supabase'; // Fixed import

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

// --- HELPERS FOR DATES ---
const getDateRange = (p) => {
    const now = new Date();
    let start, end;
    
    if (p === 'this_month') {
        start = new Date(now.getFullYear(), now.getMonth(), 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59);
    } else if (p === 'last_month') {
        start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        end = new Date(now.getFullYear(), now.getMonth(), 0, 23, 59, 59);
    } else if (p === 'this_year') {
        start = new Date(now.getFullYear(), 0, 1);
        end = new Date(now.getFullYear(), 11, 31, 23, 59, 59);
    }
    return { 
        start: start.toISOString(), 
        end: end.toISOString() 
    };
};

const formatDateDay = (dateString) => {
    const opts = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', opts);
};

// --- API ACTIONS ---

const fetchData = async () => {
    isLoading.value = true;
    try {
        const { start, end } = getDateRange(period.value);

        // Fetch Penjualan (Income)
        const { data: penjualan, error: penErr } = await supabase
            .from('penjualan')
            .select('*')
            .gte('created_at', start)
            .lte('created_at', end);
            
        if (penErr) throw penErr;

        // Fetch Penerimaan (Expense)
        const { data: penerimaan, error: pnmErr } = await supabase
            .from('penerimaan')
            .select('*')
            .gte('tgl_penerimaan', start)
            .lte('tgl_penerimaan', end);
            
        if (pnmErr) throw pnmErr;

        let total_income = 0;
        let total_expense = 0;
        let trxs = [];

        // Aggregating Income
        (penjualan || []).forEach(p => {
            total_income += p.total_harga;
            trxs.push({
                id: `pen-${p.id}`,
                type: 'income',
                amount: p.total_harga,
                date: formatDateDay(p.created_at),
                rawDate: new Date(p.created_at),
                desc: `Penjualan ${p.no_transaksi}`,
                status: 'Selesai'
            });
        });

        // Aggregating Expense
        (penerimaan || []).forEach(p => {
            total_expense += p.total_harga;
            trxs.push({
                id: `pnm-${p.id}`,
                type: 'expense',
                amount: p.total_harga,
                date: formatDateDay(p.tgl_penerimaan),
                rawDate: new Date(p.tgl_penerimaan),
                desc: `Restock Stok (Faktur: ${p.no_faktur})`,
                status: 'Selesai'
            });
        });

        // Sort descending by date
        trxs.sort((a, b) => b.rawDate - a.rawDate);

        stats.value = {
            total_income,
            total_expense,
            net_profit: total_income - total_expense,
            is_profitable: (total_income - total_expense) >= 0
        };
        
        transactions.value = trxs;
        
    } catch (error) {
        console.error("Gagal load laporan", error);
    } finally {
        isLoading.value = false;
    }
};

const printPdf = () => {
    window.print(); // Since we don't have a backend PDF generator anymore
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