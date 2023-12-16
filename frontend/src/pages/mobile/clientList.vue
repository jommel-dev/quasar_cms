<template>
  <ListContainer />
</template>

<script>
import ListContainer from '../../components/Mobile/ClientsList.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'ClientsListContainer',
  components: {
    ListContainer
  }
})
</script>
