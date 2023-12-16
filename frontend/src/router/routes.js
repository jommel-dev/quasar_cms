
const routes = [
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Index.vue') }
    ]
  },

  {
    path: '/',
    component: () => import('layouts/LoginLayout.vue'),
    children: [
      { path: 'dashboard', name: 'dashboard', component: () => import('pages/Dashboard.vue') },
      { path: 'forms', name: 'forms', component: () => import('pages/ORForm.vue') },
      { path: 'print', name: 'print', component: () => import('pages/Print.vue') },
      { path: 'mylist', name: 'mylist', component: () => import('pages/PatientList.vue') },
      { path: 'mysavelist', name: 'mysavelist', component: () => import('pages/SaveList.vue') },
      { path: 'usermanagement', name: 'usermanagement', component: () => import('pages/UserManage.vue') },
      { path: 'crsmanagement', name: 'crsmanagement', component: () => import('pages/Philhealth.vue') },
    ]
  },
  
  {
    path: '/clinic-management/',
    component: () => import('layouts/LoginLayout.vue'),
    children: [
      // Clinic Management
      { 
        path: 'patient-records',
        name: 'patientRecords',
        component: () => import('pages/Clinic/PatientRecord.vue') 
      },
    ]
  },
  {
    path: '/inventory-management/',
    component: () => import('layouts/LoginLayout.vue'),
    children: [
      // Inventory Management
      { 
        path: 'stock-list',
        name: 'stockList',
        component: () => import('pages/Inventory/Stock.vue') 
      },
      { 
        path: 'product-list',
        name: 'productList',
        component: () => import('pages/Inventory/Product.vue') 
      },
      { 
        path: 'invoice-list',
        name: 'invoiceList',
        component: () => import('pages/Inventory/Invoice.vue') 
      },
    ]
  },
  {
    path: '/mobile-app-management/',
    component: () => import('layouts/LoginLayout.vue'),
    children: [
      // Inventory Management
      { 
        path: 'agent-call-sync',
        name: 'agentCallSync',
        component: () => import('pages/mobile/syncData.vue') 
      },
      { 
        path: 'client-list',
        name: 'clientList',
        component: () => import('pages/mobile/clientList.vue') 
      },
    ]
  },

  {
    path: '/sales/',
    component: () => import('layouts/LoginLayout.vue'),
    children: [
      // Sales Management
      { 
        path: 'create-transaction',
        name: 'salesForm',
        component: () => import('pages/Inventory/Sales.vue') 
      },
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/Error404.vue')
  }
]

export default routes
