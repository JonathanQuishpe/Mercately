import { createRouter, createWebHistory } from 'vue-router'
import AdministrationView from '../views/AdministrationView.vue'
import ShopView from '../views/ShopView.vue'
import OrderView from '../views/OrderView.vue'

const routes = [
  {
    path: '/',
    name: 'administration',
    component: AdministrationView
  },
  {
    path: '/',
    name: 'administration',
    component: AdministrationView
  },
  {
    path: '/shop',
    name: 'shop',
    component: ShopView
  },
  {
    path: '/order',
    name: 'order',
    component: OrderView
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
