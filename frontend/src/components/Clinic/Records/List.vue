<template>
  <div class="q-pa-md">
    <span class="text-h4">Records</span>
    <q-table
        title="Patient Records"
        :rows="rows"
        row-key="name"
        selection="multiple"
        :filter="filter"
        grid
        :rows-per-page-options="[20]"
        hide-header
        :loading="isLoading"
    >
        <template v-slot:top-left>
            <q-breadcrumbs 
                class="text-grey" 
                active-color="primary"
            >
                <template v-slot:separator>
                    <q-icon
                        size="1.2em"
                        name="arrow_forward_ios"
                        color="primary"
                    />
                </template>

                <q-breadcrumbs-el
                    v-for="(itms, index) in breadCrumbs"
                    :key="index"
                    :label="itms.label" 
                    :icon="itms.icon"
                    @click="itms.action"
                />
            </q-breadcrumbs>
        </template>
        <template v-slot:top-right>
            <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                <template v-slot:append>
                    <q-icon name="search" />
                </template>
            </q-input>
        </template>

        <template v-slot:item="props">
            <div
                v-if="tableView !== 'patientInfo'"
                class="q-pa-xs col-xs-12 col-sm-6 col-md-3 col-lg-2 itemCursor"
            >
                <q-card 
                    flat 
                    class="itemHover"
                    @click="openFolder(props.row)"
                >
                    <q-card-section v-if="tableView === 'clientList'">
                        <q-icon 
                            name="folder" 
                            size="lg" 
                            color="warning" 
                        /> 
                        {{`${props.row.firstName} ${props.row.lastName}`}}
                    </q-card-section>
                    <q-card-section v-else>
                        <q-icon 
                            name="folder" 
                            size="lg" 
                            color="warning" 
                        /> {{props.row.petName}}
                    </q-card-section>
                    <q-popup-proxy context-menu>
                        <q-menu :model-value="true">
                            <q-list dense  style="min-width: 100px">
                                <q-item 
                                    clickable
                                    @click="openFolder(props.row)" 
                                >
                                    <q-item-section side>
                                        <q-icon name="folder_open" size="xs" />
                                    </q-item-section>
                                    <q-item-section> Open</q-item-section>
                                </q-item>
                                <q-item 
                                    clickable 
                                    v-close-popup
                                >
                                    <q-item-section side>
                                        <q-icon name="help" size="xs" />
                                    </q-item-section>
                                    <q-item-section>Show Details</q-item-section>
                                </q-item>
                                <q-separator />
                                <q-item 
                                    clickable
                                    v-close-popup
                                    @click="openAddPatient(props.row)"
                                >
                                    <q-item-section side>
                                        <q-icon name="pets" size="xs" />
                                    </q-item-section>
                                    <q-item-section>Add Patient</q-item-section>
                                </q-item>
                                <q-separator />
                                <q-item clickable v-close-popup>
                                    <q-item-section side>
                                        <q-icon name="menu_book" size="xs" />
                                    </q-item-section>
                                    <q-item-section>History</q-item-section>
                                </q-item>
                            </q-list>
                        </q-menu>
                    </q-popup-proxy>
                </q-card>
            </div>
        </template>
        <template
            v-if="tableView === 'patientInfo'"
            v-slot:bottom
        >
            <q-splitter>

                <template v-slot:before>
                    <q-tabs
                        v-model="tab"
                        vertical
                        class="text-primary"
                    >
                        <q-tab name="owner" icon="contact_emergency" label="Owner" />
                        <q-tab name="petdetails" icon="pets" label="Pet Details" />
                        <q-tab name="checkup" icon="pending_actions" label="Check Up" />
                        <q-tab name="schedule" icon="event" label="Schedules" />
                        <q-tab name="wellness" icon="vaccines" label="Wellness" />
                    </q-tabs>
                </template>

                <template v-slot:after>
                    <q-tab-panels
                        v-model="tab"
                        animated
                        swipeable
                        vertical
                        transition-prev="jump-up"
                        transition-next="jump-up"
                    >
                        <q-tab-panel name="owner">
                            <div class="text-h4 q-mb-md">Pet Owner Details</div>
                            <!-- <ownerDetails /> -->
                        </q-tab-panel>

                        <q-tab-panel name="petdetails">
                            <div class="text-h4 q-mb-md">Pet Details</div>
                            <!-- <petDetails /> -->
                        </q-tab-panel>

                        <q-tab-panel name="checkup">
                            <div class="text-h4 q-mb-md">Pet Checkups</div>
                            <!-- <checkup /> -->
                        </q-tab-panel>

                        <q-tab-panel name="schedule">
                            <div class="text-h4 q-mb-md">Pet Schedules</div>
                            <!-- <schedule /> -->
                        </q-tab-panel>

                        <q-tab-panel name="wellness">
                            <div class="text-h4 q-mb-md">Pet Wellness</div>
                            <!-- <wellness /> -->
                        </q-tab-panel>
                    </q-tab-panels>
                </template>

            </q-splitter>
        </template>

    </q-table>

    <addPatient
        :appId="appId"
        :modalStatus="openAddPatientModal"
        @updateModalStatus="closePatientModal"
        @refreshData="getList"
    />
  </div>
