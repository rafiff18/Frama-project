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
    // 1. Ubah tombol jadi loading
    isLoading.value = true;
    errorMessage.value = ''; 

    try {
        console.log("Mengirim data ke server...", form.email); // Cek log baru

        // 2. Tembak API Laravel
        const response = await axios.post('http://127.0.0.1:8000/api/login', {
            email: form.email,
            password: form.password
        });

        // 3. Jika Sukses
        console.log("Login Berhasil!", response.data);
        
        // Simpan token biar sistem tahu kita siapa
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));

        // Pindah ke Dashboard
        router.push('/'); 

    } catch (error) {
        // 4. Jika Gagal
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
        <div class="login-card">
            <div class="login-header">
                <h2>☕ Cafe Login</h2>
                <p>Silakan masuk untuk memulai shift</p>
            </div>

            <div v-if="errorMessage" class="alert-error">
                ⚠️ {{ errorMessage }}
            </div>

            <form @submit.prevent="handleLogin">
                <div class="form-group">
                    <label>Email</label>
                    <input 
                        type="email" 
                        v-model="form.email" 
                        placeholder="kasir@cafe.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input 
                        type="password" 
                        v-model="form.password" 
                        placeholder="********"
                        required
                    >
                </div>

                <button type="submit" class="btn-login" :disabled="isLoading">
                    {{ isLoading ? 'MEMPROSES...' : 'MASUK SISTEM' }}
                </button>
            </form>

            <div class="login-footer">
                <small>Lupa password? Hubungi Admin.</small>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f0f2f5;
    font-family: 'Segoe UI', sans-serif;
}

.login-card {
    background: white;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    width: 100%;
    max-width: 400px;
}

.login-header h2 { margin: 0; color: #2c3e50; }
.login-header p { color: #7f8c8d; margin-bottom: 20px; }

.alert-error {
    background-color: #ffebee;
    color: #c62828;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    font-size: 14px;
    text-align: center;
    border: 1px solid #ffcdd2;
}

.form-group { margin-bottom: 15px; }
.form-group label { display: block; margin-bottom: 5px; color: #34495e; font-weight: 600; }
.form-group input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
.form-group input:focus { border-color: #3498db; outline: none; }

.btn-login {
    width: 100%;
    padding: 12px;
    background-color: #2c3e50;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
}
.btn-login:hover { background-color: #34495e; }
.btn-login:disabled { background-color: #95a5a6; cursor: not-allowed; }

.login-footer { margin-top: 20px; text-align: center; color: #95a5a6; }
</style>