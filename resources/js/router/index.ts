import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import axios from 'axios'
import LoginView from "@/views/Auth/LoginView.vue";
import RegisterView from '@/views/Auth/RegisterView.vue';
import Dashboard from "@/components/Dashboard.vue";
import { getItem, removeItem } from "@/helpers/localStorage";

const dashboardRoutes: RouteRecordRaw[] = [
  {
    path: '',
    name: 'home',
    component: () => import('../views/HomeView.vue'),
    meta: {
      title: 'Home',
      requiresAuth: true
    }
  },
  {
    path: 'patients',
    name: 'patients',
    meta: {
      title: 'Lista de Pacientes',
      requiresAuth: true
    },
    component: () => import('../views/Patients/ListView.vue')
  },
  {
    path: 'patients/create',
    name: 'patient-create',
    meta: {
      title: 'Criar Paciente',
      requiresAuth: true
    },
    component: () => import('../views/Patients/CreateView.vue')
  },
  {
    path: 'patients/update/:id',
    name: 'patient-update',
    meta: {
      title: 'Atualizar Paciente',
      requiresAuth: true
    },
    component: () => import('../views/Patients/UpdateView.vue')
  },
  {
    path: 'patients/import-csv',
    name: 'patient-import',
    meta: {
      title: 'Importar Pacientes',
      requiresAuth: true
    },
    component: () => import('../views/Patients/ImportView.vue')
  }
]

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
    },
    {
      path: '/dashboard',
      component: Dashboard,
      meta: {
        requiresAuth: true
      },
      children: [
        ...dashboardRoutes
      ]
    },
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
