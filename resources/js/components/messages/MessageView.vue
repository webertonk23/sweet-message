<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div v-if="loading" class="flex justify-center items-center h-64">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-200 border-t-indigo-600"></div>
            </div>

            <div v-else-if="error" class="text-center py-16 bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="mx-auto w-24 h-24 bg-red-50 rounded-full flex items-center justify-center">
                    <svg class="h-12 w-12 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Erro ao carregar mensagem</h3>
                <p class="mt-2 text-sm text-gray-500">{{ error }}</p>
                <div class="mt-6">
                    <button
                        @click="fetchMessage"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-md hover:shadow-lg"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                        </svg>
                        Tentar novamente
                    </button>
                </div>
            </div>

            <div v-else class="bg-white shadow-lg rounded-2xl overflow-hidden transform transition-all duration-300 hover:shadow-xl">
                <div class="px-8 py-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ message.title }}
                            </h3>
                            <p class="mt-2 text-sm text-gray-500 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                </svg>
                                Enviada por {{ message.sender_name }}
                            </p>
                        </div>
                        <span
                            :class="[
                                message.is_read ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700',
                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium transition-colors duration-300'
                            ]"
                        >
                            <svg v-if="message.is_read" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            {{ message.is_read ? 'Lida' : 'Não lida' }}
                        </span>
                    </div>
                </div>

                <!-- Spotify Player -->
                <div v-if="message.spotify_link" class="px-8 py-6 border-b border-gray-100">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Música</h4>
                    <iframe
                        :src="getSpotifyEmbedUrl(message.spotify_link)"
                        width="100%"
                        height="152"
                        frameborder="0"
                        allowfullscreen=""
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                        loading="lazy"
                        class="rounded-lg shadow-sm"
                    ></iframe>
                </div>

                <!-- Images Gallery -->
                <div v-if="message.images && message.images.length > 0" class="px-8 py-6 border-b border-gray-100">
                    <h4 class="text-sm font-medium text-gray-700 mb-4">Imagens</h4>
                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                        <div v-for="image in message.images" :key="image.id" class="relative group">
                            <img
                                :src="'/storage/' + image.image_path"
                                class="h-48 w-full object-cover rounded-lg shadow-sm hover:shadow-md transition-all duration-300"
                                @click="openImageModal(image)"
                            />
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6">
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ message.content }}</p>
                    </div>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Data de envio</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ new Date(message.created_at).toLocaleString() }}
                                </dd>
                            </div>
                        </div>
                    </dl>
                </div>

                <div class="px-8 py-6 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <router-link
                        to="/messages"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-md hover:shadow-lg"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Voltar para mensagens
                    </router-link>
                </div>
            </div>

            <!-- Image Modal -->
            <div v-if="selectedImage" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closeImageModal"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                        <div class="relative">
                            <button
                                @click="closeImageModal"
                                class="absolute top-4 right-4 text-gray-400 hover:text-gray-500 focus:outline-none"
                            >
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <img
                                :src="'/storage/' + selectedImage.image_path"
                                class="w-full h-auto max-h-[80vh] object-contain"
                                alt="Imagem ampliada"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useToast } from '../../composables/useToast';
import api from '../../services/api';

const route = useRoute();
const message = ref(null);
const loading = ref(true);
const error = ref(null);
const selectedImage = ref(null);
const { showToast } = useToast();

const getSpotifyEmbedUrl = (spotifyLink) => {
    if (!spotifyLink) return '';
    const trackId = spotifyLink.split('/').pop();
    return `https://open.spotify.com/embed/track/${trackId}`;
};

const openImageModal = (image) => {
    selectedImage.value = image;
};

const closeImageModal = () => {
    selectedImage.value = null;
};

const fetchMessage = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await api.get(`/messages/${route.params.id}`);
        message.value = response.data;
    } catch (err) {
        console.error('Erro ao carregar mensagem:', err);
        error.value = err.response?.data?.message || 'Erro ao carregar mensagem';
        showToast(error.value, 'error');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchMessage();
});
</script>

<style scoped>
.prose {
    max-width: 65ch;
    color: #374151;
}

.prose p {
    margin-top: 1.25em;
    margin-bottom: 1.25em;
}
</style> 