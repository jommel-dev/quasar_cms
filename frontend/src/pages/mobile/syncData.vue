<template>
  <ListContainer />
</template>

<script>
import ListContainer from '../../components/Mobile/MobileSyncList.vue';
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'MobileSyncListContainer',
  components: {
    ListContainer
  }
})
</script>
