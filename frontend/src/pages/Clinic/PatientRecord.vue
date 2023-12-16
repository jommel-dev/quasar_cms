<template>
  <ListContainer />
</template>

<script>
import ListContainer from '../../components/Clinic/Records/List.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ClinicManagementRecords',
  components: {
    ListContainer
  }
})
</script>
