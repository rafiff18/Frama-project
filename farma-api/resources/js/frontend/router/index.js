import { createRouter, createWebHistory } from 'vue-router'

import LandingPage from '../pages/LandingPage.vue'
import Login from '../pages/login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Menu from '../pages/Menu.vue'
import PosSystem from '../pages/transaksi/PosSystem.vue'
import InventoryIndex from '../pages/inventori/InventoryIndex.vue'
import ResepIndex from '../pages/resep/ResepIndex.vue'
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
        name: 'Dashboard'
      },
      { path: 'menu', component: Menu, name: 'Menu' },
      { path: 'kasir', component: PosSystem, name: 'Kasir' },
      { path: 'inventori', component: InventoryIndex, name: 'Inventory' },
      { path: 'resep', component: ResepIndex, name: 'Resep' },
      { path: 'laporan', component: LaporanIndex, name: 'Laporan' },
      { path: 'staf', component: StafIndex, name: 'Staf' },
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

    if (to.path === '/login' && loggedIn) {
        next('/admin')
        return
    }

    next()
})

export default router