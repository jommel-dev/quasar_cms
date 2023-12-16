<template>
    <div>
        <div v-if="!isContinueEdit" class="q-pa-md" style="width: 100%;">
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
                        <template v-slot:body-cell-actions="props">
                            <q-td :props="props">
                                <q-btn 
                                    dense
                                    flat
                                    outline
                                    size="md"
                                    color="primary" 
                                    label="Continue Editing"
                                    @click="getSavedDetails(props.row.key)"
                                />
                                <q-btn 
                                    dense
                                    flat
                                    outline
                                    size="md"
                                    color="red" 
                                    label="Delete"
                                    @click="deleleSaveData(props.row.key)"
                                />
                            </q-td>
                        </template>
                    </q-table>
                </div>
            </div>
        </div>

        <div v-if="isContinueEdit" class="q-pa-lg">
            <singleForm 
                ref="orForm" 
                keep-alive 
                :savedData="saveDetails" 
                :savedId="saveId"
            />

            <div class="row">
                <div class="col col-xs-12 col-md-12 q-pa-sm q-mt-md">
                    <q-btn
                        class="q-mr-sm"
                        type="submit"
                        @click="backToList" 
                        color="negative" 
                        label="Cancel Editing"
                    />
                    <q-btn
                        class="q-mr-sm"
                        type="submit"
                        :loading="submitting"
                        @click="saveChangeForm" 
                        color="primary" 
                        label="Save Changes"
                    />
                    <q-btn 
                        type="submit"
                        :loading="submitting"
                        @click="finalizeForm" 
                        color="primary" 
                        label="Finalize and Submit"
                    />
                </div>
            </div>
        </div>
        
        <printModal :appId="insertedID" :modalStatus="openPrintStatus" @updatePrintStatus="updatePrintStatus"/>
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import singleForm from '../Form/Steps/SingleForm.vue';
import printModal from '../Form/Modal/PrintModal.vue';
import noData from '../Templates/NoData.vue';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default{
    name: 'SavePatientList',
    data(){
        return {
            isContinueEdit: false,
            isPwd: true,
            isLoading: false,
            submitting: false,
            tableRow: [],
            saveDetails: null,
            saveId: null,
            insertedID: null,
            openPrintStatus: false,
            appId: 0,
        }
    },
    components: {
        singleForm,
        printModal,
        noData
    },
    created(){
        this.getList();
    },
    methods: {
        moment,
        backToList(){
            this.getList();
        },
        updatePrintStatus(val){
            this.openPrintStatus = val;
        },
        async getList(){
            this.isContinueEdit = false;
            this.tableRow = [];
            this.isLoading = true;
            let vm = this;
            
            let payload = {
                user: this.user.userId,
                userType: this.user.userType
            }
            
            api.post('app/getSaveList', payload).then((response) => {
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
        async getSavedDetails(id){
            this.$q.loading.show({
                message: 'Getting you save data. Please wait...'
            });
            let vm = this;
            vm.appId = id;

            await api.get(`app/getSaveDetails/${id}`).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    // setup the data first
                    this.saveId = data.id;
                    this.saveDetails = data;

                    // ready the Form for existing data
                    this.$nextTick(() => {
                        this.isContinueEdit = true;
                        this.$q.loading.hide();
                    })
                } else {
                    this.isContinueEdit = false;
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })

                    this.selectLoading = false;
                }
            })
        },
        async deleleSaveData(id){
            let vm = this;
            // Confirm
            this.$q.dialog({
                title: 'Delete Changes',
                message: 'Would you like to delete the data you saved?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                vm.deleteData(id)
            })
            
            
        },
        async deleteData(id){
            this.$q.loading.show({
                message: 'Processing delete data. Please wait...'
            });
            let payload = {id};

            await api.post(`app/delete`, payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    // ready the Form for existing data
                    this.$nextTick(() => {
                        this.$q.loading.hide();
                    })

                    this.backToList();
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
        finalizeForm(){
            // Validate
            let compoDetails = this.$refs.orForm;
            compoDetails.$refs.formDetails.validate().then(success => {
                if(!success){
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title: 'Incomplete Form',
                        message: 'Please fill the required fields',
                        icon: 'report_problem'
                    })
                    return false;
                } else {
                    // Confirm
                    this.$q.dialog({
                        title: 'Save and Finalize',
                        message: 'Would you like to finalize and save your data?',
                        ok: {
                            label: 'Yes'
                        },
                        cancel: {
                            label: 'No',
                            color: 'negative'
                        },
                        persistent: true
                    }).onOk(() => {
                        this.submitForm();
                    })
                }
            })

        },
        async submitForm(){
            // Validate
            let compoDetails = this.$refs.orForm;
            
            let vm = this;
            this.$q.loading.show({
                message: 'Your data is finalizing and submitting. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                ...compoDetails.form,
                createdBy: this.user.userId,
                finalize: this.saveId,
            }

            // console.log(payload)
            // return false;

            api.post('app/create', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$q.notify({
                        color: 'positive',
                        position: 'top-right',
                        title:data.title,
                        message: data.message,
                        icon: 'done'
                    })
                    this.$q.loading.hide();
                    this.insertedID = data.userId;
                    this.openPrintStatus = true;

                    this.$nextTick(() => {
                        this.getList();
                        // compoDetails.resetForm();
                    });
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                    this.$q.loading.hide();
                }

            })
            
            this.btnLoading = false;
        },
        saveChangeForm(){
           // Confirm
            this.$q.dialog({
                title: 'Save Changes',
                message: 'Would you like to save the changes you made?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                this.submitSaveChangeForm();
            })
        },
        async submitSaveChangeForm(){
            // Validate
            let compoDetails = this.$refs.orForm;
            
            let vm = this;
            this.$q.loading.show({
                message: 'Your data is finalizing and submitting. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                data: {
                    ...compoDetails.form,
                    createdBy: this.user.userId,
                    finalize: this.saveId,
                },
                appId: this.appId
                
            }
            
            api.post('app/saveChanges', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$q.notify({
                        color: 'positive',
                        position: 'top-right',
                        title:data.title,
                        message: data.message,
                        icon: 'done'
                    })
                    this.$q.loading.hide();

                    this.$nextTick(() => {
                        this.getList();
                    });
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: this.$t(`errors.${data.error}`),
                        icon: 'report_problem'
                    })
                    this.$q.loading.hide();
                }

            })
            
            this.btnLoading = false;
        },
        // end
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
                { name: 'createdDate', label: 'Created Date', field: 'createdDate' },
                { name: 'actions', label: 'Action', field: 'actions' }
            ]
        }
    }
}
</script>
