<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <div v-if="isLoading" class="col text-center">
                <q-spinner-orbit
                    color="primary"
                    size="3em"
                />
            </div>
            <div v-if="tableRow.length == 0" class="col text-center">
                <noData />
            </div>
            <div v-if="tableRow.length > 0" class="col">
                
                <q-table
                    title="My Patient List"
                    :rows="tableRow"
                    :columns="tableColumns"
                    row-key="name"
                >
                    <template v-slot:top>
                        <label class="text-h4">Patient List</label>
                        <q-space />
                        <q-toggle
                            v-model="showAll"
                            label="Show All"
                            class="q-mr-md"
                        />

                        <q-input 
                            v-if="!showAll"
                            v-model="dataFrm" 
                            flat 
                            class="q-mr-sm"
                            type="date" 
                            hint="Date From"
                            dense 
                        />
                        <q-input 
                            v-if="!showAll"
                            v-model="dataTo" 
                            flat 
                            class="q-mr-sm"
                            type="date" 
                            hint="Date To"
                            dense 
                        />
                    </template>
                    <template v-slot:body-cell-name="props">
                        <q-td key="name" :props="props">
                            <span :id="`name-${props.row.key}`" v-if="user.userType == 1">{{ props.row.name }}</span>
                            <span v-else>{{ props.row.name }}</span>
                            <q-popup-proxy :target="`#name-${props.row.key}`" context-menu>
                                <q-list bordered separator>
                                    <q-item clickable v-ripple>
                                        <q-item-section @click="deletePatient(props.row.key)">Delete Record</q-item-section>
                                    </q-item>
                                </q-list>
                            </q-popup-proxy>
                        </q-td>
                    </template>
                </q-table>
            </div>
        </div>
    </div>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import noData from '../Templates/NoData.vue';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default{
    name: 'PatientList',
    components: {
        noData
    },
    data(){
        return {
            isPwd: true,
            submitting: false,
            isLoading: false,
            tableRow: [],
            showAll: true,
            dataFrm: '',
            dataTo: '',
        }
    },
    watch: {
        dataFrm: function(newVal){
            if(newVal !== '' && this.dataTo !== ''){
                this.getListRange();
            }
        },
        dataTo: function(newVal){
            if(newVal !== '' && this.dataFrm !== ''){
                this.getListRange();
            }
        },
        showAll: function(newVal){
            if(newVal){
                this.getList();
            } else {
                this.getListRange();
            }
        }
    },
    created(){
        this.getList();
    },
    methods: {
        async getList(){
            this.isLoading = true;
            let vm = this;
            
            let payload = {
                user: this.user.userId,
                userType: this.user.userType
            }
            
            api.post('app/getPatientList', payload).then((response) => {
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
        async getListRange(){
            this.isLoading = true;
            let vm = this;
            
            let payload = {
                user: this.user.userId,
                userType: this.user.userType,
                from: this.dataFrm,
                to: this.dataTo,
            }
            
            api.post('app/getPatientListByRange', payload).then((response) => {
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
        delelePatientPop(id){
            let vm = this;
            // Confirm
            this.$q.dialog({
                title: 'Delete Data',
                message: 'Would you like to delete this data?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                vm.deletePatient(id)
            })
            
            
        },
        async deletePatient(key){
            this.$q.loading.show({
                message: 'Processing delete data. Please wait...'
            });
            let payload = {
                userType: this.user.userType,
                patientId: key
            }

            api.post('app/deletePatient', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$nextTick(() => {
                        this.$q.loading.hide();
                    })

                   if(this.showAll){
                       this.getList();
                   } else {
                       this.getListRange();
                   }
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
                    name: 'name',
                    required: true,
                    label: 'Patient Name',
                    align: 'left',
                    field: row => row.name,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'birthDate', label: 'Birth Date', field: 'birthDate'},
                { name: 'sex', label: 'Sex', field: 'sex' },
                { name: 'classification', label: 'Classification', field: 'classification' },
                { name: 'operatedDate', label: 'Operated Date', field: 'operatedDate' },
                { name: 'operatedBy', label: 'Surgeon', field: 'operatedBy', sortable: true, sort: (a, b) => parseInt(a, 10) - parseInt(b, 10) },
                { name: 'assistBy', label: 'Assistant', field: 'assistBy' },
                { name: 'crsPerformed', label: 'CRS Performed', field: 'crsPerformed' },
                { name: 'rvsCode', label: 'RVS Code', field: 'rvsCode' },
                { name: 'createdDate', label: 'Created Date', field: 'createdDate' }
            ]
        }
    }
}
</script>
