import { createRouter, createWebHistory } from 'vue-router'

import LandingPage from '../pages/LandingPage.vue'
import Login from '../pages/Login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Menu from '../pages/Menu.vue'
import PosSystem from '../pages/transaksi/PosSystem.vue'
import InventoryIndex from '../pages/inventori/InventoryIndex.vue'
import PenerimaanObat from '../pages/inventori/PenerimaanObat.vue'
import SupplierIndex from '../pages/inventori/SupplierIndex.vue'
import LaporanIndex from '../pages/laporan/LaporanIndex.vue'
import StafIndex from '../pages/staf/StafIndex.vue'
import MainLayout from '../layouts/MainLayout.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: LandingPage
  },
  {
    path: '/login',
    component: Login,
    name: 'Login'
  },
  {
    path: '/admin',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        component: Dashboard,
        name: 'Dashboard',
        meta: { allowedRoles: ['superadmin', 'apoteker', 'kasir', 'owner'] } // Everyone access dashboard
      },
      {
        path: 'menu',
        component: Menu,
        name: 'Menu',
        meta: { allowedRoles: ['superadmin'] }
      },
      {
        path: 'kasir',
        component: PosSystem,
        name: 'Kasir',
        meta: { allowedRoles: ['superadmin', 'kasir'] }
      },
      {
        path: 'inventori',
        component: InventoryIndex,
        name: 'Inventori',
        meta: { allowedRoles: ['superadmin', 'owner'] }
      },
      {
        path: 'penerimaan',
        component: PenerimaanObat,
        name: 'Penerimaan',
        meta: { allowedRoles: ['superadmin', 'apoteker'] }
      },
      {
        path: 'suppliers',
        component: SupplierIndex,
        name: 'Suppliers',
        meta: { allowedRoles: ['superadmin', 'apoteker'] }
      },
      {
        path: 'laporan',
        component: LaporanIndex,
        name: 'Laporan',
        meta: { allowedRoles: ['superadmin', 'owner'] }
      },
      {
        path: 'staf',
        component: StafIndex,
        name: 'Staf',
        meta: { allowedRoles: ['superadmin'] }
      },
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const loggedIn = localStorage.getItem('token')

  if (to.matched.some(record => record.meta.requiresAuth) && !loggedIn) {
    next('/login')
    return
  }



  next()
})

export default router