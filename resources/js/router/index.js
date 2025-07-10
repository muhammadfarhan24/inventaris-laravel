import { createRouter, createWebHistory } from 'vue-router'
import LoginPage from '../views/Login.vue'
import Layout from '@/components/Layout.vue'
import Dashboard from '@/views/Dashboard.vue'
import DaftarBarang from '@/views/DaftarBarang.vue'
import KategoriBarang from '@/views/KategoriBarang.vue'
import LokasiBarang from '@/views/LokasiBarang.vue'
import MerkBarang from '@/views/MerkBarang.vue'
import Peminjaman from '@/views/Peminjaman.vue'
import StatusBarang from '@/views/StatusBarang.vue'
import ServiceBarang from '@/views/ServiceBarang.vue'
import GudangImamA from '@/views/GudangImamA.vue'
import GudangImamB from '@/views/GudangImamB.vue'
import GudangTanggaA from '@/views/GudangTanggaA.vue'
import GudangTanggaB from '@/views/GudangTanggaB.vue'
import Sekretariat from '@/views/sekretariat.vue'
import Lantai2 from '@/views/Lantai2.vue'
import User from '@/views/User.vue'

const routes = [
  {
    path: '/',
    name: 'Login',
    component: LoginPage
  },
  {
    path: '/dashboard',
    component: Layout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
        name: 'Dashboard',
        component: Dashboard,
        meta: { requiresAuth: true }
      },
      {
        path: 'daftar-barang',
        name: 'DaftarBarang',
        component: DaftarBarang,
        meta: { requiresAuth: true }
      },
      {
        path: 'kategori-barang',
        name: 'KategoriBarang',
        component: KategoriBarang,
        meta: { requiresAuth: true }
      },
      {
        path: 'lokasi-barang',
        name: 'LokasiBarang', 
        component: LokasiBarang,
        meta: { requiresAuth: true }
      },
      {
        path: 'merk-barang',
        name: 'MerkBarang',
        component: MerkBarang,
        meta: { requiresAuth: true }
      },
      {
        path: 'peminjaman',
        name: 'Peminjaman',
        component: Peminjaman,
        meta: { requiresAuth: true }
      },
      {
        path: 'status-barang',
        name: 'StatusBarang',
        component: StatusBarang,
        meta: { requiresAuth: true }
      },
      {
        path: 'service-barang',
        name: 'Service-Barang',
        component: ServiceBarang,
        meta: { requiresAuth: true }
      },
      {
        path: 'gudang-imam-a',
        name: 'Gudang-Imam-A',
        component: GudangImamA,
        meta: { requiresAuth: true }
      },
      {
        path: 'gudang-imam-b',
        name: 'Gudang-Imam-B',
        component: GudangImamB,
        meta: { requiresAuth: true }
      },
      {
        path: 'gudang-tangga-a',
        name: 'Gudang-Tangga-A',
        component: GudangTanggaA,
        meta: { requiresAuth: true }
      },
      {
        path: 'gudang-tangga-b',
        name: 'Gudang-Tangga-B',
        component: GudangTanggaB,
        meta: { requiresAuth: true }
      },
      {
        path: 'sekretariat',
        name: 'Sekretariat',
        component: Sekretariat,
        meta: { requiresAuth: true }
      },
      {
        path: 'gudang-lantai-2',
        name: 'Gudang-Lantai-2',
        component: Lantai2,
        meta: { requiresAuth: true }
      },
      {
        path: 'user',
        name: 'User',
        component: User,
        meta: { requiresAuth: true }
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// ========================
//  Route Guard Tambahan
// ========================
router.beforeEach((to, from, next) => {
  const user = JSON.parse(localStorage.getItem('user'))

  if (to.meta.requiresAuth && !user) {
    next({ name: 'Login' })
  } else if (to.name === 'Login' && user) {
    next({ name: 'Dashboard' })
  } else {
    next()
  }
})

export default router
