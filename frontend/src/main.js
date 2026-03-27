import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/app.css'
import axios from 'axios'

axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

createApp(App)
  .use(router)
  .mount('#app')
