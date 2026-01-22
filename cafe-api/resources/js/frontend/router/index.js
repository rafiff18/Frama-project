import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Menu from '../pages/Menu.vue'

const routes = [
  { path: '/login', component: Login },
  { path: '/', component: Dashboard },
  { path: '/menu', component: Menu }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
