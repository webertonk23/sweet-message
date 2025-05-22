import axios from 'axios';
import { useToast } from '../composables/useToast';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Content-Type': 'application/json',
    },
});

api.interceptors.request.use(config => {
    const token = localStorage.getItem('token');

    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }

    return config;
}, error => {
    return Promise.reject(error);
});

api.interceptors.response.use(response => {
    return response;
}, error => {
    const { showToast } = useToast();

    if (error.response) {
        // Handle validation errors
        if (error.response.status === 422) {
            const errors = error.response.data.errors;
            if (errors) {
                // Display each validation error
                Object.values(errors).forEach(messages => {
                    messages.forEach(message => {
                        showToast(message, 'error');
                    });
                });
            }
        }

        else if (error.response.status === 401) {
            showToast('Sua sessão expirou. Por favor, faça login novamente.', 'error');
            localStorage.removeItem('token');
            window.location.href = '/login';
        }

        else {
            const message = error.response.data.message || 'Ocorreu um erro na requisição.';
            showToast(message, 'error');
        }
    } else if (error.request) {
        showToast('Não foi possível conectar ao servidor. Verifique sua conexão.', 'error');
    } else {
        showToast('Ocorreu um erro ao processar sua requisição.', 'error');
    }

    return Promise.reject(error);
});

export default api;
