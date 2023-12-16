<template>
  <ListContainer />
</template>

<script>
import ListContainer from '../../components/Inventory/Invoice/List.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'InventoryManagementInvoice',
  components: {
    ListContainer
  }
})
</script>
