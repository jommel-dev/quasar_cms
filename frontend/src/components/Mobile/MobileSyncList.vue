<template>
    <div>
        <div v-if="!isContinueEdit" class="q-pa-md" style="width: 100%;">
            <div class="row">
                <div class="col col-md-6">
                    <span class="title">Agent Mobile App Sync List</span>
                </div>
                <div v-if="isLoading" class="col col-md-12 text-center">
                    <q-spinner-orbit
                        color="primary"
                        size="3em"
                    />
                </div>
                <!-- Filter -->
                <div class="col col-md-12 q-pa-md">
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
                </div>
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
                                        <div class="col col-md-4 q-pa-sm">
                                            {{initMap(props.row)}}
                                            <span class="text-bold q-mb-sm">Store Location</span>
                                            <div id="map" style="width:100%; height: 200px;"></div>
                                        </div>
                                        <div class="col col-md-4 q-pa-sm">
                                            {{initMapTimeIn(props.row.attendance.attendance.geoLocation.coorIn)}}
                                            <span class="text-bold q-mb-sm">Call In</span>
                                            <div id="mapIn" style="width:100%; height: 200px;"></div>
                                        </div>
                                        <div class="col col-md-4 q-pa-sm">
                                            {{initMapTimeOut(props.row.attendance.attendance.geoLocation.timeOut)}}
                                            <span class="text-bold q-mb-sm">Call Out</span>
                                            <div id="mapOut" style="width:100%; height: 200px;"></div>
                                        </div>
                                        <div class="col col-md-4 q-pa-sm">
                                            <span class="text-bold q-mb-sm">Call Details</span>
                                            <q-list>
                                                <q-item tag="label">
                                                    <q-item-section side top>
                                                        <q-icon name="phone_callback" color="green" />
                                                    </q-item-section>

                                                    <q-item-section>
                                                        <q-item-label>{{props.row.attendance.attendance.startCall}}</q-item-label>
                                                        <q-item-label caption>
                                                            Time In
                                                        </q-item-label>
                                                    </q-item-section>
                                                </q-item>
                                                <q-item tag="label">
                                                    <q-item-section side top>
                                                        <q-icon name="call_end" color="negative" />
                                                    </q-item-section>

                                                    <q-item-section>
                                                        <q-item-label>{{props.row.attendance.attendance.endCall}}</q-item-label>
                                                        <q-item-label caption>
                                                            Time Out
                                                        </q-item-label>
                                                    </q-item-section>
                                                </q-item>
                                            </q-list>
                                        </div>
                                        <div class="col col-md-4 q-pa-sm">
                                            <span class="text-bold q-mb-sm">Activities</span>
                                            <q-list>
                                                <q-item 
                                                    v-for="(item, index) in props.row.activity" 
                                                    tag="label" 
                                                    :key="index" 
                                                    v-ripple
                                                >
                                                    <q-item-section side top>
                                                        <q-icon v-if="!item.active" name="cancel" color="negative" />
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
                                            <q-btn
                                                class="block full-width q-mt-sm"
                                                unelevated 
                                                rounded
                                                size="sm"
                                                color="primary" 
                                                label="Get Booking Details"
                                                @click="getBookDetails(props)"
                                            />
                                        </div>
                                        <div class="col col-md-4 q-pa-sm">
                                            <span class="text-bold q-mb-sm">Photo</span><br/>
                                            {{getCallPhoto(props)}}
                                            <q-img
                                                :src="imgSrc"
                                                spinner-color="primary"
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
        <BookingModal
            :otherDetails="otherDet"
            :bookedList="bookList"
            :modalStatus="openBookModal"
            @updateModalStatus="closeBooking"
        />
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import noData from '../Templates/NoData.vue';
import { Loader } from "@googlemaps/js-api-loader"
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'
import BookingModal from './Modals/BookingModal.vue';

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
            otherDet: {},

            agentList: [],
            agentId: '',
            syncDate: moment().format('yyyy-MM-DD'),
            imgSrc: '',
        }
    },
    components: {
        noData,
        BookingModal
    },
    created(){
        this.getAgentList()
    },
    methods: {
        moment,
        closeBooking(){
            this.openBookModal = false
        },
        getAgentList(){
            api.get('users/getAgents').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    let options =data.list.map((el) => {
                        let obj = {
                            label: el.name,
                            value: el.key,
                        }
                        return obj
                    })
                    this.agentId = options[0]
                    this.agentList = response.status < 300 ? options : [];
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
        },
        async getList(){
            this.tableRow = [];
            this.isLoading = true;
            let payload = {
                aid: Number(this.agentId.value),
                date: moment(this.syncDate).format('yyyy-MM-DD')
            }
            let vm = this;
            
            api.post('mobile/client/list', payload).then((response) => {
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
        getBookDetails(row){
            let payload = {
                aid: Number(this.agentId.value),
                date: moment(this.syncDate).format('yyyy-MM-DD'),
                idx: row.key
            }
            
            api.post('mobile/get/booking', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.bookList = response.data.booking
                    payload.cid = row.row.id
                    payload.sid = row.row.syncId
                    this.otherDet = payload
                    this.openBookModal = true
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
        },
        getCallPhoto(row){
            let payload = {
                aid: Number(this.agentId.value),
                date: moment(this.syncDate).format('yyyy-MM-DD'),
                idx: row.key
            }

            api.post('mobile/get/photo', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.imgSrc = data.imgSrc
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
        },

        initMap(data){
            loader
            .load()
            .then(async (google) => {
                // Loading the maps
                await this.$nextTick(() => {
                    const mstore = new google.maps.LatLng(data.geoLocation.lat, data.geoLocation.lng);
                    const map = new google.maps.Map(document.getElementById("map"), {
                        center: mstore,
                        zoom: 17,
                    });
                    const coordInfoWindow = new google.maps.InfoWindow();

                    coordInfoWindow.setContent(this.createInfoWindowContent(data));
                    coordInfoWindow.setPosition(mstore);
                    coordInfoWindow.open(map);

                    // Store Marker
                    const marker = new google.maps.Marker({
                        map,
                        position: {lat: data.geoLocation.lat, lng: data.geoLocation.lng},
                    });
                
                    marker.addListener('click', ({domEvent, latLng}) => {
                        coordInfoWindow.setContent(this.createInfoWindowContent(data));
                    });
                })
            })
            .catch((e) => {
                // do something
                console.log(e)
            });
        },
        initMapTimeIn(data){
            loader
            .load()
            .then(async (google) => {
                // Loading the maps
                await this.$nextTick(() => {
                    const mstore = new google.maps.LatLng(data.lat, data.lng);
                    const map = new google.maps.Map(document.getElementById("mapIn"), {
                        center: mstore,
                        zoom: 17,
                    });

                    // Store Marker
                    const marker = new google.maps.Marker({
                        map,
                        position: {lat: data.lat, lng: data.lng},
                    });
                })
            })
            .catch((e) => {
                // do something
                console.log(e)
            });
        },
        initMapTimeOut(data){
            loader
            .load()
            .then(async (google) => {
                // Loading the maps
                await this.$nextTick(() => {
                    const mstore = new google.maps.LatLng(data.lat, data.lng);
                    const map = new google.maps.Map(document.getElementById("mapOut"), {
                        center: mstore,
                        zoom: 17,
                    });

                    // Store Marker
                    const marker = new google.maps.Marker({
                        map,
                        position: {lat: data.lat, lng: data.lng},
                    });
                })
            })
            .catch((e) => {
                // do something
                console.log(e)
            });
        },
        createInfoWindowContent(client) {
            let brgy = client.addressDetails !== undefined ? client.addressDetails.barangay.label : '';
            let city = client.addressDetails !== undefined ? client.addressDetails.city.label : '';
            let prov = client.addressDetails !== undefined ? client.addressDetails.province.label : '';
            let address = `${client.address}, ${brgy || ''}, ${city || ''}, ${prov || ''}`;
            return [
                client.storeName,
                "Address: " + address
            ].join("<br>");
        },
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
                { name: 'contactPerson', label: 'Owner', field: 'contactPerson', align: 'left' },
                { name: 'contactNumber', label: 'Contact', field: 'contactNumber', align: 'left' },
                { name: 'remarks', label: 'Remarks', field: 'remarks', align: 'left' },
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
