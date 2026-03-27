<script setup>
import { useRouter } from 'vue-router';
import logoImg from '../assets/images/logo.png';

const router = useRouter();

const navigateToLogin = () => {
    router.push('/login');
};
</script>

<template>
    <div class="landing-container">
        <nav class="navbar">
            <div class="logo-container">
                <img :src="logoImg" alt="RR Farma Logo" class="logo-image" />
                <span class="brand-name">RR FARMA</span>
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
                <span class="badge-new">✨ Melayani Dengan Sepenuh Hati</span>
                <h1>Apotek & Pemasok <br><span class="highlight">Alat Kesehatan Terpercaya</span></h1>
                <p>
                    RR Farma hadir untuk memenuhi kebutuhan obat-obatan, vitamin, dan perlengkapan medis terlengkap dengan kualitas terjamin. Konsultasi apoteker profesional tersedia.
                </p>
                <div class="hero-buttons">
                    <button class="btn-primary">Hubungi Kami 📞</button>
                    <button class="btn-secondary">Lokasi Apotek</button>
                </div>
                
                <div class="stats-row">
                    <div class="stat">
                        <b>100%</b> <span>Obat Asli & Resmi</span>
                    </div>
                    <div class="divider"></div>
                    <div class="stat">
                        <b>24/7</b> <span>Layanan Informasi</span>
                    </div>
                </div>
            </div>

            <div class="hero-image">
                <img :src="logoImg" alt="RR Farma Logo" style="width: 100%; max-width: 400px; animation: float 3s ease-in-out infinite;" />
            </div>
        </header>

        <section id="fitur" class="features-section">
            <div class="section-title">
                <h2>Layanan Kami</h2>
                <p>Kami berkomitmen menyediakan layanan kesehatan terbaik untuk Anda dan keluarga.</p>
            </div>

            <div class="feature-grid">
                <div class="feature-card">
                    <div class="icon-bg green">💊</div>
                    <h3>Tebus Resep Dokter</h3>
                    <p>Layanan penebusan resep dengan obat lengkap, original, dan ditangani apoteker handal.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg blue">🩺</div>
                    <h3>Alat Kesehatan</h3>
                    <p>Tersedia berbagai alat medis untuk kebutuhan perorangan, klinik, hingga rumah sakit.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg orange">💬</div>
                    <h3>Konsultasi Apoteker</h3>
                    <p>Dapatkan informasi akurat seputar penggunaan obat-obatan dari tenaga profesional kami.</p>
                </div>
                <div class="feature-card">
                    <div class="icon-bg purple">🚚</div>
                    <h3>Layanan Pesan Antar</h3>
                    <p>Pesan obat dan alat kesehatan dari rumah, kami siap antar sampai tujuan dengan aman.</p>
                </div>
            </div>
        </section>

        <footer class="footer">
            <div class="footer-content">
                <div class="brand">
                    <h3>RR FARMA</h3>
                    <p>Apotek & Alat Kesehatan.</p>
                </div>
                <div class="copyright">
                    &copy; 2026 RR FARMA. All rights reserved.
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
.logo-image { height: 48px; object-fit: contain; }
.brand-name { font-weight: 800; font-size: 22px; color: var(--primary); letter-spacing: -0.5px; }

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