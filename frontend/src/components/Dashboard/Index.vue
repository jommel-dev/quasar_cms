<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Cards -->
            <div class="col col-md-6 q-pa-sm">
                <FullCalendar 
                    :options="calendarOptions"
                >
                    <!-- <template v-slot:eventContent='arg'>
                        <b>{{ arg.event.title }}</b>
                    </template> -->
                </FullCalendar>
            </div>
            <div class="col col-md-3 q-pa-sm"> 
                Booking / Schedule Details
            </div>
            <div class="col col-md-3 q-pa-sm">
                <q-card class="my-card q-mb-sm" flat bordered>
                    <q-item>
                        <q-item-section>
                        <q-item-label>DVS Vet Clinic</q-item-label>
                        <q-item-label caption>
                            Subhead
                        </q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator />

                    <q-card-section horizontal class="row">
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Today's Sales
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Clients
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Patients
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Overall Sales
                            </div>
                        </q-card-section>
                    </q-card-section>

                    <q-separator />

                    <q-card-actions>
                        <q-btn flat round icon="event" />
                        <q-btn flat>
                        7:30PM
                        </q-btn>
                        <q-btn flat color="primary">
                        Reserve
                        </q-btn>
                    </q-card-actions>
                </q-card>
                <q-space />
                <q-card class="my-card q-mb-sm" flat bordered>
                    <q-item>
                        <q-item-section>
                        <q-item-label>DVS Distribution</q-item-label>
                        <q-item-label caption>
                            Subhead
                        </q-item-label>
                        </q-item-section>
                    </q-item>

                    <q-separator />

                    <q-card-section horizontal class="row">
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Today's Sales
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Clients
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Products
                            </div>
                        </q-card-section>
                        <q-card-section class="q-pt-xs col">
                            <div class="text-h6 q-mt-sm q-mb-xs">0</div>
                            <div class="text-caption text-grey">
                                Overall Sales
                            </div>
                        </q-card-section>
                    </q-card-section>

                    <q-separator />

                    <q-card-actions>
                        <q-btn flat round icon="event" />
                        <q-btn flat>
                        7:30PM
                        </q-btn>
                        <q-btn flat color="primary">
                        Reserve
                        </q-btn>
                    </q-card-actions>
                </q-card>
                <q-space />
                <q-card class="my-card q-mb-sm" flat bordered>

                    <q-card-section horizontal class="row">
                        <q-card-section class="col">
                            <q-list >
                                <q-item v-for="(item, index) in activities" tag="label" :key="index" v-ripple>
                                    <q-item-section side top>
                                        <q-icon v-if="!item.active" name="warning" color="orange" />
                                        <q-icon v-else name="check_circle" color="green" />
                                    </q-item-section>

                                    <q-item-section>
                                        <q-item-label>{{item.title}}</q-item-label>
                                        <q-item-label caption>
                                            {{item.caption}}
                                        </q-item-label>
                                    </q-item-section>
                                </q-item>
                            </q-list>
                        </q-card-section>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </div>
</template>

<script>
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'

export default{
    name: 'CardWidgets',
    components: {
        FullCalendar
    },
    data(){
        return {
            calendarOptions: {
                plugins: [ dayGridPlugin, interactionPlugin ],
                dayMaxEvents: true,
                initialView: 'dayGridMonth',
                // Date Action Handler
                dateClick: (args) => { return this.handleDateClick(args.event) },
                selectable: true,

                events: [],
                eventContent: 'Show Details',
                // eventClick: this.eventClick,


            },
            activities: [
                {
                    active: false,
                    title: "Client Visit",
                    caption: "Check and Inspect for the client",
                    action: () => { return false }
                },
                {
                    active: false,
                    title: "Booking",
                    caption: "Proceeds with client order transactions",
                    action: (val) => { return this.openBookingDetails(val) }
                },
                {
                    active: false,
                    title: "Onsite Activity",
                    caption: "Vaccines, Inspections, Etc.",
                    action: () => { return false }
                },
            ],
            eventList: []
        }
    },
    mounted(){
        this.getSchedules()
    },
    methods: {
        eventClick(val){
            console.log(val)
        },
        handleDateClick(val){
            console.log(val)
            console.log(val.dayEl)
        },
        getSchedules(){
            this.calendarOptions.events = [];
            this.$q.loading.show();
            api.get('dashboard/getScheduleList').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.calendarOptions.events = response.status < 300 ? data.list : [];
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                }
            })

            this.$q.loading.hide();
        },
        getDashboard(){}
    }
}
</script>
