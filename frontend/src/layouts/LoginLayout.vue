
<template>
  <q-layout view="lHh LpR lFf">

    <q-header reveal bordered class="bg-deep-purple-12 ">
      <q-toolbar>
        <q-btn dense flat round icon="menu" @click="toggleLeftDrawer" />
        <q-space />
        <q-btn class="q-mr-sm" round dense flat icon="notifications">
            <q-badge floating color="red" rounded transparent>
                3
            </q-badge>
        </q-btn>
      </q-toolbar>
      <!-- <q-bar dense>
        <Crumbs :contentLink.sync="menuCrumbs" />
      </q-bar> -->
    </q-header>
    

    <q-drawer 
      show-if-above 
      v-model="leftDrawerOpen" 
      side="left" 
      bordered
    >
      <!-- drawer content -->
      <Profile v-bind="userProfile" />
      <q-separator dark />
      <SideNav 
        v-for="link in filteredMenus"
        :key="link.title"
        v-bind="link"
        @linkCrumbs="setCrumbsItem"
      />
    </q-drawer>

    <q-page-container>
      <q-page class="q-pa-sm">
        <div style="height: 88vh;">
          <router-view />
        </div>
      </q-page>
    </q-page-container>

    <!-- <q-footer reveal class="bg-grey-5 text-weight-thin text-blue-white-9 text-center q-pt-lg q-pb-lg">
      <div>{{ `Centralize Distribution and Sales Inventory System ©2023 Created by FWDS` }}</div>
    </q-footer> -->

  </q-layout>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import SideNav from '../components/Templates/Sidenav.vue';
import Profile from '../components/Templates/Profile.vue';
import Crumbs from '../components/Templates/Breadcrumbs.vue';

const linksList = [
  {
    title: 'Dashboard',
    icon: 'dashboard',
    link: 'dashboard',
    code: 101,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
    ]
  },
  {
    title: 'Create Invoice',
    icon: 'receipt',
    link: 'salesForm',
    code: 101,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'OR Forms', icon: 'history_edu', link: 'forms'}
    ]
  },
  {
    title: 'Patient Management',
    icon: 'pets',
    code: 104,
    children: [
      {
        title: 'Patient Records',
        icon: 'source',
        link: 'patientRecords',
      },
    ],
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Patient List', icon: 'view_list', link: 'mylist'}
    ]
  },
  {
    title: 'Inventory Management',
    icon: 'inventory',
    code: 104,
    children: [
      {
        title: 'Stock List',
        icon: 'view_list',
        link: 'stockList',
      },
      {
        title: 'Product List',
        icon: 'sell',
        link: 'productList',
      },
      {
        title: 'Invoice',
        icon: 'point_of_sale',
        link: 'invoiceList',
      },
    ],
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Patient List', icon: 'view_list', link: 'mylist'}
    ]
  },
  {
    title: 'Mobile App Management',
    icon: 'devices',
    code: 104,
    children: [
      {
        title: 'Agent Call Sync',
        icon: 'contact_phone',
        link: 'agentCallSync',
      },
      {
        title: 'Client List',
        icon: 'storefront',
        link: 'clientList',
      },
    ],
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Patient List', icon: 'view_list', link: 'mylist'}
    ]
  },
  {
    title: 'Reports',
    icon: 'analytics',
    code: 104,
    children: [
      {
        title: 'Agent Call Report',
        icon: 'contact_phone',
        link: 'mobileSync',
      },
      {
        title: 'Summary Report',
        icon: 'contact_phone',
        link: 'mobileSync',
      },
      {
        title: 'Sales Report',
        icon: 'storefront',
        link: 'salesReport',
      },
    ],
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Patient List', icon: 'view_list', link: 'mylist'}
    ]
  },
  {
    title: 'Manage Users',
    icon: 'manage_accounts',
    link: 'usermanagement',
    code: 106,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Manage Users', icon: 'manage_accounts', link: 'usermanagement'}
    ]
  },
  {
    title: 'Configurations',
    icon: 'display_settings',
    link: 'crsmanagement',
    code: 107,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'Manage CRS', icon: 'dynamic_form', link: 'crsmanagement'}
    ]
  },
];

export default {
  data(){
    return {
      linksList,
      userProfile: null,
      menuCrumbs: [
        {label: '', icon: 'home', link: '/'},
        {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
      ],
      leftDrawerOpen: true,
      miniState: false
    }
  },
  mounted(){},
  components:{
    SideNav,
    Profile,
    Crumbs
  },
  computed: {
    filteredMenus: function(){
      return this.linksList;
    }
  },
  created(){
    // SessionStorage.set('userDataLogin', data.jwt);
    let profile = SessionStorage.getItem('userDataLogin');

    if(profile){
      this.userProfile = jwt_decode(profile);
    } else {
      this.$router.push('/')
    }
    
  },
  methods: {
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    setCrumbsItem(val){
      this.menuCrumbs = val;
    },
    logout(){
      localStorage.removeItem('userData');
      SessionStorage.remove('userDataLogin');
      this.$router.push('/')
    }
  }
}
</script>