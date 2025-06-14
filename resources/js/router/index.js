import { createWebHistory, createRouter } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'login',
        component: () => import('../Pages/Auth/Login.vue'),
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../Pages/Auth/Register.vue'),
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('../Pages/Dashboard.vue'),
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const isAuthenticated = localStorage.getItem('token');

    if (requiresAuth && !isAuthenticated) {
        next('/login');
    } else {
        next();
    }
});

export default router;
