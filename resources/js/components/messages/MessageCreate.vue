<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="md:grid md:grid-cols-3 md:gap-8">
                <div class="md:col-span-1">
                    <div class="px-4 sm:px-0">
                        <h3
                            class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Nova Mensagem</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Compartilhe suas ideias e pensamentos através de uma mensagem linda e emocionante.
                        </p>
                    </div>
                </div>
                <div class="mt-5 md:mt-0 md:col-span-2">
                    <form @submit.prevent="handleSubmit" class="space-y-6">
                        <div
                            class="bg-white shadow-lg rounded-2xl overflow-hidden transform transition-all duration-300 hover:shadow-xl">
                            <div class="px-6 py-8 space-y-6">
                                <div class="space-y-1">
                                    <label for="title" class="block text-sm font-medium text-gray-700">
                                        Título
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="text" id="title" v-model="form.title" required
                                            class="block w-full px-4 py-3 text-gray-900 placeholder-gray-400 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                                            placeholder="Digite o título da mensagem" :disabled="loading" />
                                    </div>
                                </div>

                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700">Conteúdo</label>
                                    <textarea id="content" v-model="form.content"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        rows="6"
                                        placeholder="Digite sua mensagem..."></textarea>
                                </div>

                                <div class="space-y-1">
                                    <label for="spotify_link" class="block text-sm font-medium text-gray-700">
                                        Link do Spotify (opcional)
                                    </label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <input type="url" id="spotify_link" v-model="form.spotify_link"
                                            class="block w-full px-4 py-3 text-gray-900 placeholder-gray-400 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300"
                                            placeholder="Cole aqui o link da música do Spotify" :disabled="loading" />
                                    </div>
                                    <p class="mt-1 text-sm text-gray-500">Exemplo: https://open.spotify.com/track/...
                                    </p>
                                </div>

                                <div class="space-y-1">
                                    <label class="block text-sm font-medium text-gray-700">
                                        Imagens (máximo 5)
                                    </label>
                                    <div
                                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-200 border-dashed rounded-lg">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600">
                                                <label for="file-upload"
                                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload de imagens</span>
                                                    <input id="file-upload" type="file" class="sr-only" multiple
                                                        accept="image/*" @change="handleFileUpload"
                                                        :disabled="loading || form.images.length >= 5">
                                                </label>
                                                <p class="pl-1">ou arraste e solte</p>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                PNG, JPG, GIF até 5MB cada
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview das imagens -->
                                <div v-if="form.images.length > 0" class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    <div v-for="(image, index) in form.images" :key="index" class="relative group">
                                        <img :src="image.preview" class="h-24 w-full object-cover rounded-lg" />
                                        <button type="button" @click="removeImage(index)"
                                            class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-4">
                                <button type="button" @click="router.push('/messages')"
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300"
                                    :disabled="loading">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="loading"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    {{ loading ? 'Enviando...' : 'Enviar Mensagem' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '../../composables/useToast';
import api from '../../services/api';

const router = useRouter();
const loading = ref(false);
const { showToast } = useToast();
const form = ref({
    title: '',
    content: '',
    spotify_link: '',
    images: []
});

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);

    if (form.value.images.length + files.length > 5) {
        showToast('Você pode adicionar no máximo 5 imagens', 'error');
        return;
    }

    files.forEach(file => {
        if (file.size > 5 * 1024 * 1024) { // 5MB
            showToast('Cada imagem deve ter no máximo 5MB', 'error');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            form.value.images.push({
                file: file,
                preview: e.target.result
            });
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    form.value.images.splice(index, 1);
};

const handleSubmit = async () => {
    loading.value = true;
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('content', form.value.content);
        formData.append('spotify_link', form.value.spotify_link);

        form.value.images.forEach((image) => {
            formData.append('images[]', image.file);
        });

        await api.post('/messages', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json'
            }
        });

        showToast('Mensagem criada com sucesso!', 'success');
        router.push('/messages');
    } catch (error) {
        console.error('Erro ao criar mensagem:', error);
        showToast(error.response?.data?.message || 'Erro ao criar mensagem', 'error');
    } finally {
        loading.value = false;
    }
};
</script>

<style>
/* Editor básico */
.ProseMirror {
    outline: none;
    min-height: 300px;
}

.ProseMirror:focus {
    outline: 2px solid #6366F1;
    border-radius: 0.375rem;
}

/* Estilo da Toolbar */
button {
    @apply px-3 py-1 border rounded bg-white hover:bg-indigo-50;
}
</style>