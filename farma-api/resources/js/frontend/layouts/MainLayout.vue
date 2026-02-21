<template>
  <div class="layout-wrapper">
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo-icon">💊</div>
        <div class="brand-text">
            <span class="brand-title">MediCare AI</span>
            <small class="brand-subtitle">Apotik Management</small>
        </div>
      </div>

      <nav class="menu">
        <div class="menu-category">MENU UTAMA</div>
        
        <router-link to="/admin" class="menu-item" :class="{ 'active': route.path === '/admin' }">
          <span class="icon">🏁</span> Dashboard
        </router-link>

        <router-link v-if="['superadmin', 'kasir'].includes(userRole)" to="/admin/kasir" class="menu-item" active-class="active">
          <span class="icon">🛒</span> Kasir (POS)
        </router-link>

        <router-link v-if="['superadmin', 'owner'].includes(userRole)" to="/admin/inventori" class="menu-item" active-class="active">
          <span class="icon">💊</span> Inventori Obat
        </router-link>

        <router-link v-if="['superadmin', 'apoteker'].includes(userRole)" to="/admin/penerimaan" class="menu-item" active-class="active">
          <span class="icon">📫</span> Penerimaan (Stok)
        </router-link>

        <router-link v-if="['superadmin', 'apoteker'].includes(userRole)" to="/admin/suppliers" class="menu-item" active-class="active">
          <span class="icon">🚚</span> Supplier
        </router-link>

        <router-link v-if="['superadmin', 'owner'].includes(userRole)" to="/admin/laporan" class="menu-item" active-class="active">
          <span class="icon">📊</span> Laporan Keuangan
        </router-link>

        <router-link v-if="['superadmin'].includes(userRole)" to="/admin/staf" class="menu-item" active-class="active">
          <span class="icon">👥</span> Kelola Staf
        </router-link>
      </nav>

      <div class="sidebar-footer">
        <div class="role-selector">
          <small>LOGIN SEBAGAI</small>
          <div class="user-profile">
            <div class="avatar">👩‍⚕️</div>
            <div class="user-details">
                <span class="name">{{ user.name || 'User' }}</span>
                <span class="role">{{ user.role || 'Guest' }}</span>
            </div>
          </div>
        </div>
        <a href="#" @click.prevent="logout" class="logout-link">
          <span class="icon">🚪</span> Keluar
        </a>
      </div>
    </aside>

    <main class="main-content">
      <header class="top-navbar">
        <div class="breadcrumb">
          <span class="sub-title">APOTIK MANAGEMENT SYSTEM</span>
          <h2 class="page-title">{{ currentPageTitle }}</h2>
        </div>
        <div class="header-actions">
            <!-- Theme Toggle -->
            <button @click="toggleTheme" class="btn-theme-toggle" :title="isDarkMode ? 'Beralih ke Terang' : 'Beralih ke Gelap'">
                {{ isDarkMode ? '🌙' : '☀️' }}
            </button>
            <div class="user-info">
                <div class="avatar-small">{{ userRole.charAt(0).toUpperCase() }}</div>
            </div>
        </div>
      </header>
      
      <div class="content-body">
        <router-view></router-view> 
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const isDarkMode = ref(false);

const route = useRoute();
const router = useRouter();

const user = JSON.parse(localStorage.getItem('user') || '{}');
const userRole = String(user.role || 'guest');

const currentPageTitle = computed(() => {
    if (route.path === '/admin') return 'Dashboard';
    if (route.path.includes('/admin/inventori')) return 'Inventori Obat';
    if (route.path.includes('/admin/penerimaan')) return 'Penerimaan Barang';
    if (route.path.includes('/admin/suppliers')) return 'Master Supplier';
    if (route.path.includes('/admin/kasir')) return 'Kasir Apotik';
    if (route.path.includes('/admin/staf')) return 'Kelola Staf';
    return 'Halaman';
});

// --- THEME LOGIC ---
onMounted(() => {
    // Check local storage preference
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
        await axios.post('http://127.0.0.1:8000/api/logout', {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        router.push('/login');
    } catch (e) {
        console.error(e);
        router.push('/login');
    }
};
</script>

<style scoped>
:root {
    --primary: #10b981;
    --primary-dark: #059669;
    --bg-light: #ecfdf5;
    --sidebar-bg: #0f172a;
}

.layout-wrapper {
  display: flex; min-height: 100vh; font-family: 'Inter', sans-serif;
  /* background-color handled by global var(--bg-body) */
}

.sidebar {
  width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text);
  display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 10;
  border-right: 1px solid var(--border-color);
}

.sidebar-header {
  padding: 24px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--border-color);
}

.logo-icon {
  background: var(--primary); color: white; width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 10px; font-size: 22px;
}

.brand-text { display: flex; flex-direction: column; }
.brand-title { color: white; font-weight: 700; font-size: 16px; }
.brand-subtitle { font-size: 10px; color: #10b981; font-weight: 600; letter-spacing: 0.5px; }

.menu { padding: 20px 16px; flex: 1; }
.menu-category { font-size: 10px; font-weight: 700; color: var(--text-muted); margin-bottom: 12px; letter-spacing: 1px; }

.menu-item {
  display: flex; align-items: center; gap: 12px; padding: 12px 16px;
  color: var(--sidebar-text); text-decoration: none; border-radius: 8px; margin-bottom: 4px;
  transition: all 0.3s; font-weight: 500; font-size: 14px;
}

.menu-item:hover { background-color: var(--sidebar-hover); color: white; }

.menu-item.active {
  background-color: var(--primary); color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.sidebar-footer { padding: 20px; border-top: 1px solid var(--border-color); background-color: transparent; }
.role-selector small { display: block; font-size: 10px; margin-bottom: 8px; color: var(--text-muted); }

.user-profile { display: flex; align-items: center; gap: 10px; }
.avatar { width: 32px; height: 32px; background: var(--bg-hover); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.user-details { display: flex; flex-direction: column; }
.name { color: var(--text-main); font-size: 13px; font-weight: 600; }
.role { color: var(--primary); font-size: 11px; }

.logout-link { display: flex; align-items: center; gap: 10px; margin-top: 15px; color: var(--text-muted); text-decoration: none; font-size: 13px; }
.logout-link:hover { color: var(--danger); }

.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }

.top-navbar {
  background: var(--bg-card); padding: 20px 40px; display: flex; justify-content: space-between; align-items: center;
  border-bottom: 1px solid var(--border-color); transition: background-color 0.3s;
}

.sub-title { font-size: 11px; color: var(--primary); font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
.page-title { margin: 5px 0 0; font-size: 26px; color: var(--text-main); font-weight: 800; transition: color 0.3s; }

.header-actions { display: flex; align-items: center; gap: 20px; }
.btn-theme-toggle { background: var(--bg-hover); border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; transition: 0.2s; }
.btn-theme-toggle:hover { background: var(--border-color); transform: rotate(15deg); }
.avatar-small { width: 36px; height: 36px; background: var(--success-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 2px solid var(--primary); }

.content-body { padding: 30px 40px; }
</style>