import Vue from 'vue'
import VueRouter from 'vue-router'

import Dashboard from './pages/Dashboard.vue'
import Login from './pages/Login.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    component: Dashboard
  },
  {
    path: '/login',
    component: Login
  }
]

const router = new VueRouter({
	mode: 'history',
  routes
})

export default router