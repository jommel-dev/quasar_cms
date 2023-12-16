<template>
  <q-page>
    <FillForm />
  </q-page>
</template>

<script>
import FillForm from '../components/Philhealth/Index.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'OperationReportForm',
  components: {
    FillForm
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
