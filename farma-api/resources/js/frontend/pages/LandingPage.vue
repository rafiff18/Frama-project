<script setup>
import { useRouter } from 'vue-router';

const router = useRouter();

const navigateToLogin = () => {
    router.push('/login');
};
</script>

<template>
    <div class="landing-container">
        
        <nav class="navbar">
            <div class="logo-container">
                <div class="logo-icon">💊</div>
                <span class="brand-name">MediCare AI</span>
            </div>
            <div class="nav-links">
                <a href="#fitur">Fitur</a>
                <a href="#tentang">Tentang</a>
                <a href="#kontak">Hubungi Kami</a>
            </div>
            <button @click="navigateToLogin" class="btn-login">Masuk Sistem</button>
        </nav>

        <header class="hero-section">
            <div class="hero-content">
                <span class="badge-new">✨ Solusi Apotik Digital 2026</span>
                <h1>Kelola Apotik Jadi <br><span class="highlight">Lebih Mudah & Cerdas</span></h1>
                <p>
                    Sistem manajemen apotik terintegrasi dengan fitur Kasir (POS), 
                    Inventori Otomatis, dan Laporan Keuangan Real-time. 
                    Tinggalkan cara lama, beralih ke digital.
                </p>
                <div class="hero-buttons">
                    <button @click="navigateToLogin" class="btn-primary">Mulai Sekarang 🚀</button>
                    <button class="btn-secondary">Lihat Demo</button>
                </div>
                
                <div class="stats-row">
                    <div class="stat">
                        <b>500+</b> <span>Apotik Bergabung</span>
                    </div>
                    <div class="divider"></div>
                    <div class="stat">
                        <b>1Juta+</b> <span>Transaksi/Bulan</span>
                    </div>
                </div>
            </div>

            <div class="hero-image">
                <div class="mockup-window">
                    <div class="window-header">
                        <div class="dots">
                            <span class="dot red"></span>
                            <span class="dot yellow"></span>
                            <span class="dot green"></span>
                        </div>
                    </div>
                    <div class="window-body">
                        <div class="skeleton-header"></div>
                        <div class="skeleton-grid">
                            <div class="box"></div>
                            <div class="box"></div>
                            <div class="box"></div>
                        </div>
                        <div class="skeleton-chart"></div>
                    </div>
                    <div class="floating-badge">
                        ✅ Stok Aman
                    </div>
                </div>
            </div>
        </header>

        <section id="fitur" class="features-section">
            <div class="section-title">
                <h2>Fitur Unggulan</h2>
                <p>Semua yang Anda butuhkan untuk operasional apotik yang efisien.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-card">
                    <div class="icon-bg green">🛒</div>
                    <h3>Kasir Pintar (POS)</h3>
                    <p>Proses transaksi cepat dengan barcode scanner dan struk otomatis.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg blue">💊</div>
                    <h3>Inventori Obat</h3>
                    <p>Pantau stok obat, tanggal kadaluarsa, dan notifikasi stok menipis.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg orange">📊</div>
                    <h3>Laporan Keuangan</h3>
                    <p>Analisa keuntungan, omzet harian, dan arus kas secara real-time.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg purple">📄</div>
                    <h3>Resep Digital</h3>
                    <p>Kelola penebusan resep dokter dan riwayat pasien dengan rapi.</p>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="footer-content">
                <div class="brand">
                    <h3>MediCare AI</h3>
                    <p>Software Apotik No. #1 di Indonesia.</p>
                </div>
                <div class="copyright">
                    &copy; 2026 MediCare AI. All rights reserved.
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
/* --- GLOBAL --- */
.landing-container {
    font-family: 'Inter', sans-serif;
    color: var(--text-main);
    background: var(--bg-body);
    overflow-x: hidden; transition: background-color 0.3s;
}

/* --- NAVBAR --- */
.navbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 5%; background: var(--bg-card); opacity: 0.98;
    position: sticky; top: 0; z-index: 100; border-bottom: 1px solid var(--border-color);
}

.logo-container { display: flex; align-items: center; gap: 10px; }
.logo-icon { font-size: 24px; }
.brand-name { font-weight: 800; font-size: 18px; color: var(--primary); letter-spacing: -0.5px; }

.nav-links { display: flex; gap: 30px; }
.nav-links a { text-decoration: none; color: var(--text-muted); font-weight: 500; font-size: 14px; transition: 0.3s; }
.nav-links a:hover { color: var(--primary); }

.btn-login {
    background: var(--text-main); color: var(--bg-card); border: none; padding: 10px 24px;
    border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s;
}
.btn-login:hover { opacity: 0.8; }

/* --- HERO SECTION --- */
.hero-section {
    display: flex; align-items: center; justify-content: space-between;
    padding: 80px 5%; gap: 50px; min-height: 80vh;
    background: radial-gradient(circle at top right, var(--success-bg) 0%, transparent 40%);
}

