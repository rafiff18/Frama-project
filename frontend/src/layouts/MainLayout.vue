<template>
  <div class="flex min-h-screen bg-slate-50 dark:bg-slate-900 font-outfit text-slate-800 dark:text-slate-100 transition-colors duration-300">
    
    <!-- Sidebar -->
    <aside class="w-[280px] bg-white dark:bg-slate-900 flex flex-col fixed h-screen z-20 border-r border-slate-200 dark:border-slate-800 transition-all shadow-sm">
      
      <!-- Sidebar Header -->
      <div class="px-6 py-6 flex items-center gap-3 border-b border-slate-100 dark:border-slate-800/50 cursor-pointer" @click="router.push('/admin')">
        <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl border border-emerald-100 dark:border-emerald-800">
          <img :src="logoImg" alt="RR Farma" class="w-8 h-8 object-contain drop-shadow-sm" />
        </div>
        <div class="flex flex-col">
          <span class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white leading-tight">RR FARMA</span>
          <span class="text-[10px] font-bold text-primary uppercase tracking-wider">Apotek & Alkes</span>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 py-6 overflow-y-auto custom-scrollbar space-y-1">
        <div class="px-3 mb-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Menu Utama</div>
        
        <router-link v-if="['superadmin', 'owner', 'apoteker'].includes(userRole)" to="/admin" class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200" :class="{ 'bg-primary text-white shadow-md shadow-primary/20': route.path === '/admin', 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary': route.path !== '/admin' }">
          <span class="text-lg opacity-80 group-hover:scale-110 transition-transform" :class="{ 'opacity-100': route.path === '/admin' }">🏁</span> Dashboard
        </router-link>

        <router-link v-if="['superadmin', 'kasir'].includes(userRole)" to="/admin/kasir" class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200" active-class="bg-primary text-white shadow-md shadow-primary/20" :class="route.path.includes('/kasir') ? '' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary'">
          <span class="text-lg opacity-80 group-hover:scale-110 transition-transform">🛒</span> Kasir (POS)
        </router-link>

        <router-link v-if="['superadmin', 'owner'].includes(userRole)" to="/admin/inventori" class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200" active-class="bg-primary text-white shadow-md shadow-primary/20" :class="route.path.includes('/inventori') ? '' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary'">
          <span class="text-lg opacity-80 group-hover:scale-110 transition-transform">💊</span> Inventori Obat
        </router-link>

        <router-link v-if="['superadmin', 'apoteker'].includes(userRole)" to="/admin/penerimaan" class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200" active-class="bg-primary text-white shadow-md shadow-primary/20" :class="route.path.includes('/penerimaan') ? '' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary'">
          <span class="text-lg opacity-80 group-hover:scale-110 transition-transform">📫</span> Penerimaan (Stok)
        </router-link>

        <router-link v-if="['superadmin', 'apoteker'].includes(userRole)" to="/admin/suppliers" class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200" active-class="bg-primary text-white shadow-md shadow-primary/20" :class="route.path.includes('/suppliers') ? '' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary'">
          <span class="text-lg opacity-80 group-hover:scale-110 transition-transform">🚚</span> Supplier
        </router-link>

        <router-link v-if="['superadmin', 'owner'].includes(userRole)" to="/admin/laporan" class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200" active-class="bg-primary text-white shadow-md shadow-primary/20" :class="route.path.includes('/laporan') ? '' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-primary'">
          <span class="text-lg opacity-80 group-hover:scale-110 transition-transform">📊</span> Laporan Keuangan
        </router-link>

        <div v-if="['superadmin'].includes(userRole)" class="pt-4 pb-2">
            <div class="px-3 mb-2 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">Administrator</div>
            <router-link to="/admin/staf" class="group flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all duration-200" active-class="bg-slate-800 dark:bg-slate-700 text-white shadow-md" :class="route.path.includes('/staf') ? '' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white'">
            <span class="text-lg opacity-80 group-hover:scale-110 transition-transform">👥</span> Kelola Staf
            </router-link>
        </div>
      </nav>

      <!-- Sidebar Footer User Profile -->
      <div class="p-4 border-t border-slate-100 dark:border-slate-800/50 bg-slate-50/50 dark:bg-slate-900/50">
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 shadow-sm border border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-3 mb-3 pb-3 border-b border-slate-100 dark:border-slate-700/50">
                <div class="w-10 h-10 rounded-full bg-primary-light/20 flex items-center justify-center text-xl shadow-inner text-primary-dark">
                    👨‍⚕️
                </div>
                <div class="flex flex-col truncate">
                    <span class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ user.name || 'User' }}</span>
                    <span class="text-xs font-semibold text-primary capitalize">{{ user.role || 'Guest' }}</span>
                </div>
            </div>
            <button @click="logout" class="w-full flex items-center justify-center gap-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 py-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Keluar Sistem
            </button>
        </div>
      </div>
    </aside>

    <!-- Main Workspace -->
    <main class="flex-1 ml-[280px] flex flex-col min-h-screen">
      
      <!-- Top Navbar -->
      <header class="sticky top-0 z-10 glass bg-white/80 dark:bg-slate-900/80 px-8 py-5 border-b border-slate-200/60 dark:border-slate-800/60 flex justify-between items-center shadow-sm">
        <div class="flex flex-col">
          <span class="text-[10px] font-extrabold text-primary tracking-widest uppercase mb-0.5">Sistem Manajemen Apotek</span>
          <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ currentPageTitle }}</h2>
        </div>
        
        <div class="flex items-center gap-4">
            <!-- Theme Toggle -->
            <button @click="toggleTheme" class="relative group p-2.5 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-all focus:outline-none" :title="isDarkMode ? 'Beralih ke Terang' : 'Beralih ke Gelap'">
                <div class="absolute inset-0 rounded-full bg-primary/0 group-hover:bg-primary/10 transition-colors"></div>
                <svg v-if="!isDarkMode" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 relative sm-rotate" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 relative sm-rotate text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.02 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </button>
            
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-light flex items-center justify-center text-white font-bold shadow-md ring-2 ring-white dark:ring-slate-800">
                {{ userRole.charAt(0).toUpperCase() }}
            </div>
        </div>
      </header>
      
      <!-- Page Content -->
      <div class="flex-1 p-8">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view> 
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { supabase } from '../lib/supabase';
import logoImg from '../assets/images/logo.png';

