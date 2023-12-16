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
                        <q-btn rounded size="md" color="positive" @click="openModal" label="Add new user" />
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
                                        <q-btn rounded size="sm" color="info" label="Edit" />
                                        <q-btn rounded size="sm" color="negative" label="Deactivate" />
                                    </q-btn-group>
                                </q-td>
                            </template>
                        </q-table>
                    </div>
                </div>
            </div>
        </div>

        <addUserModal 
            v-bind="modalComponents" 
            @updateStatus="updateModalStatus" 
            @updateTable="getList"
        />
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import noData from '../Templates/NoData.vue';
import addUserModal from './Modal/AddUserModal.vue';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default{
    name: 'SavePatientList',
    data(){
        return {
            isLoading: false,
            submitting: false,
            tableRow: [],
            saveDetails: null,
            saveId: null,
            insertedID: null,
            filter: '',
            modalComponents: {
                modalStatus: false,
                appId: 0,
                userDetails: {},
                modalTitle: 'Add New User'
            }
        }
    },
    components: {
        addUserModal,
        noData
    },
    created(){
        this.getList();
    },
    methods: {
        openModal(){
            this.modalComponents.modalStatus = true;
        },
        updateModalStatus(){
            this.modalComponents.modalStatus = false;
        },
        async getList(){
            this.tableRow = [];
            this.isLoading = true;
            let vm = this;
            
            let payload = {
                user: this.user.userId,
                userType: this.user.userType
            }
            
            api.get('users/getAllUserList').then((response) => {
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
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        tableColumns: function(){
            return [
                {
                    name: 'name',
                    required: true,
                    label: 'Name',
                    align: 'left',
                    field: row => row.name,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'username', label: 'Username', field: 'username'},
                { name: 'userType', label: 'Type', field: 'userType' },
                { name: 'email', label: 'Email Address', field: 'email' },
                { name: 'contact', label: 'Contact #', field: 'contact' },
                { name: 'status', label: 'Status', field: 'status' },
                { name: 'createdAt', label: 'Created Date', field: 'createdAt' },
                { name: 'actions', label: 'Action', field: 'actions' }
            ]
        }
    }
}
</script>
