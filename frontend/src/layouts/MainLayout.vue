<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-white">
      <q-toolbar class="text-black q-pa-md">
        <!-- Logo -->
        <q-avatar size="xl">
          <img src="/imgs/wuplogo.jpg">
        </q-avatar> 
        <span class="text-h5 text-bold q-ml-md q-mr-md"> Wesleyan University-Philippines  </span>
        <img src="/imgs/mhalogo.png" style="width: 2.5%;">
        <q-space />
        <!-- contact sections -->

        <q-list
          v-for="(item, index) in contacts"
          :key="index"
        >
          <q-item>
            <q-item-section top avatar>
              <q-icon :name="item.icon" :color="item.iconColor" size="md" />
            </q-item-section>

            <q-item-section>
              <q-item-label class="text-bold">{{item.title}}</q-item-label>
              <q-item-label caption lines="2">{{item.value}}</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-toolbar>
      <q-toolbar class="bg-green-10" inset>
        <q-tabs v-model="menuSelected">
          <q-route-tab to="/" name="home" label="Home" />
          <q-route-tab to="about" name="about" label="About Us" />
          <q-tab name="courses" label="Courses" />
          <q-tab name="teacher" label="Teachers" />
        </q-tabs>
        <q-space />

        <q-btn 
          flat
          to="admin"
          color="white"
          label="Login" 
        />
      </q-toolbar>
    </q-header>



    <q-page-container class="customBG text-white">
      <router-view />
    </q-page-container>
  </q-layout>
</template>
<style scoped>
.customBG{
  background: rgb(227,0,0);
  background: linear-gradient(207deg, rgba(227,0,0,1) 0%, rgba(105,191,54,1) 100%);
}
</style>
<script>
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { defineComponent, ref } from 'vue'

export default defineComponent({
  name: 'MainLayout',
  data(){
    return {
      menuSelected: 'home',
      contacts: [
        {
          icon: 'contact_phone',
          iconColor: 'orange',
          title: 'Call',
          value: '(044) 463 2162',
        },
        {
          icon: 'schedule',
          iconColor: 'blue',
          title: 'Working Time',
          value: 'Mon - Fri 8:00 AM - 7:00 PM',
        },
        {
          icon: 'location_on',
          iconColor: 'red',
          title: 'Address',
          value: 'Mabini Extension, Cabanatuan City, Philippines',
        },
      ]
    }
  },
  created(){
    let profile = SessionStorage.getItem('userDataLogin');
    
    if(profile || profile !== null){
      this.$router.push('/admin/dashboard')
    } else {
      this.$router.push('/')
    }
  }
})
</script>
