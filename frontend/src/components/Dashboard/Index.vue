<template>
    <div class="q-pa-md" style="width: 100%;">
       Dashboard
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
