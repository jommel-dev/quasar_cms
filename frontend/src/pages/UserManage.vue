<template>
  <q-page>
    <UserList />
  </q-page>
</template>

<script>
import UserList from '../components/Users/Index.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'UserManagement',
  components: {
    UserList
  },
  data(){
    return {
      sample: "Here it is"
    }
  },
  methods: {
    
  }
})
</script>
