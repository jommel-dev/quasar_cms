<template>
  <div>
    <q-list class="rounded-borders">
      <q-item
        clickable
        @click="showCrumbs(crumbs)"
        tag="a"
        v-if="checkModule(code) && children.length === 0"
        :to="{name: link}"
      > 
        <q-item-section
          v-if="icon"
          avatar
        >
          <q-icon :name="icon" />
        </q-item-section>

        <q-item-section>
          <q-item-label>{{ title }}</q-item-label>
        </q-item-section>
      </q-item>

      <q-expansion-item
        clickable
        v-if="checkModule(code) && children.length !== 0"
        expand-separator
        :icon="icon"
        :label="title"
        :default-opened="checkSubchildOpen(children)"
      >
        <q-item
          class="q-ml-xl"
          :header-inset-level="1"
          v-for="(item, index) in children"
          :key="index"
          :to="{name: item.link}"
          clickable 
          v-close-popup
        >
          <q-item-section
            v-if="item.icon"
            avatar
          >
            <q-icon :name="item.icon" />
          </q-item-section>
          <q-item-section>
            {{ item.title }}
          </q-item-section>
          <q-separator />
        </q-item>
      </q-expansion-item>
    </q-list>
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { defineComponent } from 'vue'

export default defineComponent({
    name: 'SideNavigation',
    props: {
        title: {
            type: String,
            required: true
        },

        link: {
            type: String,
            default: '#'
        },

        icon: {
            type: String,
            default: ''
        },

        code: {
          type: Number
        },

        crumbs: {
            type: Array,
            default: [{title: '', icon: 'home', link: '/'}]
        },

        children: {
          type: Array,
          default: []
        }
    },
    computed: {
      user: function(){
        let profile = LocalStorage.getItem('userData');
        return jwt_decode(profile);
      }
    },
    methods:{
      checkModule(id) {
        return this.user.modules.includes(id) ? true : false;
      },
      checkSubchildOpen(childs){
        // Get Current route open
        let currRoute = this.$router.currentRoute._value.name;
        let filterChild = childs.filter((el) => {
          return el.link === currRoute
        })
        return filterChild.length === 0 ? false : true;
      },
      async showCrumbs(value){
          this.$emit('linkCrumbs', value);
      }
    }
})
</script>