</template>

<script>
import moment from 'moment';
import addPatient from '../Modals/AddPatient.vue'
// import {
//     ownerDetails,
//     petDetails,
//     schedule,
//     checkup,
//     wellness
// } from '../Widgets/index'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default {
    name: 'RecordList',
    components: {
        addPatient,
        // ownerDetails,
        // petDetails,
        // schedule,
        // checkup,
        // wellness
    },
    data(){
        return {
            // tabs controller
            tab: 'owner',
            // Context Controller
            appId: {},
            openAddPatientModal: false,

            // Others
            isLoading: false,
            filter: '',
            rows: [],
            tableView: 'clientList',
            openId: 0,
            patientInfo: {},
            breadCrumbs: [
                {
                    label: 'Records',
                    icon: 'dvr',
                    classNames: '',
                    action: () => {}
                },
                {
                    label: 'Client List',
                    icon: 'view_list',
                    classNames: 'itemCursor', 
                    action: () => { return this.breadCrumbsClick('clientList', []) }
                },
            ]
        }
    },
    watch:{
        tab(newVal){
            if(newVal){
                this.getTabCallDetails(newVal)
            }
        }
    },
    created(){
        this.getList();
    },
    methods:{
        moment,
        openAddPatient(row){
            this.appId = row
            this.openAddPatientModal = true
        },
        closePatientModal(){
            this.openAddPatientModal = false;
        },
        openFolder(row){
            if(this.tableView === 'clientList'){
                this.appId = row
                this.tableView = 'patientList'
                
                this.getPatientList(row.id);
                this.breadCrumbs.push({
                    label: `${row.firstName} ${row.lastName}`,
                    icon:'folder_open',
                    classNames: 'itemCursor',
                    action: () => { return this.breadCrumbsClick('patientList', row) }
                })
            } else if(this.tableView === 'patientList'){
                this.tableView = 'patientInfo'
                this.openId = row.id;
                this.patientInfo = row
                this.breadCrumbs.push({
                    label: `${row.petName}`,
                    icon:'pets',
                    classNames: '',
                    action: () => {}
                })
            } else {
                this.tableView = 'clientList'
                this.getList();
                this.breadCrumbs = [
                    {
                        label: 'Records',
                        icon: 'dvr',
                        classNames: '',
                        action: () => {}
                    },
                    {
                        label: 'Client List',
                        icon: 'view_list',
                        classNames: 'itemCursor',
                        action: () => { return this.breadCrumbsClick('clientList', []) }
                    },
                ]
            }
        },
        breadCrumbsClick(type, row = []){
            this.breadCrumbs = [
                {
                    label: 'Records',
                    icon: 'dvr',
                    classNames: '',
                    action: () => {}
                },
                {
                    label: 'Client List',
                    icon: 'view_list',
                    classNames: 'itemCursor',
                    action: () => { return this.breadCrumbsClick('clientList', []) }
                },
            ]
            if(type === 'patientList'){
                this.tableView = 'clientList'
                this.openFolder(row)
            } else {
                this.tableView = 'backToList'
                this.openFolder(row)
            }
        },
        async getList(){
            this.rows = [];
            this.isLoading = true;
            
            api.get('client/clientList').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.rows = response.status < 300 ? data.list : [];
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

            this.isLoading = false;
        },
        async getPatientList(id){
            this.rows = [];
            this.isLoading = true;
            let payload = {
                uid: id
            }
            api.post('client/get/patient/list', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.rows = response.status < 300 ? data.list : [];
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

            this.isLoading = false;
        },
        getTabCallDetails(tab){
            let payload = {
                pid: this.openId
            }

            api.post(`patient/get/${tab}`, payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    console.log(data)
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
        }
    }
}
</script>
<style scoped>
.itemCursor{
    cursor: pointer;
}
.itemHover:hover{
    background: aliceblue;
}
</style>