<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-white">
      <q-toolbar class="text-black q-pa-md">
        <!-- Logo -->
        <q-icon name="school" size="xl" class="q-mr-md" /> <span class="text-bold"> TITLE </span>
        <q-space ></q-space>
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
      <q-toolbar class="bg-green" inset>
        <q-tabs v-model="menuSelected">
          <q-tab name="home" label="Home" />
          <q-tab name="about" label="About" />
          <q-tab name="classes" label="Classes" />
          <q-tab name="teacher" label="Teachers" />
          <q-tab name="contact" label="Contact" />
        </q-tabs>
      </q-toolbar>
    </q-header>



    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>

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
          value: '(0987) 654 3214',
        },
        {
          icon: 'schedule',
          iconColor: 'blue',
          title: 'Working Time',
          value: 'Mon - Fri 7:00 AM - 6:00 PM',
        },
        {
          icon: 'location_on',
          iconColor: 'red',
          title: 'Address',
          value: 'Lorem Ipsum Address Sample Test',
        },
      ]
    }
  },
  created(){
    let profile = SessionStorage.getItem('userDataLogin');
    
    if(profile || profile !== null){
      this.$router.push('/dashboard')
    } else {
      this.$router.push('/')
    }
  }
})
</script>
