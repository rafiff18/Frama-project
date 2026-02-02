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
                    <h4 class="value">Rp 1.250.000</h4>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon-box orange">📦</div>
                <div class="stat-info">
                    <span class="label">STOK MENIPIS</span>
                    <h4 class="value">2</h4>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon-box red">⚠️</div>
                <div class="stat-info">
                    <span class="label">HAMPIR KADALUARSA</span>
                    <h4 class="value">3</h4>
                </div>
            </div>

            <div class="stat-card">
                <div class="icon-box blue">💊</div>
                <div class="stat-info">
                    <span class="label">TOTAL PRODUK</span>
                    <h4 class="value">5</h4>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <div class="card chart-card">
                <div class="card-header">
                    <h4>📈 Tren Penjualan Obat</h4>
                </div>
                <div class="chart-placeholder">
                    <div class="bar" style="height: 30%"></div>
                    <div class="bar" style="height: 50%"></div>
                    <div class="bar" style="height: 40%"></div>
                    <div class="bar" style="height: 80%"></div>
                    <div class="bar" style="height: 60%"></div>
                    <div class="bar" style="height: 90%"></div>
                    <div class="bar" style="height: 70%"></div>
                </div>
            </div>

            <div class="card list-card">
                <div class="card-header">
                    <h4>🛒 Transaksi Terakhir</h4>
                </div>
                <div class="transaction-list">
                    <div class="trx-item">
                        <div class="dot green"></div>
                        <div class="trx-details">
                            <span class="trx-id">TRX-9901</span>
                            <span class="trx-desc">Obat Umum (Paracetamol, Vit C)</span>
                        </div>
                        <div class="trx-amount">
                            Rp 65.000
                            <small>09:15</small>
                        </div>
                    </div>

                    <div class="trx-item">
                        <div class="dot orange"></div>
                        <div class="trx-details">
                            <span class="trx-id">TRX-9902</span>
                            <span class="trx-desc">Resep: Bp. Joko (Amoxicillin)</span>
                        </div>
                        <div class="trx-amount">
                            Rp 120.000
                            <small>10:45</small>
                        </div>
                    </div>

                    <div class="trx-item">
                        <div class="dot green"></div>
                        <div class="trx-details">
                            <span class="trx-id">TRX-9903</span>
                            <span class="trx-desc">Alat Kesehatan (Masker, Betadine)</span>
                        </div>
                        <div class="trx-amount">
                            Rp 45.000
                            <small>11:20</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<style scoped>
.intro-text p { color: #64748b; margin-bottom: 25px; font-size: 14px; }

/* GRID KARTU ATAS */
.stats-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;
}

.stat-card {
    background: white; padding: 24px; border-radius: 16px; display: flex; align-items: center; gap: 16px;
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); border: 1px solid #f1f5f9;
}

.icon-box {
    width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;
}
.icon-box.green { background: #ecfdf5; color: #10b981; }
.icon-box.orange { background: #fff7ed; color: #f97316; }
.icon-box.red { background: #fef2f2; color: #ef4444; }
.icon-box.blue { background: #eff6ff; color: #3b82f6; }

.stat-info { display: flex; flex-direction: column; }
.stat-info .label { font-size: 10px; color: #64748b; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 6px; text-transform: uppercase; }
.stat-info .value { font-size: 20px; color: #0f172a; font-weight: 800; margin: 0; }

/* GRID KONTEN BAWAH */
.content-grid {
    display: grid; grid-template-columns: 2fr 1fr; gap: 24px;
}

.card { background: white; border-radius: 16px; border: 1px solid #f1f5f9; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.card-header { padding: 20px 24px; border-bottom: 1px solid #f1f5f9; }
.card-header h4 { margin: 0; font-size: 16px; color: #0f172a; font-weight: 700; }

/* Chart Hijau */
.chart-placeholder {
    height: 250px; display: flex; align-items: flex-end; justify-content: space-around; padding: 30px; background: #fafafa;
}
.bar { width: 36px; background-color: #34d399; border-radius: 6px 6px 0 0; opacity: 0.9; transition: height 0.5s; }
.bar:hover { opacity: 1; background-color: #10b981; cursor: pointer; }

/* List Transaksi */
.transaction-list { padding: 0; }
.trx-item {
    display: flex; align-items: center; gap: 15px; padding: 18px 24px; border-bottom: 1px solid #f8fafc;
}
.trx-item:last-child { border-bottom: none; }
.trx-details { flex: 1; display: flex; flex-direction: column; }
.trx-id { font-size: 13px; font-weight: 700; color: #0f172a; }
.trx-desc { font-size: 12px; color: #64748b; margin-top: 2px; }

.trx-amount { text-align: right; font-weight: 700; color: #0f172a; font-size: 13px; }
.trx-amount small { display: block; font-weight: 400; color: #94a3b8; font-size: 11px; margin-top: 2px; }

.dot { width: 8px; height: 8px; border-radius: 50%; }
.dot.green { background: #10b981; }
.dot.orange { background: #f97316; }
</style>