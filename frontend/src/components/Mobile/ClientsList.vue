<template>
    <div>
        <div v-if="!isContinueEdit" class="q-pa-md" style="width: 100%;">
            <div class="row">
                <div class="col col-md-6">
                    <span class="title">Client List</span>
                </div>
                <div v-if="isLoading" class="col col-md-12 text-center">
                    <q-spinner-orbit
                        color="primary"
                        size="3em"
                    />
                </div>
                <!-- Filter -->
                <!-- <div class="col col-md-12 q-pa-md">
                    <div class="row">
                        <q-input 
                            class="col col-md-2 q-pa-sm"
                            outlined 
                            type="date"
                            v-model="syncDate"
                            label="Sync Date"
                            stack-label
                            dense
                        />
                        <q-select
                            class="col col-md-2 q-pa-sm"
                            outlined 
                            v-model="agentId" 
                            :options="agentList" 
                            label="Agent" 
                            stack-label 
                            dense
                            options-dense
                        />
                        <div class="col col-md-2 q-pa-sm">
                            <q-btn 
                                class="q-pa-sm"
                                dense
                                size="md"
                                color="primary" 
                                label="Show Record"
                                @click="getList"
                            />
                        </div>
                    </div>
                </div> -->
                <div v-if="tableRow.length === 0" class="col col-md-12 text-center">
                    <noData
                        @reloadData="getList"
                    />
                </div>
                <div v-if="tableRow.length > 0" class="col col-md-12 q-mt-md">
                    <q-table
                        :rows="tableRow"
                        :filter="filter"
                        :columns="tableColumns"
                        row-key="key"
                        separator="cell"
                    >
                        <template v-slot:top-left>
                            <span class="text-h6">Agent Transaction from {{syncDate}}</span>
                        </template>
                        <template v-slot:top-right>
                            <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                                <template v-slot:append>
                                    <q-icon name="search" />
                                </template>
                            </q-input>
                        </template>
                        <template v-slot:header="props">
                            <q-tr :props="props">
                                <q-th auto-width />
                                <q-th
                                    v-for="col in props.cols"
                                    :key="col.name"
                                    :props="props"
                                >
                                    {{ col.label }}
                                </q-th>
                            </q-tr>
                        </template>
                        <template v-slot:body="props">
                            <q-tr :props="props">
                                <q-td auto-width>
                                    <q-btn 
                                        size="sm" 
                                        :color="props.expand ? 'negative' : 'primary'"
                                        round 
                                        dense 
                                        @click="props.expand = !props.expand" 
                                        :icon="props.expand ? 'cancel' : 'display_settings'"
                                    />
                                </q-td>
                                <q-td
                                    v-for="col in props.cols"
                                    :key="col.name"
                                    :props="props"
                                >
                                    {{ col.value }}
                                </q-td>
                            </q-tr>

                            <q-tr v-if="props.expand" :props="props">
                                <q-td colspan="100%">
                                    <div class="row">
                                        <div class="col col-md-4">
                                            <q-btn
                                                class="block full-width q-mt-sm"
                                                unelevated 
                                                rounded
                                                size="sm"
                                                color="primary" 
                                                label="Store Relocation"
                                                @click="updateClientGeoLocation(props.row)"
                                            />
                                        </div>
                                    </div>
                                </q-td>
                            </q-tr>
                        </template>
                    </q-table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import noData from '../Templates/NoData.vue';
import { Loader } from "@googlemaps/js-api-loader"
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const loader = new Loader({
    apiKey: 'AIzaSyCrQ2gSBwhbFsnj8JSYxCnTkXrb1ZJbmjw',
    version: "weekly",
    libraries: ["places"]
});

export default {
    name: 'ProductList',
    data(){
        return {
            isContinueEdit: false,
            isPwd: true,
            isLoading: false,
            submitting: false,
            tableRow: [],
            filter: '',
            saveDetails: null,
            saveId: null,
            insertedID: null,
            openBookModal: false,
            bookList: [],
            appId: 0,

            agentList: [],
            agentId: '',
            syncDate: moment().format('yyyy-MM-DD'),
            imgSrc: '',
        }
    },
    components: {
        noData
    },
    created(){
        this.getList()
    },
    methods: {
        moment,
        async getList(){
            this.tableRow = [];
            this.isLoading = true;

            api.get('mobile/fetch/client/list').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.tableRow = response.status < 300 ? data.list : [];
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
        updateClientGeoLocation(row){
            this.$q.dialog({
                title: 'Change Store Location?',
                message: 'Are you sure you want to take this action?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                let modified = {
                    "geoLocation": {}
                }

                this.updateClientData(row.key, modified)
            })
        },
        async updateClientData(id, value){
            this.tableRow = [];
            this.isLoading = true;
            let payload = {
                id: Number(id),
                modified: value
            }

            api.post('mobile/client/update', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$q.notify({
                        color: 'success',
                        position: 'top-right',
                        title:data.title,
                        message: 'Success',
                        icon: 'check_circle'
                    })
                    this.getList()
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
        } 
        // end
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        tableColumns: function(){
            // ["Business Name", "Address", "Category", "Call", "Remarks", "Action"];
            return [
                {
                    name: 'storeName',
                    required: true,
                    label: 'Business Name',
                    align: 'left',
                    field: row => row.storeName,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'address',
                    required: true,
                    label: 'Address',
                    align: 'left',
                    field: row => row.address,
                    format: val => `${val}`,
                    sortable: true
                },
                // { name: 'category', label: 'Category', field: 'category', align: 'left' },
                { name: 'contactPerson', label: 'Contact Person', field: 'contactPerson', align: 'left' },
                { name: 'contactNumber', label: 'Contact Number', field: 'contactNumber', align: 'left' },
            ]
        }
    }
}
</script>

<style lang="scss" scoped>
.title{
    font-weight: 600;
    font-size: 18pt;
}
</style>
