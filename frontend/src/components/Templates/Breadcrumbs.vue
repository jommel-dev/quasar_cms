<template>
  <q-breadcrumbs>
    <template v-slot:separator>
      <q-icon
        size="1.5em"
        name="chevron_right"
        color="white"
      />
    </template>

    <q-breadcrumbs-el v-for="crumbs in contentLink" :key="crumbs.label" :label="crumbs.label" :icon="crumbs.icon" :to="crumbs.link" />
  </q-breadcrumbs>
</template>

<script>
import { defineComponent } from 'vue'

export default defineComponent({
  name: 'Breadcrumbs',
  props: {
    contentLink: {
      type: Array,
    },
  }
})
</script>
