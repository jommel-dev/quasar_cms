<template>
  <q-page class="">
    
    <div class="row q-mb-xl" style="height: 60vh;">
      <div class="col col-md-12"></div>
      <div class="col col-md-6"></div>
      <div class="col col-xs-12 col-sm-12 col-md-4 flex items-center" style="z-index:999;">
        <span class="text-h2 text-bold">The Smarter Way to Learn <span class="text-orange">Anything</span></span>
        <span class="text-subtitle1">Mauris malesuada enim eget blandit gravida. Duis hendrerit cursus turpis, id mollis est rutrum nec. Sed interdum nisi id libero tincidunt, sit amet vestibulum tortor porttitor. Cras ligula lacus, ullamcorper sed</span>
      
        <q-input rounded outlined stack-label v-model="text" placeholder="Search" style="width: 100%;"/>
      </div>
      <div class="col col-md-2"></div>
      <div class="backgroundHeader"></div>
    </div>

    <div class="row q-mt-xl">
      <div class="col col-xs-12 col-sm-12 col-md-12 text-center">
        <span class="text-h4 text-bold">Welcome to <span class="text-orange">MHA</span></span>
      </div>
      <div class="col col-xs-12 col-sm-12 col-md-2 flex items-center"></div>
      <div class="col col-xs-12 col-sm-12 col-md-8">
        <div class="row">
          <div
            v-for="(item, index) in programs"
            :key="index"
            class="col col-xs-12 col-sm-12 col-md-3 flex items-center q-pa-md"
          >
            <q-list bordered class="shadow-6" >
              <q-item active class="q-pa-md bg-white">
                <q-item-section top avatar>
                  <q-icon :name="item.icon" :color="item.iconColor" size="md" />
                </q-item-section>

                <q-item-section>
                  <q-item-label class="text-bold">{{item.title}}</q-item-label>
                  <q-item-label caption lines="2">{{item.value}}</q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>
      </div>
      <div class="col col-md-2 flex items-center"></div>
    </div>
    <div class="row q-mt-lg">
      <div class="col col-md-2 flex items-center"></div>
      <div class="col col-xs-12 col-sm-12 col-md-8">
        <q-carousel
          swipeable
          animated
          v-model="slide"
          thumbnails
          infinite
        >
          <q-carousel-slide :name="1" img-src="https://cdn.quasar.dev/img/mountains.jpg" />
          <q-carousel-slide :name="2" img-src="https://cdn.quasar.dev/img/parallax1.jpg" />
          <q-carousel-slide :name="3" img-src="https://cdn.quasar.dev/img/parallax2.jpg" />
          <q-carousel-slide :name="4" img-src="https://cdn.quasar.dev/img/quasar.jpg" />
        </q-carousel>
      </div>
      <div class="col col-md-2 flex items-center"></div>
    </div>

    <div class="row q-mt-xl">
      <div class="col col-md-2 flex items-center"></div>
      <div class="col col-xs-12 col-sm-12 col-md-8">
        <div class="row">
          <div class="col col-xs-12 col-sm-12 col-md-12 q-mb-lg flex">
            <span class="text-h4 text-bold">Announcements</span><q-space />
            <q-btn 
              flat
              color="white"
              label="More"
              icon-right="arrow_right_alt"
            />
          </div>
          <div
            v-for="(item, index) in announcements"
            :key="index"
            class="col col-xs-12 col-sm-12 col-md-4 q-pa-lg"
          >
            <q-card class="my-card" flat bordered>
              <q-img :src="item.image" />

              <q-card-section>
                <q-chip 
                  color="primary" 
                  text-color="white" 
                  :icon="item.subject.icon"
                  class="absolute"
                  style="top: 0; right: 12px; transform: translateY(-50%);"
                >
                  {{item.subject.label}}
                </q-chip>
                <div class="row no-wrap items-center">
                  <div class="col text-h6 text-black ellipsis">
                    {{item.title}}
                  </div>
                </div>
              </q-card-section>

              <q-card-section class="q-pt-none">
                <div class="text-subtitle1 text-black ellipsis" v-html="item.content"></div>
                <div class="text-caption text-grey">
                  {{`By ${item.createdBy}`}}・{{item.tag.join(', ')}}
                </div>
                <div class="col-auto text-grey text-caption q-pt-md row no-wrap items-center">
                  <q-icon name="event" /> {{item.createdDate}}
                </div>
              </q-card-section>

              <q-card-actions>
                <q-btn style="width: 100px;" outline rounded color="primary" label="Read" size="sm" />
              </q-card-actions>
            </q-card>
          </div>
        </div>
      </div>
      <div class="col col-md-2 flex items-center"></div>
    </div>

    <!-- Find Course -->
    <div class="row q-mb-xl" style="height: 60vh;">
      <div class="col col-md-2"></div>
      <div class="col col-xs-12 col-sm-12 col-md-4 flex items-center" style="z-index:999;">
        <span class="text-h2 text-bold">Find your <span class="text-orange">Course!</span></span>
        <span class="text-subtitle1">
          Mauris malesuada enim eget blandit gravida. Duis hendrerit cursus turpis, id mollis est rutrum nec. Sed interdum nisi id libero tincidunt, sit amet vestibulum tortor porttitor. Cras ligula lacus, ullamcorper sed
        </span>
        <q-list>
          <q-item>
            <q-item-section top avatar>
              <q-icon name="contact_phone" color="orange" size="xl" />
            </q-item-section>
            <q-item-section>
              <q-item-label class="text-h5 text-bold">(0929) 1234 678</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>

        <q-input rounded outlined sta ck-label v-model="text" placeholder="Search for course" style="width: 100%;"/>
      </div>
      <div class="col col-md-6">
        <div class="backgroundMid" ></div>
      </div>
      
    </div>

    <!-- Contact Us -->
    
    <!-- Footer -->
    <div class="row footerBackground bg-green-10 text-white" style="z-index:999;">
      <div class="col col-md-2"></div>
      <div class="col col-xs-12 col-sm-12 col-md-8">
        footer
      </div>
      <div class="col col-md-2"></div>
    </div>
  </q-page>
