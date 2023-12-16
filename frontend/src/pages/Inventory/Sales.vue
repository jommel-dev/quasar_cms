<template>
  <FormContainer />
</template>

<script>
import FormContainer from '../../components/Inventory/Sales/Form.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'SalesProducts',
  components: {
    FormContainer
  }
})
</script>
