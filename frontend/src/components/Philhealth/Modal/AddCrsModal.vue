<template>
    <div class="q-pa-sm">
        <q-dialog persistent v-model="openModal">
            <q-card style="width: 700px; max-width: 80vw;">
                <q-card-section>
                    <div class="text-h6">{{ modalTitle }}</div>
                </q-card-section>

                <q-separator />

                <q-card-section style="max-height: 60vh" class="scroll">
                    <registerForm ref="regForm" :crsDetails.sync="crsDetails" />
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn flat label="Cancel" :loading="btnLoading" color="negative" @click="closeModal" />
                    <q-btn v-if="actionType == 'ADD'" flat label="Submit" :loading="btnLoading" color="positive" @click="submitForm" />
                    <q-btn v-if="actionType == 'EDIT'" flat label="Save Changes" :loading="btnLoading" color="primary" @click="submitEditForm" />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage, Loading } from 'quasar'
import registerForm from '../Forms/RegisterForm.vue';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'AddCrsModal',
    props: {
        appId: {
            type: Number
        },
        actionType: {
            type: String
        },
        userDetails: {
            type: Object
        },
        modalStatus: {
            type: Boolean
        },
        modalTitle: {
            type: String
        }
    },
    data(){
        return {
            btnLoading: false,
            openModal: false,
            maximizedToggle: true,
            crsDetails: {},
        }
    },
    watch: {
        modalStatus(newVal){
            this.openModal = newVal
        },
        actionType: function(newVal){
            if(newVal == 'EDIT'){
                this.loadDetailsEdit();
            }
        }
    },
    components:{
        registerForm
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    methods: {
        async closeModal(){
            this.$emit('updateStatus', false);
        },
        async loadDetailsEdit(){
            this.$q.loading.show({
                message: 'Getting you save data. Please wait...'
            });
            let vm = this;

            api.get(`misc/philhealthCrs/byId/${this.appId}`).then((response) => {
                const data = {...response.data};
                this.crsDetails = data
                this.$q.loading.hide();
            })
        },
        submitForm(){
            let compoDetails = this.$refs.regForm;
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
                        title: 'Save Data',
                        message: 'Would you like to save your data?',
                        ok: {
                            label: 'Yes'
                        },
                        cancel: {
                            label: 'No',
                            color: 'negative'
                        },
                        persistent: true
                    }).onOk(() => {
                        this.saveOrData();
                    })
                }
            })
        },

        async saveOrData(){
            let compoDetails = this.$refs.regForm;
            
            let vm = this;
            this.$q.loading.show({
                message: 'Your data is submitting. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                ...compoDetails.form
            }

            api.post('misc/philhealthCrs/insert', payload).then((response) => {
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
                        compoDetails.resetForm();
                        this.$emit('updateTable');
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


        submitEditForm(){
            let compoDetails = this.$refs.regForm;
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
                        title: 'Save Changes',
                        message: 'Would you like to save your changes?',
                        ok: {
                            label: 'Yes'
                        },
                        cancel: {
                            label: 'No',
                            color: 'negative'
                        },
                        persistent: true
                    }).onOk(() => {
                        this.saveEditData();
                    })
                }
            })
        },


        async saveEditData(){
            let compoDetails = this.$refs.regForm;
            
            let vm = this;
            this.$q.loading.show({
                message: 'Your data is submitting. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                data: {...compoDetails.form},
                appId: this.appId
            }

            api.post('misc/philhealthCrs/update', payload).then((response) => {
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
                        this.$emit('updateTable');
                        this.$emit('updateStatus', false);
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
        }
        // ending method
    },
    
}
</script>
