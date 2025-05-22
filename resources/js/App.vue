<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Toast />
        <!-- Navigation -->
        <nav v-if="isAuthenticated" class="bg-white shadow-lg backdrop-blur-sm bg-opacity-90 sticky top-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 flex items-center">
                            <router-link to="/messages" class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent hover:from-indigo-700 hover:to-purple-700 transition-all duration-300">
                                Sweet Message
                            </router-link>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <router-link 
                            to="/messages/create"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-md hover:shadow-lg"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                            </svg>
                            Nova Mensagem
                        </router-link>
                        <button 
                            @click="logout"
                            class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transform hover:scale-105 transition-all duration-300 shadow-sm hover:shadow-md"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V4a1 1 0 00-1-1H3zm11 4a1 1 0 10-2 0v4a1 1 0 102 0V7zm-3 1a1 1 0 10-2 0v3a1 1 0 102 0V8zM8 9a1 1 0 00-2 0v2a1 1 0 102 0V9z" clip-rule="evenodd" />
                            </svg>
                            Sair
                        </button>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main :class="[isAuthenticated ? 'py-12' : '']">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <transition 
                    name="fade" 
                    mode="out-in"
                >
                    <router-view></router-view>
                </transition>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import Toast from './components/Toast.vue';

const router = useRouter();
const isAuthenticated = computed(() => !!localStorage.getItem('token'));

const logout = async () => {
    try {
        await fetch('/api/auth/logout', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`,
                'Accept': 'application/json',
            },
        });
        localStorage.removeItem('token');
        router.push('/login');
    } catch (error) {
        console.error('Erro ao fazer logout:', error);
        showToast(error.message);
    }
};
</script>

<style>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #666;
}
</style> 