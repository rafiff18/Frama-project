<script setup>
import { useRouter } from 'vue-router';
import logoImg from '../assets/images/logo.png';

const router = useRouter();

const navigateToLogin = () => {
    router.push('/login');
};
</script>

<template>
    <div class="min-h-screen flex flex-col font-outfit text-slate-800 dark:text-slate-100 bg-slate-50 dark:bg-slate-900 selection:bg-primary selection:text-white">
        
        <!-- Navbar (Glassmorphism) -->
        <nav class="fixed top-0 w-full z-50 glass border-b border-white/20 dark:border-slate-700/50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3 cursor-pointer" @click="window.scrollTo(0,0)">
                    <img :src="logoImg" alt="RR Farma Logo" class="h-10 w-auto object-contain drop-shadow-sm" />
                    <span class="font-bold text-2xl tracking-tight text-primary dark:text-green-400">RR FARMA</span>
                </div>
                
                <div class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">Layanan</a>
                    <a href="#tentang" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">Tentang Kami</a>
                    <a href="#kontak" class="text-sm font-semibold text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">Kontak</a>
                </div>

                <button @click="navigateToLogin" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-full font-semibold text-sm transition-all transform hover:-translate-y-0.5 shadow-lg shadow-primary/30 flex items-center gap-2">
                    Masuk Sistem
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L8.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Hero Section -->
        <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
            <!-- Background Decorative Elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-[500px] h-[500px] bg-primary-light/20 dark:bg-primary/10 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
            <div class="absolute bottom-10 left-10 w-[300px] h-[300px] bg-emerald-400/20 dark:bg-emerald-900/40 rounded-full blur-3xl opacity-60 pointer-events-none"></div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center relative z-10">
                
                <!-- Hero Content -->
                <div class="order-2 lg:order-1 flex flex-col items-start text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-100 dark:bg-green-900/30 text-primary-dark dark:text-green-400 font-semibold text-xs uppercase tracking-wider mb-6 border border-green-200 dark:border-green-800">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        Apotek Modern & Terpercaya
                    </div>
                    
                    <h1 class="text-5xl lg:text-6xl font-extrabold tracking-tight mb-6 leading-tight text-slate-900 dark:text-white">
                        Kesehatan Keluarga,<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-primary-light">Prioritas Utama Kami</span>
                    </h1>
                    
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-xl leading-relaxed">
                        RR Farma mensuplai obat-obatan resmi, vitamin, dan alat kesehatan rumah sakit dengan standar kualitas tinggi serta konsultasi apoteker profesional 24/7.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 w-full sm:w-auto">
                        <button class="bg-primary hover:bg-primary-dark text-white px-8 py-4 rounded-xl font-bold text-lg transition-all shadow-xl shadow-primary/25 hover:-translate-y-1 flex justify-center items-center gap-2">
                            Mulai Konsultasi
                        </button>
                        <button class="bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-white border border-slate-200 dark:border-slate-700 px-8 py-4 rounded-xl font-bold text-lg transition-all flex justify-center items-center gap-2">
                            Lihat Katalog Produk
                        </button>
                    </div>

                    <div class="mt-12 pt-8 border-t border-slate-200 dark:border-slate-700/50 flex flex-wrap gap-8 justify-center lg:justify-start w-full">
                        <div>
                            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">100%</p>
                            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-1">Obat Asli & Resmi</p>
                        </div>
                        <div class="w-px h-12 bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>
                        <div>
                            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">24/7</p>
                            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-1">Layanan Apoteker</p>
                        </div>
                        <div class="w-px h-12 bg-slate-200 dark:bg-slate-700 hidden sm:block"></div>
                         <div>
                            <p class="text-3xl font-extrabold text-slate-900 dark:text-white">10k+</p>
                            <p class="text-sm font-semibold text-slate-500 uppercase tracking-wider mt-1">Pelanggan Puas</p>
                        </div>
                    </div>
                </div>

                <!-- Hero Image -->
                <div class="order-1 lg:order-2 flex justify-center items-center relative">
                    <!-- Soft glowing backdrop behind logo -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-green-300 to-emerald-500 opacity-20 blur-3xl rounded-full scale-75"></div>
                    <img :src="logoImg" alt="RR Farma Logo Big" class="w-full max-w-[320px] lg:max-w-[420px] object-contain relative z-10 animate-float drop-shadow-2xl" />
                </div>

            </div>
        </header>

        <!-- Features Section -->
        <section id="fitur" class="py-24 bg-white dark:bg-slate-800 relative z-10">
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-sm font-bold text-primary uppercase tracking-widest mb-2">Layanan Unggulan</h2>
                    <h3 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white mb-4">Solusi Kesehatan Terintegrasi</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-lg">Kami hadir dengan layanan lengkap untuk memastikan kesehatan Anda dan keluarga selalu terjaga dengan baik.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    
                    <!-- Feature 1 -->
                    <div class="group bg-slate-50 dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-700 hover:border-primary/50 dark:hover:border-primary/50 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/10 hover:-translate-y-2">
                        <div class="w-14 h-14 rounded-2xl bg-green-100 dark:bg-green-900/50 flex items-center justify-center text-2xl mb-6 text-primary group-hover:scale-110 transition-transform">
                            💊
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Tebus Resep Dokter</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Penebusan resep cepat dengan obat komplit, original, serta pengecekan ketat oleh apoteker.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group bg-slate-50 dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-700 hover:border-blue-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-blue-500/10 hover:-translate-y-2">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-2xl mb-6 text-blue-600 group-hover:scale-110 transition-transform">
                            🩺
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Alat Kesehatan</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Beragam alkes modern untuk perawatan mandiri, klinik, maupun peralatan khusus rumah sakit.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group bg-slate-50 dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-700 hover:border-amber-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-amber-500/10 hover:-translate-y-2">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-2xl mb-6 text-amber-600 group-hover:scale-110 transition-transform">
                            💬
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Konsultasi Gratis</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Layanan tanya jawab langsung dengan Apoteker kami seputar dosis, indikasi, dan efek samping obat.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="group bg-slate-50 dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-700 hover:border-purple-500/50 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/10 hover:-translate-y-2">
                        <div class="w-14 h-14 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-2xl mb-6 text-purple-600 group-hover:scale-110 transition-transform">
                            🚚
                        </div>
                        <h4 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Pesan Antar</h4>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Cukup #DiRumahAja, kami antar obat dan kebutuhan medis lainnya langsung ke depan pintu Anda.</p>
                    </div>

                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 relative overflow-hidden bg-slate-900">
            <div class="absolute inset-0 bg-primary/20"></div>
            <div class="max-w-4xl mx-auto px-6 relative z-10 text-center">
                <h2 class="text-3xl md:text-5xl font-extrabold text-white mb-6">Siap Melayani Kebutuhan Medis Anda?</h2>
                <p class="text-lg text-slate-300 mb-10 max-w-2xl mx-auto">Masuk ke dalam sistem manajemen kami untuk karyawan, atau hubungi pusat bantuan kami segera.</p>
                <div class="flex justify-center gap-4">
                    <button @click="navigateToLogin" class="bg-white text-slate-900 hover:bg-slate-100 px-8 py-4 rounded-xl font-bold text-lg transition-transform hover:scale-105">
                        Portal Karyawan
                    </button>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-950 py-12 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-3">
                    <img :src="logoImg" alt="RR Farma" class="h-8 opacity-80" />
                    <span class="text-xl font-bold text-white tracking-tight">RR FARMA</span>
                </div>
                <div class="text-slate-500 text-sm font-medium">
                    &copy; 2026 RR FARMA. All rights reserved.
                </div>
            </div>
        </footer>

    </div>
</template>

<style>
/* Custom Float Animation */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}
.animate-float {
    animation: float 6s ease-in-out infinite;
}
</style>