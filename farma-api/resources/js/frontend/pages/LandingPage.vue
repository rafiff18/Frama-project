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
    color: #0f172a;
    background: #f8fafc;
    overflow-x: hidden;
}

/* --- NAVBAR --- */
.navbar {
    display: flex; justify-content: space-between; align-items: center;
    padding: 20px 5%; background: rgba(255,255,255,0.9); backdrop-filter: blur(10px);
    position: sticky; top: 0; z-index: 100; border-bottom: 1px solid #f1f5f9;
}

.logo-container { display: flex; align-items: center; gap: 10px; }
.logo-icon { font-size: 24px; }
.brand-name { font-weight: 800; font-size: 18px; color: #10b981; letter-spacing: -0.5px; }

.nav-links { display: flex; gap: 30px; }
.nav-links a { text-decoration: none; color: #64748b; font-weight: 500; font-size: 14px; transition: 0.3s; }
.nav-links a:hover { color: #10b981; }

.btn-login {
    background: #0f172a; color: white; border: none; padding: 10px 24px;
    border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s;
}
.btn-login:hover { background: #334155; }

/* --- HERO SECTION --- */
.hero-section {
    display: flex; align-items: center; justify-content: space-between;
    padding: 80px 5%; gap: 50px; min-height: 80vh;
    background: radial-gradient(circle at top right, #ecfdf5 0%, transparent 40%);
}

.hero-content { max-width: 600px; }
.badge-new { 
    background: #ecfdf5; color: #10b981; padding: 6px 12px; border-radius: 20px; 
    font-size: 12px; font-weight: 700; margin-bottom: 20px; display: inline-block;
}

h1 { font-size: 48px; line-height: 1.2; font-weight: 800; margin-bottom: 20px; color: #0f172a; }
.highlight { color: #10b981; }
p { font-size: 16px; line-height: 1.6; color: #64748b; margin-bottom: 30px; }

.hero-buttons { display: flex; gap: 15px; margin-bottom: 40px; }
.btn-primary {
    background: #10b981; color: white; padding: 14px 32px; border-radius: 10px;
    font-weight: 700; border: none; cursor: pointer; font-size: 16px;
    box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4); transition: 0.3s;
}
.btn-primary:hover { transform: translateY(-3px); background: #059669; }

.btn-secondary {
    background: white; color: #0f172a; padding: 14px 32px; border-radius: 10px;
    font-weight: 700; border: 1px solid #e2e8f0; cursor: pointer; font-size: 16px;
}
.btn-secondary:hover { background: #f8fafc; }

.stats-row { display: flex; align-items: center; gap: 20px; }
.stat b { display: block; font-size: 18px; color: #0f172a; }
.stat span { font-size: 12px; color: #64748b; }
.divider { width: 1px; height: 30px; background: #cbd5e1; }

/* --- HERO IMAGE (MOCKUP) --- */
.hero-image { flex: 1; display: flex; justify-content: center; position: relative; }
.mockup-window {
    width: 100%; max-width: 500px; height: 320px; background: white;
    border-radius: 16px; box-shadow: 0 20px 50px -10px rgba(0,0,0,0.1);
    border: 1px solid #f1f5f9; overflow: hidden; position: relative;
    transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
    transition: 0.5s;
}
.mockup-window:hover { transform: perspective(1000px) rotateY(0) rotateX(0); }

.window-header { padding: 12px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
.dots { display: flex; gap: 6px; }
.dot { width: 10px; height: 10px; border-radius: 50%; }
.red { background: #ef4444; } .yellow { background: #f59e0b; } .green { background: #22c55e; }

.window-body { padding: 20px; display: flex; flex-direction: column; gap: 15px; }
.skeleton-header { height: 40px; width: 60%; background: #f1f5f9; border-radius: 8px; }
.skeleton-grid { display: flex; gap: 10px; }
.box { height: 80px; flex: 1; background: #f8fafc; border-radius: 8px; }
.skeleton-chart { height: 100px; width: 100%; background: #f1f5f9; border-radius: 8px; margin-top: auto; }

.floating-badge {
    position: absolute; bottom: 40px; right: -20px; background: white;
    padding: 10px 20px; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    font-weight: 700; color: #10b981; border: 1px solid #ecfdf5; animation: float 3s ease-in-out infinite;
}

/* --- FEATURES --- */
.features-section { padding: 80px 5%; background: white; }
.section-title { text-align: center; max-width: 600px; margin: 0 auto 50px; }
.section-title h2 { font-size: 32px; font-weight: 800; margin-bottom: 10px; }

.feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
.feature-card {
    padding: 30px; border-radius: 20px; background: #f8fafc; border: 1px solid transparent;
    transition: 0.3s;
}
.feature-card:hover { background: white; border-color: #10b981; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }

.icon-bg {
    width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
    font-size: 24px; margin-bottom: 20px;
}
.green { background: #ecfdf5; } .blue { background: #eff6ff; }
.orange { background: #fff7ed; } .purple { background: #f3e8ff; }

.feature-card h3 { font-size: 18px; font-weight: 700; margin-bottom: 10px; }
.feature-card p { font-size: 14px; color: #64748b; line-height: 1.5; }

/* --- FOOTER --- */
.footer { background: #0f172a; color: white; padding: 40px 5%; margin-top: auto; }
.footer-content { display: flex; justify-content: space-between; align-items: center; }
.brand h3 { margin: 0; color: #10b981; }
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