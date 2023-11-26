import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'
import LoginView from "@/views/Auth/LoginView.vue";
import RegisterView from '@/views/Auth/RegisterView.vue';
import { getItem, removeItem } from "@/helpers/localStorage";

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      name: 'login',
      component: LoginView,
      beforeEnter: async (to, from, next) => {
        const token = getItem('token')
        if (token && await checkValidToken(token)) {
          next({name: 'home'})
          return;
        }
        next()
      },
      meta: {
        title: 'Pagina de Login'
      }
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView,
      meta: {
        title: 'Pagina de Cadastro'
      }
    }
  ]
})

async function checkValidToken(token?: string) {
  try {
    token = token ?? getItem('token')

    if (!token) {
      return false
    }

    const res = await axios.get('/api/user/profile', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })

    return true;
  } catch {
    removeItem('token')
    return false
  }
}

router.beforeEach((to, from, next) => {
  const titleFromParams = to.params?.pageTitle
  const appName = import.meta.env.VITE_APP_NAME
  const requireAuth = to.matched.some((record) => record.meta.requiresAuth)

  if (requireAuth && !checkValidToken()) {
    next({name: 'login'})
    return
  }

  if (titleFromParams) {
    document.title = `${titleFromParams} - ${document.title} - ${appName}`
  } else {
    document.title = `${to.meta?.title} - ${appName}` ?? appName
  }

  next()
})

export default router
