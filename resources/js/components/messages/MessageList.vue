<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Minhas Mensagens
                </h2>
                <router-link to="/messages/create"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-plus mr-2"></i>
                    Nova Mensagem
                </router-link>
            </div>

            <div v-if="loading" class="flex justify-center items-center min-h-[200px]">
                <i class="fas fa-spinner fa-spin text-4xl text-indigo-500"></i>
            </div>

            <div v-else-if="error" class="text-center py-12">
                <div class="bg-red-50 border border-red-200 rounded-lg p-6 max-w-lg mx-auto">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-red-800 mb-2">Erro ao carregar mensagens</h3>
                    <p class="text-red-600 mb-4">{{ error }}</p>
                    <button @click="fetchMessages"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-redo mr-2"></i>
                        Tentar novamente
                    </button>
                </div>
            </div>

            <div v-else-if="messages.length === 0" class="text-center py-12">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 max-w-lg mx-auto">
                    <i class="fas fa-inbox text-4xl text-gray-400 mx-auto mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma mensagem encontrada</h3>
                    <p class="text-gray-500 mb-4">Comece criando sua primeira mensagem!</p>
                    <router-link to="/messages/create"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i>
                        Criar Mensagem
                    </router-link>
                </div>
            </div>

            <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div v-for="message in messages" :key="message.id"
                    class="bg-white shadow-lg rounded-2xl overflow-hidden transform transition-all duration-300 hover:shadow-xl">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ message.title }}</h3>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-500 mr-2">Público</span>
                                    <button @click="togglePublic(message)" 
                                        :class="[
                                            'relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2',
                                            message.is_public ? 'bg-indigo-600' : 'bg-gray-200'
                                        ]"
                                        :disabled="updatingPublic === message.id">
                                        <span class="sr-only">Tornar mensagem pública</span>
                                        <span :class="[
                                            'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                            message.is_public ? 'translate-x-6' : 'translate-x-1'
                                        ]" />
                                    </button>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="showQRCode(message)"
                                        class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                        title="Gerar QR Code"
                                        :disabled="!message.is_public">
                                        <i class="fas fa-qrcode text-lg" :class="{ 'opacity-50': !message.is_public }"></i>
                                    </button>
                                    <router-link :to="`/messages/${message.id}`"
                                        class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200"
                                        title="Ver mensagem">
                                        <i class="fas fa-eye text-lg"></i>
                                    </router-link>
                                    <button @click="confirmDelete(message)"
                                        class="text-red-600 hover:text-red-900 transition-colors duration-200"
                                        title="Excluir mensagem">
                                        <i class="fas fa-trash-alt text-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="text-gray-600 line-clamp-3 mb-4" v-html="message.content"></div>
                        <div class="flex items-center justify-between text-sm text-gray-500">
                            <span>{{ formatDate(message.created_at) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar exclusão</h3>
            <p class="text-gray-500 mb-6">Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end space-x-4">
                <button @click="showDeleteModal = false"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <button @click="deleteMessage"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-trash-alt mr-2"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de QR Code -->
    <div v-if="showQRModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg p-6 max-w-sm w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">QR Code da Mensagem</h3>
                <button @click="showQRModal = false" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="text-center">
                <div class="py-4">
                    <div ref="qrCodeContainer">
                        <QRCode :value="publicUrl" :size="200" level="H" class="mx-auto mb-4" />
                        <p class="text-sm text-gray-500 mb-4">Escaneie o QR Code para acessar a mensagem</p>
                        <p class="text-sm text-gray-500 mb-4">{{ publicUrl }}</p>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <input type="text" :value="publicUrl" readonly 
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                        <button @click="copyToClipboard" 
                            class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            title="Copiar link">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button @click="downloadQRCode" 
                            class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            title="Baixar QR Code">
                            <i class="fas fa-download"></i>
                        </button>
                        <button @click="printQRCode" 
                            class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            title="Imprimir QR Code">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from '../../composables/useToast';
import api from '../../services/api';
import QRCode from 'qrcode.vue';
import '@fortawesome/fontawesome-free/css/all.css';

const router = useRouter();
const { showToast } = useToast();
const messages = ref([]);
const loading = ref(true);
const error = ref(null);
const showDeleteModal = ref(false);
const messageToDelete = ref(null);
const showQRModal = ref(false);
const publicUrl = ref('');
const updatingPublic = ref(null);
const qrCodeContainer = ref(null);

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('pt-BR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const fetchMessages = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await api.get('/messages');
        messages.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message || 'Erro ao carregar mensagens';
        showToast(error.value, 'error');
    } finally {
        loading.value = false;
    }
};

const confirmDelete = (message) => {
    messageToDelete.value = message;
    showDeleteModal.value = true;
};

const deleteMessage = async () => {
    try {
        await api.delete(`/messages/${messageToDelete.value.id}`);
        showToast('Mensagem excluída com sucesso!', 'success');
        messages.value = messages.value.filter(m => m.id !== messageToDelete.value.id);
        showDeleteModal.value = false;
        messageToDelete.value = null;
    } catch (err) {
        showToast(err.response?.data?.message || 'Erro ao excluir mensagem', 'error');
    }
};

const showQRCode = async (message) => {
    if (!message.is_public) {
        showToast('Apenas mensagens públicas podem ter QR Code', 'error');
        return;
    }

    showQRModal.value = true;
    publicUrl.value = `${window.location.origin}/${message.id}`;
};

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(publicUrl.value);
        showToast('Link copiado para a área de transferência!', 'success');
    } catch (err) {
        showToast('Erro ao copiar link', 'error');
    }
};

const downloadQRCode = () => {
    const canvas = qrCodeContainer.value.querySelector('canvas');
    const link = document.createElement('a');
    link.download = `qr-code-${publicUrl.value.split('/').pop()}.png`;
    link.href = canvas.toDataURL('image/png');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

const printQRCode = () => {
    const printWindow = window.open('', '_blank');
    const qrCodeImage = qrCodeContainer.value.querySelector('canvas').toDataURL();
    
    const style = `
        body { font-family: Arial; text-align: center; padding: 20px; }
        .qr-code { margin: 20px auto; }
        .url { word-break: break-all; max-width: 300px; margin: 10px auto; }
    `;
    
    const html = `
        <div class="qr-container">
            <img src="${qrCodeImage}" alt="QR Code" class="qr-code" width="200" height="200" />
            <p class="url">${publicUrl.value}</p>
        </div>
    `;
    
    printWindow.document.write(`
        <html>
            <head>
                <title>QR Code</title>
                <style>${style}</style>
            </head>
            <body>${html}</body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
};

const togglePublic = async (message) => {
    if (updatingPublic.value === message.id) return;
    
    updatingPublic.value = message.id;
    try {
        const response = await api.put(`/messages/${message.id}`, {
            is_public: !message.is_public
        });
        
        // Update the message in the list
        const index = messages.value.findIndex(m => m.id === message.id);
        if (index !== -1) {
            messages.value[index] = response.data;
        }
        
        showToast(
            `Mensagem ${response.data.is_public ? 'tornada pública' : 'tornada privada'} com sucesso!`,
            'success'
        );
    } catch (err) {
        showToast(err.response?.data?.message || 'Erro ao atualizar mensagem', 'error');
    } finally {
        updatingPublic.value = null;
    }
};

onMounted(fetchMessages);
</script>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>