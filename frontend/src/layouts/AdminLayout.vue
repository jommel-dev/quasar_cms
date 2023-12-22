<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-white">
      <q-toolbar class="text-black q-pa-md">
        <!-- Logo -->
        <q-avatar size="xl">
          <img src="/imgs/wuplogo.jpg">
        </q-avatar> 
        <span class="text-h5 text-bold q-ml-md"> Wesleyan University-Philippines  </span>
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
        
      </q-toolbar>
    </q-header>

    <q-page-container class="bg-red-10 text-white">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { defineComponent, ref } from 'vue'

export default defineComponent({
  name: 'AdminLayout',
  data(){
    return {
      menuSelected: 'home',
      
    }
  },
  created(){
    let profile = SessionStorage.getItem('userDataLogin');
    
    if(profile || profile !== null){
      this.$router.push('/dashboard')
    } else {
      this.$router.push('/admin')
    }
  }
})
</script>
