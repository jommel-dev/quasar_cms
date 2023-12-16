<template>
  <div class="text-center">
    <Dashboard />
  </div>
</template>

<script>
import { defineComponent } from 'vue';
import Dashboard from '../components/Dashboard/Index';

export default defineComponent({
  name: 'PageDashboard',
  components: {
    Dashboard
  },
})
</script>
