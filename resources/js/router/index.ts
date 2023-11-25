import { createRouter, createWebHistory } from 'vue-router'
import LoginView from "@/views/Auth/LoginView.vue";

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
    }
  ]
})
export default router
