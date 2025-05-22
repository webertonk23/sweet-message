<template>
    <div v-if="show" class="fixed top-20 right-4 z-50">
        <div :class="[
            'px-4 py-3 rounded-lg shadow-lg max-w-md',
            type === 'error' ? 'bg-red-500' : 'bg-green-500',
            'text-white'
        ]">
            {{ message }}
        </div>
    </div>
</template>

<script setup>
import { useToast } from '../composables/useToast';

const { message, type, show } = useToast();
</script> 