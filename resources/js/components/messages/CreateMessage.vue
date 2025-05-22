<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Nova Mensagem
                </h2>
            </div>

            <form @submit.prevent="submitForm" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Título</label>
                    <input type="text" id="title" v-model="form.title"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required />
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">Conteúdo</label>
                    <div class="mt-1">
                        <Editor
                            v-model="form.content"
                            :init="{
                                height: 400,
                                menubar: true,
                                branding: false,
                                promotion: false,
                                resize: false,
                                plugins: [
                                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                                ],
                                toolbar: 'undo redo | formatselect | ' +
                                    'bold italic backcolor | alignleft aligncenter ' +
                                    'alignright alignjustify | bullist numlist outdent indent | ' +
                                    'removeformat | help',
                                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif; font-size: 14px; }',
                                skin: 'oxide',
                                language: 'pt_BR',
                                language_url: 'https://cdn.tiny.cloud/1/no-api-key/tinymce/6/langs/pt_BR.js',

                                setup: (editor) => {
                                    editor.on('init', () => {
                                        editor.getContainer().style.transition = 'border-color 0.15s ease-in-out';
                                    });
                                }
                            }"
                            api-key="no-api-key"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Imagens</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48" aria-hidden="true">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="file-upload"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload de imagens</span>
                                    <input id="file-upload" name="file-upload" type="file" class="sr-only" multiple
                                        @change="handleFileUpload" accept="image/*" />
                                </label>
                                <p class="pl-1">ou arraste e solte</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF até 10MB</p>
                        </div>
                    </div>
                    <div v-if="form.images.length > 0" class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                        <div v-for="(image, index) in form.images" :key="index"
                            class="relative group aspect-w-1 aspect-h-1">
                            <img :src="image.preview" :alt="`Imagem ${index + 1}`"
                                class="object-cover rounded-lg shadow-sm" />
                            <button type="button" @click="removeImage(index)"
                                class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_public" v-model="form.is_public"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                    <label for="is_public" class="ml-2 block text-sm text-gray-900">
                        Tornar mensagem pública
                    </label>
                </div>

                <div class="flex justify-end space-x-4">
                    <router-link to="/messages"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </router-link>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        :disabled="loading">
                        <i class="fas fa-spinner fa-spin mr-2" v-if="loading"></i>
                        <i class="fas fa-save mr-2" v-else></i>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '../../composables/useToast';
import api from '../../services/api';
import Editor from '@tinymce/tinymce-vue';

import 'tinymce/tinymce';
import 'tinymce/icons/default'; // <- Ícones
import 'tinymce/themes/silver'; // <- Tema Silver

// Plugins que você usa:
import 'tinymce/plugins/advlist';
import 'tinymce/plugins/autolink';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/charmap';
import 'tinymce/plugins/preview';
import 'tinymce/plugins/anchor';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/visualblocks';
import 'tinymce/plugins/code';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/insertdatetime';
import 'tinymce/plugins/media';
import 'tinymce/plugins/table';
import 'tinymce/plugins/help';
import 'tinymce/plugins/wordcount';

const router = useRouter();
const { showToast } = useToast();
const loading = ref(false);

const form = ref({
    title: '',
    content: '',
    images: [],
    is_public: false
});

const handleFileUpload = (event) => {
    const files = Array.from(event.target.files);
    const maxSize = 10 * 1024 * 1024; // 10MB

    files.forEach(file => {
        if (file.size > maxSize) {
            showToast('Arquivo muito grande. O tamanho máximo é 10MB.', 'error');
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

const submitForm = async () => {
    loading.value = true;
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('content', form.value.content);
        formData.append('is_public', form.value.is_public);

        form.value.images.forEach((image, index) => {
            formData.append(`images[${index}]`, image.file);
        });

        await api.post('/messages', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        showToast('Mensagem criada com sucesso!', 'success');
        router.push('/messages');
    } catch (err) {
        showToast(err.response?.data?.message || 'Erro ao criar mensagem', 'error');
    } finally {
        loading.value = false;
    }
};
</script>

<style>
.tox-tinymce {
    border-radius: 0.375rem !important;
    border-color: #D1D5DB !important;
    min-height: 400px !important;
}

.tox-tinymce:focus-within {
    border-color: #6366F1 !important;
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
}

.tox .tox-toolbar__primary {
    background: #f9fafb !important;
    border-bottom: 1px solid #e5e7eb !important;
}

.tox .tox-toolbar__group {
    border-color: #e5e7eb !important;
}

.tox .tox-tbtn {
    color: #374151 !important;
}

.tox .tox-tbtn:hover {
    background: #f3f4f6 !important;
}

.tox .tox-tbtn--enabled {
    background: #e5e7eb !important;
}

.tox .tox-tbtn--enabled:hover {
    background: #d1d5db !important;
}

.tox .tox-edit-area__iframe {
    background: white !important;
}
</style> 