</template>

<script>
import { defineComponent } from 'vue';

export default defineComponent({
  name: 'PageIndex',
  components: {},
  data(){
    return {
      text: '',
      slide: 1,
      programs: [
        {
          icon: 'badge',
          iconColor: 'orange',
          title: 'Awesome Teachers',
          value: 'Vivamus interdum, mauris interdum quis curdum sodales',
        },
        {
          icon: 'verified',
          iconColor: 'red',
          title: 'Certificate',
          value: 'Etiam risus neque, volutpat vel laoreet a, finibus volutpat non',
        },
        {
          icon: 'square_foot',
          iconColor: 'green',
          title: 'Best Programm',
          value: 'Etiam risus neque, volutpat vel laoreet a, finibus volutpat non',
        },
        {
          icon: 'support_agent',
          iconColor: 'orange',
          title: 'Student Support',
          value: 'Etiam risus neque, volutpat vel laoreet a, finibus volutpat non',
        },
      ],
      announcements: [
        {
          image: '/imgs/cardHeading.png',
          subject: {
            icon: 'edit_calendar',
            label: 'Enrollment'
          },
          createdDate: '12/16/2023',
          createdBy: 'Admin',
          tag: ['student', 'school'],
          title: 'Second Semester AY. 2023-2024',
          content: 'Resumption of work at the university <strong>January 3, 2024</strong>',
        },
        {
          image: '/imgs/cardHeading.png',
          subject: {
            icon: 'notification_important',
            label: 'Notice'
          },
          createdDate: '12/16/2023',
          createdBy: 'Admin',
          tag: ['student', 'school'],
          title: 'Semester Ends',
          content: 'End of semester AY. 2023-2024 <strong>December 21, 2023</strong>',
        },
        {
          image: '/imgs/cardHeading.png',
          subject: {
            icon: 'campaign',
            label: 'Announcement'
          },
          createdDate: '12/16/2023',
          createdBy: 'Admin',
          tag: ['student', 'school'],
          title: 'Semester Start',
          content: 'Opening of classes for second semester AY. 2023-2024 <strong>January 15, 2024</strong>',
        },
      ],
      classes: [
        {
          image: 'https://cdn.quasar.dev/img/chicken-salad.jpg',
          subject: {
            icon: 'notification_important',
            label: 'Programming'
          },
          createdDate: '12/16/2023',
          createdBy: 'Admin',
          tag: ['student', 'school'],
          title: 'Programming Seminar',
          content: 'Nam mattis felis id sodales rutrum. Nulla ornare tristique mauris, a laoreet erat ornare sit amet',
        },
        {
          image: 'https://cdn.quasar.dev/img/chicken-salad.jpg',
          subject: {
            icon: 'code',
            label: 'Programming'
          },
          createdDate: '12/16/2023',
          createdBy: 'Admin',
          tag: ['student', 'school'],
          title: 'Programming Seminar',
          content: 'Nam mattis felis id sodales rutrum. Nulla ornare tristique mauris, a laoreet erat ornare sit amet',
        },
        {
          image: 'https://cdn.quasar.dev/img/chicken-salad.jpg',
          subject: {
            icon: 'code',
            label: 'Programming'
          },
          createdDate: '12/16/2023',
          createdBy: 'Admin',
          tag: ['student', 'school'],
          title: 'Programming Seminar',
          content: 'Nam mattis felis id sodales rutrum. Nulla ornare tristique mauris, a laoreet erat ornare sit amet',
        },
      ],
    }
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
