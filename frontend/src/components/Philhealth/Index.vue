<template>
    <div>
        <div class="q-pa-md" style="width: 100%;">
            <div class="row">
                <div v-if="isLoading" class="col text-center">
                    <q-spinner-orbit
                        color="primary"
                        size="3em"
                    />
                </div>
                
                <div class="col col-md-12">
                    <q-btn-group class="q-mb-md" flat>
                        <q-btn rounded size="md" color="positive" @click="openModal" label="Add new crs" />
                    </q-btn-group>
                    <div v-if="tableRow.length == 0" class="col text-center">
                        <noData />
                    </div>
                    <div v-if="tableRow.length > 0">
                        <q-table
                            title="Users List"
                            :rows="tableRow"
                            :columns="tableColumns"
                            :filter="filter"
                            row-key="name"
                        >
                            <template v-slot:top-right>
                                <q-input outlined dense debounce="300" v-model="filter" placeholder="Search">
                                    <template v-slot:append>
                                        <q-icon name="search" />
                                    </template>
                                </q-input>
                            </template>
                            <template v-slot:body-cell-status="props">
                                <q-td :props="props">
                                    {{ props.row.status == 1 ? `Active`:`Deactive` }}
                                </q-td>
                            </template>
                            <template v-slot:body-cell-actions="props">
                                <q-td :props="props">
                                    <q-btn-group  rounded>
                                        <q-btn rounded size="sm" color="info" label="Edit" @click="openEditModal(props.row)" />
                                    </q-btn-group>
                                </q-td>
                            </template>
                        </q-table>
                    </div>
                </div>
            </div>
        </div>

        <addCRSModal 
            v-bind="modalComponents" 
            @updateStatus="updateModalStatus" 
            @updateTable="getCRSList"
        />
    </div>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import noData from '../Templates/NoData.vue';
import addCRSModal from './Modal/AddCrsModal.vue';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default{
    name: 'CRSList',
    components: {
        noData,
        addCRSModal
    },
    data(){
        return {
            isPwd: true,
            submitting: false,
            isLoading: false,
            tableRow: [],
            modalComponents: {
                modalStatus: false,
                appId: 0,
                userDetails: {},
                modalTitle: 'Add New CRS',
                actionType: 'ADD'
            }
        }
    },
    created(){
        this.getCRSList();
    },
    methods: {
        openEditModal(val){
            this.modalComponents.modalStatus = true;
            this.modalComponents.appId = val.id;
            this.modalComponents.modalTitle = `Edit ${val.Description}`;
            this.modalComponents.actionType = 'EDIT';
        },
        openModal(){
            this.modalComponents.modalStatus = true;
            this.modalComponents.appId = 0;
            this.modalComponents.modalTitle = `Add New CRS`;
            this.modalComponents.actionType = 'ADD';
        },
        updateModalStatus(){
            this.modalComponents.modalStatus = false;
            this.modalComponents.appId = 0;
            this.modalComponents.modalTitle = `Add New CRS`;
            this.modalComponents.actionType = 'ADD';
        },
        async getCRSList(){
            this.isLoading = true;
            let vm = this;
            
            api.get('misc/philhealthCrs').then((response)=>{
                let data = {...response.data}

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
            });

            this.isLoading = false;
        },
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        tableColumns: function(){
            // name, age, sex, classification, operatedDate, operatedBy, assistBy, crsPerformed, rvsCode, createdDate, createdBy
            return [
                {
                    name: 'rvsCode',
                    required: true,
                    label: 'RVS CODE',
                    align: 'left',
                    field: row => row.rvsCode,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'Description', label: 'Description', field: 'Description', align: 'left'},
                { name: 'actions', label: 'Action', field: 'actions', align: 'left'},
                // { name: 'typeTemplate', label: 'Template', field: 'typeTemplate' },
                
            ]
        }
    }
}
</script>