const isDarkMode = ref(false);

const route = useRoute();
const router = useRouter();

const user = JSON.parse(localStorage.getItem('user') || '{}');
const userRole = String(user.role || 'guest');

const currentPageTitle = computed(() => {
    if (route.path === '/admin') return 'Dashboard Utama';
    if (route.path.includes('/admin/inventori')) return 'Inventori Obat';
    if (route.path.includes('/admin/penerimaan')) return 'Penerimaan Stok';
    if (route.path.includes('/admin/suppliers')) return 'Master Supplier';
    if (route.path.includes('/admin/kasir')) return 'Terminal Kasir';
    if (route.path.includes('/admin/laporan')) return 'Laporan Keuangan';
    if (route.path.includes('/admin/staf')) return 'Kelola Karyawan';
    return 'Halaman Sistem';
});

// --- THEME LOGIC ---
onMounted(() => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.body.classList.add('dark');
    }
});

const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.body.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.body.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

const logout = async () => {
    try {
        await supabase.auth.signOut();
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        router.push('/login');
    } catch (e) {
        console.error(e);
        router.push('/login');
    }
};
</script>

<style>
/* Custom Scrollbar for sidebar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, 0.2);
    border-radius: 4px;
}
.custom-scrollbar:hover::-webkit-scrollbar-thumb {
    background: rgba(100, 116, 139, 0.5);
}

/* Page Transition */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

/* Hover rotation class */
.sm-rotate {
    transition: transform 0.3s ease;
}
.group:hover .sm-rotate {
    transform: rotate(15deg) scale(1.1);
}
</style>