<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 700px; max-width: 80vw;" >
                <q-bar class="q-mb-md">
                    <div class="text-h6">Add New Patient</div>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 100%;" class="q-pt-none scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >   
                        <div class="col col-md-12">
                            <span class="text-h5">Owner Details</span>
                        </div>
                        <q-card class="my-card q-mb-md col col-md-12" flat bordered>
                            <q-item>
                                <q-item-section avatar>
                                    <q-avatar>
                                        <q-icon name="account_box" size="lg" />
                                    </q-avatar>
                                </q-item-section>

                                <q-item-section>
                                    <q-item-label>{{`${appId.firstName} ${appId.lastName}`}}</q-item-label>
                                    <q-item-label caption>
                                        {{ appId.address }}
                                    </q-item-label>
                                </q-item-section>
                            </q-item>

                            <q-separator />

                            <q-card-section horizontal>
                                <q-card-section>
                                    <span class="text-bold">Contact Number: </span>
                                </q-card-section>
                                <q-separator vertical />
                                <q-card-section class="col-4">
                                    <span>{{appId.contact}}</span>
                                </q-card-section>
                            </q-card-section>
                        </q-card>

                        <div class="col col-md-12">
                            <span class="text-h5">Patient Details</span>
                            <q-separator />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.petName" 
                                label="Pet Name" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                type="date"
                                v-model="form.birthDate" 
                                @change="computeAge"
                                label="Birthdate" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="form.sex" 
                                :options="genderOption" 
                                label="Gender" 
                                stack-label 
                                dense
                                options-dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.species" 
                                label="Species" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input
                                outlined 
                                v-model="form.breed" 
                                label="Breed" 
                                stack-label 
                                dense
                            />
                        </div>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn
                        flat 
                        label="Submit" 
                        color="primary"
                        @click="submitModalClick" 
                    />
                </q-card-actions>
            </q-card>
        </q-dialog>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

export default {
    name: 'PrintModal',
    data(){
        return {
            openModal: false,
            form: {
                clientId: 0,
                petName: '',
                birthDate: '',
                age: 0,
                sex: '',
                species: '',
                breed: '',
            },
            genderOption: [
                {
                    label: 'MALE',
                    value: 'MALE',
                },
                {
                    label: 'FEMALE',
                    value: 'FEMALE',
                },
            ]
        }
    },
    watch:{
        modalStatus(newVal, oldVal){
            console.log(newVal, oldVal)
            this.openModal = newVal
        }
    },
    props: {
        appId: {
            type: Object
        },
        modalStatus: {
            type: Boolean
        },
        processType: {
            type: String
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },

    methods: {
        computeAge(val){
            var today = new Date();
            var birthDate = new Date(val);

            const monthDiff = today.getMonth() - birthDate.getMonth();
            const yearDiff = today.getYear() - birthDate.getYear();

            let ageMonth = monthDiff + yearDiff * 12;

            this.form.age = ageMonth
        },
        fillTheDetails(){
            this.form = {
                sku: this.appId.sku,
                productName: this.appId.productName,
                productCost: this.appId.productCost,
                productSRP: this.appId.productSRP,
                unit: JSON.parse(this.appId.unit),
                category: JSON.parse(this.appId.category),
                description: this.appId.description,
                hasPriceGroup: false,
                costGroup: {},
            }
        },
        async closeModal(){
            this.$emit('updateModalStatus', false);
        },
        async submitModalClick(){
            let vm = this;

            this.$refs.formDetails.validate().then(success => {
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
                        title: 'Submit details',
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
                        // this.$emit('submitModalClick', vm.form);
                        this.addNewPatient();
                    })
                }
            })
            
        },

        async addNewPatient(){
            this.$q.loading.show();
            let payload = this.form
            payload.clientId = this.appId.id
            payload.sex = payload.sex.value
            payload.createdBy = this.user.userId
            
            api.post('patient/add/new', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$emit('refreshData')
                    this.clearForm();
                    this.closeModal();
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
        async updateProduct(){
            this.$q.loading.show();
            let payload = {
                id: this.appId.id,
                ...this.form
            }

           
            api.post('product/update/details', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$emit('refreshData')
                    this.closeModal();
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

        clearForm(){
            this.form = {
                clientId: 0,
                petName: '',
                birthDate: '',
                age: 0,
                sex: '',
                species: '',
                breed: '',
            }
        }
    }
    
}
</script>
