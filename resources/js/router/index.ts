import {createRouter, createWebHistory} from 'vue-router'
import LoginView from "@/views/Auth/LoginView.vue";
import RegisterView from '@/views/Auth/RegisterView.vue';

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
    },
  ]
})

export default router
