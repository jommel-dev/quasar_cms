
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

  </q-layout>
</template>

<script>
import { SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import SideNav from '../components/Templates/Sidenav.vue';
import Profile from '../components/Templates/Profile.vue';
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
    title: 'Create Announcement',
    icon: 'campaign',
    link: 'announcement',
    code: 102,
    crumbs: [
      {label: '', icon: 'home', link: '/'},
      {label: 'OR Forms', icon: 'history_edu', link: 'forms'}
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
    Profile
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
      this.$router.push('/admin')
    }
  }
}
</script>