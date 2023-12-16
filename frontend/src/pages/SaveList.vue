<template>
  <q-page>
    <SaveList />
  </q-page>
</template>

<script>
import SaveList from '../components/Patients/SaveList.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'OperationReportForm',
  components: {
    SaveList
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
