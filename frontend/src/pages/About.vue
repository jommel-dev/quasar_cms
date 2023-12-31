<template>
  <q-page class="">
    
    <div class="row q-mb-xl" style="height: 60vh;">
      <div class="col col-md-12"></div>
      <div class="col col-md-6"></div>
      <div class="col col-xs-12 col-sm-12 col-md-4 flex items-center" style="z-index:999;">
        <span class="text-h2 text-bold">MASTER IN <span class="text-orange">HOSPITAL ADMINISTRATION</span></span>
        <span class="text-subtitle1">
            The Master in Hospital Administration (MHA) is a 36-unit graduate degree program that equips graduates
            to be effective administrators of hospitals or health care facilities. The program covers topics such as
            financial management, legal and ethical issues of health services, information management, economics and
            policy, strategic marketing, hospital planning and design, and hospital administration. Graduates shall be
            well-equipped to demonstrate managerial and leadership competencies, and exhibit critical thinking, and
            ethical sensitivity in decision-making in the various areas of responsibilities in hospital administration.
        </span>
      
        <!-- <q-input rounded outlined stack-label v-model="text" placeholder="Search" style="width: 100%;"/> -->
      </div>
      <div class="col col-md-2"></div>
      <div class="backgroundHeader"></div>
    </div>

    <div class="row q-mt-xl">
      <div class="col col-xs-12 col-sm-12 col-md-12 text-center">
        <span class="text-h4 text-bold">Contact Details</span>
      </div>
      <div class="col col-xs-12 col-sm-12 col-md-2 flex items-center"></div>
      <div class="col col-xs-12 col-sm-12 col-md-8">
        <div class="row">
          <div
            v-for="(item, index) in programs"
            :key="index"
            class="col col-xs-12 col-sm-12 col-md-4 flex items-center q-pa-md"
          >
            <q-list bordered class="shadow-6" style="width: 100%;">
              <q-item active class="q-pa-md bg-white">
                <q-item-section top avatar>
                  <q-icon :name="item.icon" :color="item.iconColor" size="md" />
                </q-item-section>

                <q-item-section>
                  <q-item-label class="text-bold"><a :href="`mailto: ${item.value}`" style="text-decoration:none;" color="primary" >{{item.value}}</a></q-item-label>
                  <q-item-label caption lines="2">{{item.title}}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>
      </div>
      <div class="col col-md-2 flex items-center"></div>
    </div>


    <!-- Contact Us -->
    
    <!-- Footer -->
    <!-- <div class="row footerBackground bg-green-10 text-white" style="z-index:999;">
      <div class="col col-md-2"></div>
      <div class="col col-xs-12 col-sm-12 col-md-8">
        footer
      </div>
      <div class="col col-md-2"></div>
    </div> -->
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';
import { api } from 'boot/axios'

export default defineComponent({
  name: 'AboutPage',
  components: {},
  data(){
    return {
      text: '',
      slide: 1,
      programs: [
        {
          icon: 'alternate_email',
          iconColor: 'red',
          title: 'SOLAS Assistant',
          value: 'assistant.solas@wesleyan.edu.ph',
        },
        {
          icon: 'alternate_email',
          iconColor: 'red',
          title: 'SOLAS Secretary',
          value: 'secretary.solas@wesleyan.edu.ph',
        },
        {
          icon: 'alternate_email',
          iconColor: 'red',
          title: 'SOLAS Dean',
          value: 'dean.solas@wesleyan.edu.ph',
        },
      ],
      announcements: [],
      programList: [],
    }
  },
  created(){
    this.getAnnouncements();
    this.getProgramList();
  },
  methods:{
    async getAnnouncements(){
      let vm = this;
      
      api.get('announcement/public/getList').then((response)=>{
          let data = {...response.data}

          if(!data.error){
            this.announcements = response.status < 300 ? data.list : [];
          } else {
              this.$q.notify({
                  color: 'negative',
                  position: 'top-right',
                  title:data.title,
                  message: this.$t(`errors.${data.error}`),
                  icon: 'report_problem'
              })
          }
      });
    },
    async getProgramList(){
      let vm = this;
      
      api.get('program/public/getList').then((response)=>{
          let data = {...response.data}

          if(!data.error){
            this.programList = response.status < 300 ? data.list : [];
          } else {
              this.$q.notify({
                  color: 'negative',
                  position: 'top-right',
                  title:data.title,
                  message: this.$t(`errors.${data.error}`),
                  icon: 'report_problem'
              })
          }
      });
    },
  }
})
</script>

<style scoped>
.backgroundHeader{
  position: absolute;
  background: url('/imgs/background.svg') no-repeat;
  height: 80vh;
  width: 100%;
  background-position: 50% 60%;
  background-size: 60%;
  opacity: 0.2;
}
.backgroundMid{
  position: absolute;
  background: url('/imgs/background2.svg') no-repeat;
  height: 60vh;
  width: 34%;
  background-position: -25% 66%;
  /* background-size: 60%;
  opacity: 0.2; */
}
.footerBackground{
  height: 30vh;
}
</style>
