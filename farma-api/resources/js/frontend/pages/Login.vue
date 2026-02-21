<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const errorMessage = ref(''); 
const isLoading = ref(false); 

const form = reactive({
    email: '',
    password: ''
});

const handleLogin = async () => {
    isLoading.value = true;
    errorMessage.value = ''; 

    try {
        // Tembak API Laravel
        const response = await axios.post('/api/login', {
            email: form.email,
            password: form.password
        });

        // Simpan token & user
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        // Pindah ke Dashboard
        router.push('/admin'); 

    } catch (error) {
        console.error(error);
        if (error.response && error.response.data.message) {
            errorMessage.value = error.response.data.message;
        } else {
            errorMessage.value = "Gagal terhubung ke server.";
        }
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <div class="login-wrapper">
        
        <div class="brand-header">
            <div class="logo-icon">💊</div>
            <h1>MediCare AI</h1>
        </div>

        <div class="login-card">
            <div class="card-header">
                <h3>Selamat Datang Kembali 👋</h3>
                <p>Masuk untuk mengelola apotik & inventori obat.</p>
            </div>

            <div v-if="errorMessage" class="alert-error">
                <span class="icon">⚠️</span> {{ errorMessage }}
            </div>

            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label>Email Staf / Dokter</label>
                    <div class="input-wrapper">
                        <span class="input-icon">📧</span>
                        <input 
                            type="email" 
                            v-model="form.email" 
                            placeholder="nama@medicare.com"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>
                        <input 
                            type="password" 
                            v-model="form.password" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                </div>

                <div class="forgot-pass">
                    <a href="#">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login" :disabled="isLoading">
                    {{ isLoading ? 'MEMVERIFIKASI...' : 'MASUK KE SISTEM' }}
                </button>
            </form>

            <div class="login-footer">
                <small>Sistem Manajemen Apotik v1.0</small>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-wrapper {
    display: flex; flex-direction: column; justify-content: center; align-items: center;
    height: 100vh; background-color: var(--bg-body);
    font-family: 'Inter', sans-serif; transition: background-color 0.3s;
}

/* BRAND LOGO */
.brand-header { display: flex; align-items: center; gap: 10px; margin-bottom: 25px; }
.logo-icon { 
    background: var(--primary); color: white; width: 45px; height: 45px; 
    border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4);
}
.brand-header h1 { font-size: 24px; font-weight: 800; color: var(--text-main); margin: 0; letter-spacing: -0.5px; }

/* CARD STYLE */
.login-card {
    background: var(--bg-card); padding: 40px; border-radius: 20px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
    width: 100%; max-width: 420px; border: 1px solid var(--border-color); transition: background-color 0.3s, border-color 0.3s;
}

.card-header { text-align: center; margin-bottom: 30px; }
.card-header h3 { margin: 0; color: var(--text-main); font-size: 20px; font-weight: 700; }
.card-header p { color: var(--text-muted); font-size: 14px; margin-top: 5px; }

/* ALERT ERROR */
.alert-error {
    background-color: var(--danger-bg); color: var(--danger); padding: 12px; border-radius: 8px;
    margin-bottom: 20px; font-size: 13px; display: flex; align-items: center; gap: 8px;
    border: 1px solid var(--danger-border); font-weight: 500;
}

/* FORM INPUTS */
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 8px; color: var(--text-muted); font-weight: 600; font-size: 13px; }

.input-wrapper { position: relative; }
.input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 16px; opacity: 0.7; }

.form-group input {
    width: 100%; padding: 12px 12px 12px 40px;
    border: 1px solid var(--border-color); border-radius: 10px; box-sizing: border-box;
    font-size: 14px; transition: all 0.3s; background: var(--input-bg); color: var(--text-main);
}

.form-group input:focus {
    border-color: var(--primary); background: var(--bg-card); outline: none;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.forgot-pass { text-align: right; margin-bottom: 20px; }
.forgot-pass a { color: var(--primary); font-size: 12px; text-decoration: none; font-weight: 600; }
.forgot-pass a:hover { text-decoration: underline; }

/* BUTTON */
.btn-login {
    width: 100%; padding: 14px;
    background-color: var(--primary); color: white; border: none; border-radius: 10px;
    font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 14px;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
}
.btn-login:hover { background-color: var(--primary-dark); transform: translateY(-1px); }
.btn-login:disabled { background-color: var(--text-muted); cursor: not-allowed; transform: none; box-shadow: none; }

.login-footer { margin-top: 30px; text-align: center; color: var(--text-muted); font-size: 12px; border-top: 1px solid var(--border-color); padding-top: 20px; }
</style>