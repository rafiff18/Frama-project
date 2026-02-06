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
            <button class="btn-check-health">✨ Cek Kesehatan Inventori</button>
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
import { computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

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
  display: flex; min-height: 100vh; font-family: 'Inter', sans-serif; background-color: #f8fafc;
}

.sidebar {
  width: 260px; background-color: #0f172a; color: #94a3b8;
  display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 10;
}

.sidebar-header {
  padding: 24px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #1e293b;
}

.logo-icon {
  background: #10b981; color: white; width: 40px; height: 40px;
  display: flex; align-items: center; justify-content: center;
  border-radius: 10px; font-size: 22px;
}

.brand-text { display: flex; flex-direction: column; }
.brand-title { color: white; font-weight: 700; font-size: 16px; }
.brand-subtitle { font-size: 10px; color: #10b981; font-weight: 600; letter-spacing: 0.5px; }

.menu { padding: 20px 16px; flex: 1; }
.menu-category { font-size: 10px; font-weight: 700; color: #475569; margin-bottom: 12px; letter-spacing: 1px; }

.menu-item {
  display: flex; align-items: center; gap: 12px; padding: 12px 16px;
  color: #94a3b8; text-decoration: none; border-radius: 8px; margin-bottom: 4px;
  transition: all 0.3s; font-weight: 500; font-size: 14px;
}

.menu-item:hover { background-color: #1e293b; color: white; }

.menu-item.active {
  background-color: #10b981; color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.sidebar-footer { padding: 20px; border-top: 1px solid #1e293b; background-color: #0b1120; }
.role-selector small { display: block; font-size: 10px; margin-bottom: 8px; color: #64748b; }

.user-profile { display: flex; align-items: center; gap: 10px; }
.avatar { width: 32px; height: 32px; background: #334155; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.user-details { display: flex; flex-direction: column; }
.name { color: white; font-size: 13px; font-weight: 600; }
.role { color: #10b981; font-size: 11px; }

.logout-link { display: flex; align-items: center; gap: 10px; margin-top: 15px; color: #94a3b8; text-decoration: none; font-size: 13px; }
.logout-link:hover { color: #f87171; }

.main-content { margin-left: 260px; flex: 1; display: flex; flex-direction: column; }

.top-navbar {
  background: white; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center;
  border-bottom: 1px solid #e2e8f0;
}

.sub-title { font-size: 11px; color: #10b981; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
.page-title { margin: 5px 0 0; font-size: 26px; color: #0f172a; font-weight: 800; }

.header-actions { display: flex; align-items: center; gap: 20px; }
.btn-check-health {
    background: #10b981; color: white; border: none; padding: 10px 20px;
    border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 13px;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
}
.avatar-small { width: 36px; height: 36px; background: #ecfdf5; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; border: 2px solid #10b981; }

.content-body { padding: 30px 40px; }
</style>