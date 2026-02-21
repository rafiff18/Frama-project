<template>
    <div class="dashboard-container">
        
        <div class="intro-text">
            <p>Kondisi operasional terkini per hari ini.</p>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon-box green">📊</div>
                <div class="stat-info">
                    <span class="label">OMZET HARI INI</span>
                    <h4 class="value" v-if="!isLoading">{{ formatRupiah(omzetHariIni) }}</h4>
                    <h4 class="value" v-else>...</h4>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon-box orange">📦</div>
                <div class="stat-info">
                    <span class="label">STOK MENIPIS</span>
                    <h4 class="value" v-if="!isLoading">{{ stokMenipisCount }}</h4>
                    <h4 class="value" v-else>...</h4>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon-box red">⚠️</div>
                <div class="stat-info">
                    <span class="label">HAMPIR KADALUARSA</span>
                    <h4 class="value" v-if="!isLoading">{{ hampirKadaluarsaCount }}</h4>
                    <h4 class="value" v-else>...</h4>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon-box blue">💊</div>
                <div class="stat-info">
                    <span class="label">TOTAL PRODUK</span>
                    <h4 class="value" v-if="!isLoading">{{ totalProduk }}</h4>
                    <h4 class="value" v-else>...</h4>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card menu-summary-card">
                <div class="card-header">
                    <h4>🚀 Dashboard Ringkasan Menu</h4>
                </div>
                <div class="menu-grid-detailed">
                    <router-link to="/admin/kasir" class="menu-card-detailed">
                        <div class="menu-card-icon pos-icon">🛒</div>
                        <div class="menu-card-content">
                            <h5>Kasir (POS)</h5>
                            <p>Sistem Point of Sales untuk melayani transaksi pembelian obat pelanggan.</p>
                        </div>
                        <div class="menu-card-action">→</div>
                    </router-link>

                    <router-link to="/admin/inventori" class="menu-card-detailed">
                        <div class="menu-card-icon inv-icon">💊</div>
                        <div class="menu-card-content">
                            <h5>Inventori Obat</h5>
                            <p>Kelola data obat, stok, harga, dan kategori produk apotek.</p>
                        </div>
                        <div class="menu-card-action">→</div>
                    </router-link>

                    <router-link to="/admin/penerimaan" class="menu-card-detailed">
                        <div class="menu-card-icon recv-icon">📫</div>
                        <div class="menu-card-content">
                            <h5>Penerimaan Barang</h5>
                            <p>Catat stok masuk, faktur pembelian, dan histori dari supplier.</p>
                        </div>
                        <div class="menu-card-action">→</div>
                    </router-link>

                    <router-link to="/admin/suppliers" class="menu-card-detailed">
                        <div class="menu-card-icon supp-icon">🚚</div>
                        <div class="menu-card-content">
                            <h5>Master Supplier</h5>
                            <p>Manajemen data pemasok, alamat, dan kontak distributor obat.</p>
                        </div>
                        <div class="menu-card-action">→</div>
                    </router-link>

                    <router-link to="/admin/laporan" class="menu-card-detailed">
                        <div class="menu-card-icon rep-icon">📊</div>
                        <div class="menu-card-content">
                            <h5>Laporan Keuangan</h5>
                            <p>Pantau arus kas, rekap omzet harian/bulanan, dan penjualan.</p>
                        </div>
                        <div class="menu-card-action">→</div>
                    </router-link>

                    <router-link to="/admin/staf" class="menu-card-detailed">
                        <div class="menu-card-icon staff-icon">👥</div>
                        <div class="menu-card-content">
                            <h5>Kelola Staf</h5>
                            <p>Atur hak akses pengguna, tambah, edit, atau hapus data pegawai.</p>
                        </div>
                        <div class="menu-card-action">→</div>
                    </router-link>
                </div>
            </div>

            <!-- Kolom Kanan (Warning, Top Items, Recent Trx) -->
            <div class="side-content-wrapper">
                
                <!-- Mini Warning Table -->
                <div class="card warning-card">
                    <div class="card-header highlight-header-yellow">
                        <h4>⚠️ Perlu Perhatian (Stok & Exp)</h4>
                    </div>
                    <div class="warning-list" v-if="peringatanItems.length > 0">
                        <div class="warning-item" v-for="(item, index) in peringatanItems" :key="'warn-'+index">
                            <div class="warning-icon" :class="item.colorClass">{{ item.icon }}</div>
                            <div class="warning-details">
                                <span class="warning-title">{{ item.title }}</span>
                                <span class="warning-desc" :class="'text-' + item.colorClass">{{ item.desc }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state-small">
                        <p>Tidak ada stok obat yang menipis atau mau expired hari ini. 👍</p>
                    </div>
                </div>

                <!-- Top 5 Fast Moving Items -->
                <div class="card top-items-card">
                    <div class="card-header highlight-header-blue">
                        <h4>🔥 Top 5 Obat Terlaris (Bulan Ini)</h4>
                    </div>
                    <div class="top-items-list" v-if="topItems.length > 0">
                        <div class="top-item" v-for="(item, index) in topItems" :key="'top-'+index">
                            <div class="rank-badge" :class="getRankClass(index)">{{ index + 1 }}</div>
                            <div class="top-item-details">
                                <span class="item-name">{{ item.obat ? item.obat.nama_obat : 'Unknown' }}</span>
                                <span class="item-sales">Terjual: {{ item.total_qty }} QTY</span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state-small">
                        <p>Belum ada data transaksi bulan ini.</p>
                    </div>
                </div>

                <!-- Transaksi Terakhir -->
                <div class="card list-card">
                    <div class="card-header">
                        <h4>🛒 Transaksi Terakhir</h4>
                    </div>
                    <div class="transaction-list" v-if="recentTransactions.length > 0">
                        <div class="trx-item" v-for="trx in recentTransactions" :key="trx.id">
                            <div class="dot" :class="getTrxDotClass(trx)"></div>
                            <div class="trx-details">
                                <span class="trx-id">{{ trx.no_transaksi }}</span>
                                <span class="trx-desc" :title="trx.deskripsi_obat">{{ trx.deskripsi_obat }}</span>
                            </div>
                            <div class="trx-amount">
                                {{ formatRupiah(trx.total_harga) }}
                                <small :class="getTrxTextClass(trx)">{{ getTrxStatusText(trx) }}</small>
                            </div>
                        </div>
                    </div>
                    <div v-else class="empty-state-small">
                        <p>Belum ada transaksi sama sekali.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

// States for Dashboard Data
const omzetHariIni = ref(0);
const stokMenipisCount = ref(0);
const hampirKadaluarsaCount = ref(0);
const totalProduk = ref(0);

const peringatanItems = ref([]); // Combined stok_menipis + hampir_kadaluarsa
const topItems = ref([]);
const recentTransactions = ref([]);

const isLoading = ref(true);

const fetchDashboardData = async () => {
    try {
        isLoading.value = true;
        
        // Asumsi base URL untuk API diseting di interceptor atau menggunakan VITE_API_BASE_URL
        const response = await axios.get('http://localhost:8000/api/dashboard', {
            headers: {
                Authorization: `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        if (response.data.success) {
            const data = response.data.data;
            
            // Metrics
            omzetHariIni.value = data.omzet_hari_ini || 0;
            stokMenipisCount.value = data.stok_menipis_count || 0;
            hampirKadaluarsaCount.value = data.hampir_kadaluarsa_count || 0;
            totalProduk.value = data.total_produk || 0;

            // Warning Items (gabung array stok menipis dan hampir kadaluarsa)
            const warningStok = (data.stok_menipis_items || []).map(item => ({
                type: 'stok',
                title: item.nama_obat,
                desc: `Sisa ${item.stok} (Pesan sekarang)`,
                icon: '📦',
                colorClass: 'orange' 
            }));

            const warningExp = (data.hampir_kadaluarsa_items || []).map(item => {
                // Parse date untuk display yang baagus
                const expDate = new Date(item.tgl_kadaluarsa).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                return {
                    type: 'exp',
                    title: item.nama_obat,
                    desc: `Exp: ${expDate}`,
                    icon: '⏰',
                    colorClass: 'red'
                };
            });

            // Ambil maksimal 5 warning untuk tabel mini
            peringatanItems.value = [...warningStok, ...warningExp].slice(0, 5);

            // Top Items
            topItems.value = data.top_items || [];

            // Recent Transactions
            recentTransactions.value = data.recent_transactions || [];
        }

    } catch (error) {
        console.error('Error fetching dashboard data:', error);
    } finally {
        isLoading.value = false;
    }
};

// Helpers
const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};

const getRankClass = (index) => {
    if (index === 0) return 'rank-1';
    if (index === 1) return 'rank-2';
    if (index === 2) return 'rank-3';
    return '';
};

// Coba dapatkan class warna untuk trx secara sederhana, asumsikan trx selesai berwarna hijau
const getTrxDotClass = (trx) => {
    // Pada contoh ini semua selesai / lunas asumsikan hijau
    // Jika ada field status, bisa disesuaikan
    return 'green';
};

const getTrxStatusText = (trx) => {
    return `Selesai (${trx.waktu})`;
};

const getTrxTextClass = (trx) => {
    return 'text-green';
};

onMounted(() => {
    fetchDashboardData();
});
</script>

<style scoped>
.intro-text p { color: var(--text-muted); margin-bottom: 25px; font-size: 14px; }

/* GRID KARTU ATAS */
.stats-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;
}

.stat-card {
    background: var(--bg-card); padding: 24px; border-radius: 16px; display: flex; align-items: center; gap: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid var(--border-color);
}

.icon-box {
    width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.icon-box.green { background: var(--success-bg); color: var(--success); }
.icon-box.orange { background: var(--warning-bg); color: var(--warning); }
.icon-box.red { background: var(--danger-bg); color: var(--danger); }
.icon-box.blue { background: var(--info-bg); color: var(--info); }

.stat-info { display: flex; flex-direction: column; }
.stat-info .label { font-size: 10px; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px; margin-bottom: 6px; text-transform: uppercase; }
.stat-info .value { font-size: 20px; color: var(--text-main); font-weight: 800; margin: 0; }

/* GRID KONTEN BAWAH */
.content-grid {
    display: grid; grid-template-columns: 2fr 1.2fr; gap: 24px;
}
.side-content-wrapper { display: flex; flex-direction: column; gap: 24px; }

.card { background: var(--bg-card); border-radius: 16px; border: 1px solid var(--border-color); overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.card-header { padding: 16px 20px; border-bottom: 1px solid var(--border-color); }
.card-header h4 { margin: 0; font-size: 15px; color: var(--text-main); font-weight: 700; }
.highlight-header-yellow { background: var(--warning-bg); border-bottom: 1px solid var(--border-color); }
.highlight-header-yellow h4 { color: var(--warning); }
.highlight-header-blue { background: var(--info-bg); border-bottom: 1px solid var(--border-color); }
.highlight-header-blue h4 { color: var(--info); }

/* Dashboard Ringkasan / Menu Detailed */
.menu-grid-detailed {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; padding: 24px; background: var(--bg-hover);
}
.menu-card-detailed {
    display: flex; align-items: center; gap: 16px; padding: 20px;
    background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 12px; text-decoration: none;
    transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.menu-card-detailed:hover {
    transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border-color: var(--primary);
}
.menu-card-icon {
    width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0;
}
.pos-icon { background: var(--success-bg); color: var(--success); }
.inv-icon { background: var(--info-bg); color: var(--info); }
.recv-icon { background: var(--danger-bg); color: var(--danger); }
.supp-icon { background: var(--warning-bg); color: var(--warning); }
.rep-icon { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; } /* custom purple */
.staff-icon { background: rgba(34, 197, 94, 0.1); color: #22c55e; } /* custom green */

.menu-card-content { flex: 1; }
.menu-card-content h5 { margin: 0 0 4px 0; font-size: 15px; font-weight: 700; color: var(--text-main); }
.menu-card-content p { margin: 0; font-size: 12px; color: var(--text-muted); line-height: 1.5; }

.menu-card-action {
    color: var(--text-muted); font-weight: bold; font-size: 18px; transition: color 0.3s;
}
.menu-card-detailed:hover .menu-card-action { color: var(--primary); }

/* List Warning Mini Table */
.warning-list, .top-items-list, .transaction-list { padding: 0; }
.warning-item {
    display: flex; align-items: flex-start; gap: 12px; padding: 14px 20px; border-bottom: 1px solid var(--border-color);
}
.warning-item:last-child { border-bottom: none; }
.warning-icon { font-size: 18px; margin-top: 2px; }
.warning-details { display: flex; flex-direction: column; gap: 4px; }
.warning-title { font-size: 13px; font-weight: 700; color: var(--text-main); }
.warning-desc { font-size: 12px; font-weight: 600; }
.text-orange { color: var(--warning); }
.text-red { color: var(--danger); }
.text-green { color: var(--success); font-weight: 600 !important; }

/* Top 5 Items */
.top-item {
    display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid var(--border-color);
}
.top-item:last-child { border-bottom: none; }
.rank-badge {
    width: 28px; height: 28px; border-radius: 8px; background: var(--bg-hover); color: var(--text-muted);
    display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px;
}
.rank-1 { background: var(--warning-bg); color: var(--warning); }
.rank-2 { background: var(--border-color); color: var(--text-main); }
.rank-3 { background: rgba(249, 115, 22, 0.2); color: var(--warning); }
.top-item-details { display: flex; flex-direction: column; }
.item-name { font-size: 13px; font-weight: 700; color: var(--text-main); }
.item-sales { font-size: 11px; color: var(--text-muted); margin-top: 2px; }

/* List Transaksi */
.trx-item {
    display: flex; align-items: center; gap: 15px; padding: 14px 20px; border-bottom: 1px solid var(--border-color);
}
.trx-item:last-child { border-bottom: none; }
.trx-details { flex: 1; display: flex; flex-direction: column; }
.trx-id { font-size: 13px; font-weight: 700; color: var(--text-main); }
.trx-desc { font-size: 12px; color: var(--text-muted); margin-top: 2px; }

.trx-amount { text-align: right; font-weight: 700; color: var(--text-main); font-size: 13px; }
.trx-amount small { display: block; font-weight: 400; color: var(--text-muted); font-size: 11px; margin-top: 2px; }

.dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dot.green { background: var(--success); }
.dot.orange { background: var(--warning); }
</style>