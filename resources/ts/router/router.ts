import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router';
import ChatPage from "@/pages/Chat/ChatPage.vue";
import InboxPage from "@/pages/Inbox/InboxPage.vue";


const routes: Array<RouteRecordRaw> = [
    {
        path: '/chat',
        name: 'ChatPage',
        component: ChatPage,
    },
    {
        path: '/inbox',
        name: 'inbox',
        component: InboxPage,
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
