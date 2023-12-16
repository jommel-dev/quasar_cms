<template>
  <ListContainer />
</template>

<script>
import ListContainer from '../../components/Inventory/Stock/List.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'InventoryManagementStocks',
  components: {
    ListContainer
  }
})
</script>
