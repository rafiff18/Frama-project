<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { supabase } from '../lib/supabase';
import logoImg from '../assets/images/logo.png';

const router = useRouter();
const errorMessage = ref(''); 
const isLoading = ref(false); 
const showPassword = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const form = reactive({
    email: '',
    password: ''
});

const handleLogin = async () => {
    isLoading.value = true;
    errorMessage.value = ''; 

    try {
        const { data, error } = await supabase.auth.signInWithPassword({
            email: form.email,
            password: form.password
        });

        if (error) {
            errorMessage.value = error.message;
            isLoading.value = false;
            return;
        }

        // Simpan token & user
        localStorage.setItem('token', data.session.access_token);
        
        // Supabase user doesn't have custom roles by default in user metadata unless set up.
        // We will default to a fallback local user object or fetch it from a 'users' table if needed.
        // For now, assume auth is sufficient.
        localStorage.setItem('user', JSON.stringify({
            name: data.user.email.split('@')[0],
            role: 'superadmin' // HARDCODED for now to allow access, needs future RBAC implementation against 'users' DB
        }));

        router.push('/admin');

    } catch (error) {
        console.error(error);
        errorMessage.value = "Gagal terhubung ke server.";
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center font-outfit relative overflow-hidden bg-slate-50 dark:bg-slate-900">
        
        <!-- Glowing Background Orbs -->
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-primary/20 dark:bg-primary/10 rounded-full blur-[100px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[600px] h-[600px] bg-emerald-400/20 dark:bg-emerald-800/20 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="w-full max-w-[440px] px-6 relative z-10">
            
            <!-- Brand Header -->
            <div class="flex flex-col items-center justify-center mb-8 cursor-pointer" @click="router.push('/')">
                <div class="p-3 bg-white/50 dark:bg-slate-800/50 rounded-2xl backdrop-blur-sm shadow-sm mb-4 border border-white/60 dark:border-slate-700/50">
                    <img :src="logoImg" alt="RR Farma Logo" class="h-16 w-16 object-contain drop-shadow-md" />
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-slate-800 dark:text-white">RR FARMA</h1>
            </div>

            <!-- Login Card -->
            <div class="glass dark:bg-slate-800/80 rounded-3xl p-8 shadow-2xl shadow-primary/10 border border-white/50 dark:border-slate-700/50">
                
                <div class="text-center mb-8">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center justify-center gap-2">
                        Selamat Datang 👋
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 font-medium">Masuk untuk mengelola sistem apotek.</p>
                </div>

                <div v-if="errorMessage" class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 flex items-start gap-3 text-red-600 dark:text-red-400 text-sm font-semibold animate-shake">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ errorMessage }}</span>
                </div>

                <form @submit.prevent="handleLogin" class="space-y-5">
                    
                    <!-- Email Field -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Email Staf</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <input 
                                type="email" 
                                v-model="form.email" 
                                placeholder="nama@apotek.com"
                                required
                                class="w-full pl-11 pr-4 py-3.5 bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input 
                                :type="showPassword ? 'text' : 'password'" 
                                v-model="form.password" 
                                placeholder="••••••••"
                                required
                                class="w-full pl-11 pr-12 py-3.5 bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all shadow-sm"
                            >
                            <button type="button" @click="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-primary transition-colors focus:outline-none">
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                  <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                  <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded cursor-pointer">
                            <label for="remember-me" class="ml-2 block text-sm text-slate-600 dark:text-slate-400 cursor-pointer">
                                Ingat saya
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-semibold text-primary hover:text-primary-dark transition-colors">Lupa sandi?</a>
                        </div>
                    </div>

                    <button type="submit" :disabled="isLoading" class="w-full flex justify-center py-4 px-4 border border-transparent rounded-xl shadow-lg shadow-primary/30 text-sm font-bold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all disabled:opacity-70 disabled:cursor-not-allowed mt-4 transform hover:-translate-y-0.5">
                        <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        {{ isLoading ? 'MEMVERIFIKASI...' : 'MASUK KE SISTEM' }}
                    </button>
                    
                </form>

            </div>
            
            <p class="mt-8 text-center text-xs text-slate-500 dark:text-slate-500 font-medium">
                &copy; 2026 RR FARMA &middot; Sistem Manajemen Apotek v1.0
            </p>
        </div>
    </div>
</template>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
    20%, 40%, 60%, 80% { transform: translateX(4px); }
}
.animate-shake {
    animation: shake 0.6s cubic-bezier(.36,.07,.19,.97) both;
}
</style>