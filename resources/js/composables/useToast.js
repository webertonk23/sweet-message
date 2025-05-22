import { ref } from 'vue';

const message = ref('');
const type = ref('error');
const show = ref(false);

export function useToast() {
    const showToast = (msg, toastType = 'error', duration = 3000) => {
        message.value = msg;
        type.value = toastType;
        show.value = true;

        setTimeout(() => {
            show.value = false;
        }, duration);
    };

    return {
        message,
        type,
        show,
        showToast
    };
} 