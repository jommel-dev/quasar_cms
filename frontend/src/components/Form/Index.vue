<template>
    <div class="q-pa-lg">
        <singleForm 
            ref="orForm"
            keep-alive
        />
        <div class="row">
            <div class="col col-xs-12 col-md-12 q-pa-sm q-mt-md">
                <q-btn
                    class="q-mr-sm"
                    type="submit"
                    :loading="btnLoading"
                    @click="saveForm" 
                    color="primary" 
                    label="Save and Continue Later"
                />
                <q-btn 
                    type="submit"
                    :loading="btnLoading"
                    @click="finalizeForm" 
                    color="primary" 
                    label="Finalize and Submit"
                />
            </div>
        </div>

        <printModal :appId="insertedID" :modalStatus="openPrintStatus" @updatePrintStatus="updatePrintStatus"/>
    </div>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage } from 'quasar'
import singleForm from './Steps/SingleForm.vue';
import printModal from './Modal/PrintModal.vue';
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'OperativeReportForm',
    props: {
        isEdit: {
            type: Boolean
        }
    },
    data(){
        return {
            saveEdit: false,
            btnLoading: false,
            insertedID: null,
            openPrintStatus: false,
        }
    },
    components: {
        singleForm,
        printModal
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    methods: {
        updatePrintStatus(val){
            this.openPrintStatus = val;
        },
        saveForm(){
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
        async submitForm(){// Validate
            let compoDetails = this.$refs.orForm;
            let ufiles = compoDetails.fileGenerated;

            let vm = this;
            this.$q.loading.show({
                message: 'Your data is finalizing and submitting. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                ...compoDetails.form,
                createdBy: this.user.userId,
            }

            // console.log(payload)
            // return false;

            api.post('app/create', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    if(ufiles.length > 0){
                        let counter = 0;

                        ufiles.forEach((el, key) => {
                            let fileLoad = {
                                file: {
                                    referenceId: data.userId,
                                    encodedURL: el.encodedUrl
                                }
                            }
                            api.post('upload/operation/attachment', fileLoad).then((res) => {});
                            counter += 1;
                        });

                        if(counter == ufiles.length){
                            this.$q.notify({
                                color: 'positive',
                                position: 'top-right',
                                title:data.title,
                                message: data.message,
                                icon: 'done'
                            })
                            this.$q.loading.hide();
                            // this.step = 1;
                            this.insertedID = data.userId;
                            this.generateMemo(data.userId);
                            this.$nextTick(() => {
                                compoDetails.resetForm();
                            });
                        } else {
                            this.$q.notify({
                                color: 'negative',
                                position: 'top-right',
                                title:data.title,
                                message: 'Having some problems upload attachment failed',
                                icon: 'report_problem'
                            })
                            this.$q.loading.hide();
                        }
                    } else {
                        // No uploaded image
                        this.$q.notify({
                            color: 'positive',
                            position: 'top-right',
                            title:data.title,
                            message: data.message,
                            icon: 'done'
                        })
                        this.$q.loading.hide();
                        // this.step = 1;
                        this.insertedID = data.userId;
                        this.generateMemo(data.userId);
                        this.$nextTick(() => {
                            compoDetails.resetForm();
                        });
                    }
                    
                    // this.openPrintStatus = true;
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
        async generateMemo(id){
            let payload = {
                patientId: id 
            };

            await api.post(`download/patient/memoLink`, payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    const link = document.createElement('a');
                    link.id = "downloadResult";
                    link.href = data.urlLink;
                    link.target = "_blank";

                    link.click();
                    this.isViewAll = true;
                    this.selectLoading = false;
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
        async saveOrData(){
            // Validate
            let compoDetails = this.$refs.orForm;
            
            let vm = this;
            this.$q.loading.show({
                message: 'Your data is being save. Please wait...'
            });
            this.btnLoading = true;

            let payload = {
                ...compoDetails.form,
                createdBy: this.user.userId,
            }

            api.post('app/save', payload).then((response) => {
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
    },
    
}
</script>
