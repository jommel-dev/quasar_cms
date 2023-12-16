<template>
  <ListContainer />
</template>

<script>
import ListContainer from '../../components/Inventory/Product/List.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'InventoryManagementProducts',
  components: {
    ListContainer
  }
})
</script>
