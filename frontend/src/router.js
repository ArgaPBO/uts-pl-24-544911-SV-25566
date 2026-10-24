
import { createRouter, createWebHistory } from 'vue-router'

import LoginView from './views/LoginView.vue'
import BooksView from './views/BooksView.vue'

const routes = [
  {
    path: '/',
    redirect: '/books',
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guestOnly: true },
  },
  {
    path: '/books',
    name: 'books',
    component: BooksView,
    meta: { requiresAuth: true },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    return { name: 'login' }
  }

  if (to.meta.guestOnly && token) {
    return { name: 'books' }
  }
})

export default router
