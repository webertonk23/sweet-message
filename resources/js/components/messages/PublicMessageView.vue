<template>
    <div class="relative h-screen overflow-hidden bg-gray-100">
        <div v-if="message?.images?.length" class="absolute inset-0 z-0">
            <img v-for="(image, index) in message.images" :key="'img-' + index" :src="getImageUrl(image.image_path)"
                :alt="'Imagem ' + index" :class="['absolute inset-0 w-full h-full object-cover transition-opacity duration-1000',
                    currentImageIndex === index ? 'opacity-100' : 'opacity-0']" />
        </div>

        <div class="relative z-10 h-screen flex flex-col">
            <header class="pt-8 px-4 sm:px-8">
                <h1 class="text-4xl sm:text-5xl font-bold text-center text-white drop-shadow-lg">
                    {{ message?.title }}
                </h1>
            </header>

            <main class="flex-grow flex items-end justify-center px-4">
                <div class="w-full bg-[#dcf8c6] p-6 sm:p-8 max-h-[15vh] overflow-y-auto rounded-3xl scrollbar-hide shadow-lg relative">
                    <div class="absolute -bottom-2 right-4 w-4 h-4 bg-[#dcf8c6] transform rotate-45"></div>
                    <div class="prose prose-xl max-w-none">
                        <p class="whitespace-pre-wrap text-gray-800 drop-shadow-sm text-xl sm:text-2xl" v-html="message?.content"></p>
                    </div>
                </div>
            </main>

            <footer class="px-4 sm:px-8 pb-4">
                <div v-if="message?.spotify_link" class="bg-opacity-70 rounded-xl p-4 max-w-xl mx-auto">
                    <iframe :src="getSpotifyEmbedUrl(message.spotify_link) + '?autoplay=1'" width="100%" height="152"
                        frameborder="0" allowfullscreen=""
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                        loading="lazy">
                    </iframe>
                </div>
            </footer>
        </div>
    </div>

    <!-- Loading e tratamento de erro -->
    <div v-if="loading" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-indigo-500"></div>
    </div>

    <div v-if="error" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-md w-full text-center">
            <svg class="h-12 w-12 text-red-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-800 mb-2">Mensagem não encontrada</h3>
            <p class="text-gray-600 mb-4">{{ error }}</p>
            <button @click="fetchMessage"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Tentar novamente
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../services/api';

const route = useRoute();
const message = ref(null);
const loading = ref(true);
const error = ref(null);
const currentImageIndex = ref(0);
let carouselInterval = null;

const getSpotifyEmbedUrl = (spotifyLink) => {
    const trackId = spotifyLink.split('/').pop();
    return `https://open.spotify.com/embed/track/${trackId}`;
};

const startCarousel = () => {
    if (message.value?.images?.length > 1) {
        carouselInterval = setInterval(() => {
            currentImageIndex.value = (currentImageIndex.value + 1) % message.value.images.length;
        }, 5000);
    }
};

const fetchMessage = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await api.get(`/${route.params.id}`);
        message.value = response.data;
        startCarousel();
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar a mensagem';
    } finally {
        loading.value = false;
    }
};

onMounted(fetchMessage);

onUnmounted(() => {
    if (carouselInterval) {
        clearInterval(carouselInterval);
    }
});

// Configuração
const debug = ref(true); // Mude para false em produção
const hasImages = ref(false);
const loadedImages = ref([]);

// Funções adicionadas
const getImageUrl = (path) => {
    // Adapte conforme sua estrutura de arquivos
    if (path.startsWith('http')) return path;
    if (path.startsWith('/storage')) return path;
    return `/storage/${path}`; // Padrão comum
};

const onImageLoad = (index) => {
    loadedImages.value[index] = true;
    if (!hasImages.value && loadedImages.value.some(Boolean)) {
        hasImages.value = true;
    }
    console.log(`Imagem ${index} carregada com sucesso`);
};

const onImageError = (index) => {
    console.error(`Erro ao carregar imagem ${index}:`, message.value.images[index].image_path);
    loadedImages.value[index] = false;
};
</script>

<style scoped>
.prose p {
    margin-bottom: 1em;
    line-height: 1.6;
}
</style>