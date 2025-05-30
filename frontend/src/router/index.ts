import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '@/views/Dashboard.vue';
import Login from '@/views/Login.vue';
import Register from '@/views/Register.vue';
import ReceiptFiling from '@/views/ReceiptFiling.vue';
import Expenses from '@/views/Expenses.vue';
import IncomeSummaryReport from '@/views/IncomeSummaryReport.vue';
import QuotationInvoice from '@/views/QuotationInvoice.vue';
import IncomeManagement from '@/views/IncomeManagement.vue';

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true }
  },
  {
    path: '/receipts',
    name: 'ReceiptFiling',
    component: ReceiptFiling,
    meta: { requiresAuth: true }
  },
  {
    path: '/expenses',
    name: 'Expenses',
    component: Expenses,
    meta: { requiresAuth: true }
  },
  {
    path: '/quotation-invoice',
    name: 'QuotationInvoice',
    component: QuotationInvoice
  },
  {
    path: '/reports/income-summary',
    name: 'IncomeSummaryReport',
    component: IncomeSummaryReport,
    meta: {
      requiresAuth: true,
      title: 'Income Summary Report'
    }
  },
  {
    path: '/income',
    name: 'IncomeManagement',
    component: IncomeManagement,
    meta: { 
      requiresAuth: true,
      title: 'Income Management'
    }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = !!localStorage.getItem('token');
  
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!isAuthenticated) {
      next({ name: 'Login' });
    } else {
      next();
    }
  } else if (to.matched.some(record => record.meta.guest)) {
    if (isAuthenticated) {
      next({ name: 'Dashboard' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