.hero-content { max-width: 600px; }
.badge-new { 
    background: var(--success-bg); color: var(--primary); padding: 6px 12px; border-radius: 20px; 
    font-size: 12px; font-weight: 700; margin-bottom: 20px; display: inline-block;
}

h1 { font-size: 48px; line-height: 1.2; font-weight: 800; margin-bottom: 20px; color: var(--text-main); }
.highlight { color: var(--primary); }
p { font-size: 16px; line-height: 1.6; color: var(--text-muted); margin-bottom: 30px; }

.hero-buttons { display: flex; gap: 15px; margin-bottom: 40px; }
.btn-primary {
    background: var(--primary); color: white; padding: 14px 32px; border-radius: 10px;
    font-weight: 700; border: none; cursor: pointer; font-size: 16px;
    box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4); transition: 0.3s;
}
.btn-primary:hover { transform: translateY(-3px); background: var(--primary-dark); }

.btn-secondary {
    background: var(--bg-card); color: var(--text-main); padding: 14px 32px; border-radius: 10px;
    font-weight: 700; border: 1px solid var(--border-color); cursor: pointer; font-size: 16px; transition: 0.3s;
}
.btn-secondary:hover { background: var(--bg-hover); }

.stats-row { display: flex; align-items: center; gap: 20px; }
.stat b { display: block; font-size: 18px; color: var(--text-main); }
.stat span { font-size: 12px; color: var(--text-muted); }
.divider { width: 1px; height: 30px; background: var(--border-color); }

/* --- HERO IMAGE (MOCKUP) --- */
.hero-image { flex: 1; display: flex; justify-content: center; position: relative; }
.mockup-window {
    width: 100%; max-width: 500px; height: 320px; background: var(--bg-card);
    border-radius: 16px; box-shadow: 0 20px 50px -10px rgba(0,0,0,0.1);
    border: 1px solid var(--border-color); overflow: hidden; position: relative;
    transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
    transition: 0.5s;
}
.mockup-window:hover { transform: perspective(1000px) rotateY(0) rotateX(0); }

.window-header { padding: 12px; background: var(--bg-hover); border-bottom: 1px solid var(--border-color); }
.dots { display: flex; gap: 6px; }
.dot { width: 10px; height: 10px; border-radius: 50%; }
.red { background: var(--danger); } .yellow { background: var(--warning); } .green { background: var(--success); }

.window-body { padding: 20px; display: flex; flex-direction: column; gap: 15px; }
.skeleton-header { height: 40px; width: 60%; background: var(--bg-body); border-radius: 8px; }
.skeleton-grid { display: flex; gap: 10px; }
.box { height: 80px; flex: 1; background: var(--bg-hover); border-radius: 8px; }
.skeleton-chart { height: 100px; width: 100%; background: var(--bg-body); border-radius: 8px; margin-top: auto; }

.floating-badge {
    position: absolute; bottom: 40px; right: -20px; background: var(--bg-card);
    padding: 10px 20px; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    font-weight: 700; color: var(--primary); border: 1px solid var(--success-bg); animation: float 3s ease-in-out infinite;
}

/* --- FEATURES --- */
.features-section { padding: 80px 5%; background: var(--bg-card); }
.section-title { text-align: center; max-width: 600px; margin: 0 auto 50px; }
.section-title h2 { font-size: 32px; font-weight: 800; margin-bottom: 10px; color: var(--text-main); }
.section-title p { color: var(--text-muted); }

.feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
.feature-card {
    padding: 30px; border-radius: 20px; background: var(--bg-hover); border: 1px solid transparent;
    transition: 0.3s;
}
.feature-card:hover { background: var(--bg-card); border-color: var(--primary); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }

.icon-bg {
    width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
    font-size: 24px; margin-bottom: 20px;
}
.green { background: var(--success-bg); } .blue { background: var(--info-bg); }
.orange { background: var(--warning-bg); } .purple { background: rgba(147, 51, 234, 0.1); }

.feature-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--text-main); }
.feature-card p { font-size: 14px; color: var(--text-muted); line-height: 1.5; }

/* --- FOOTER --- */
.footer { background: #0b1121; color: white; padding: 40px 5%; margin-top: auto; }
.footer-content { display: flex; justify-content: space-between; align-items: center; }
.brand h3 { margin: 0; color: var(--primary); }
.brand p { margin: 5px 0 0; font-size: 12px; color: #94a3b8; }
.copyright { font-size: 12px; color: #64748b; }

@keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

/* Responsiveness */
@media (max-width: 768px) {
    .hero-section { flex-direction: column; text-align: center; padding-top: 40px; }
    .hero-content { margin-bottom: 40px; }
    .hero-buttons { justify-content: center; }
    .stats-row { justify-content: center; }
    .nav-links { display: none; }
}
</style>