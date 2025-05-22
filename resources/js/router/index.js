import { createRouter, createWebHistory } from 'vue-router';

// Importações diretas (opcional, pode usar lazy loading se preferir)
import PublicMessageView from '../components/messages/PublicMessageView.vue';
import Login from '../components/auth/Login.vue';
import Register from '../components/auth/Register.vue';
import MessageList from '../components/messages/MessageList.vue';
import MessageCreate from '../components/messages/MessageCreate.vue';
import MessageView from '../components/messages/MessageView.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        redirect: '/mensages',
        meta: { requiresAuth: true }
    },
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { requiresAuth: false }
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { requiresAuth: false }
    },
    {
        path: '/messages',
        name: 'messages',
        component: MessageList,
        meta: { requiresAuth: true }
    },
    {
        path: '/messages/create',
        name: 'message-create',
        component: MessageCreate,
        meta: { requiresAuth: true }
    },
    {
        path: '/messages/:id',
        name: 'message-view',
        component: MessageView,
        meta: { requiresAuth: true }
    },
    {
        path: '/:id',
        name: 'public-message',
        component: PublicMessageView,
        meta: { requiresAuth: false }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token');
    if (to.meta.requiresAuth && !token) {
        next('/login');
    }

    else if ((to.name === 'login' || to.name === 'register') && token) {
        next('/messages');
    }

    else {
        next();
    }
});

export default router;