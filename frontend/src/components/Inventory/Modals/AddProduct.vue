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
                    <div class="text-h6">Add New Product</div>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 70vh;" class="q-pt-none scroll">
                    <q-form
                        ref="formDetails"
                        class="row"
                    >   
                        <div class="col col-md-12">
                            <span class="text-h5">Product Details</span>
                            <q-separator />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input
                                :disable="processType === 'edit'"
                                outlined 
                                v-model="form.sku" 
                                label="Product SKU" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.productName" 
                                label="Product Name" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.productCost" 
                                label="Product Cost" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.productSRP" 
                                label="Product SRP" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="form.unit" 
                                :options="uTypeOptions" 
                                label="Unit Type" 
                                stack-label 
                                dense
                                options-dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="form.category" 
                                :options="catOptions" 
                                label="Category" 
                                stack-label 
                                dense
                                options-dense
                            />
                        </div>
                        <div class="col col-md-12 q-pa-sm">
                            <q-input 
                                outlined 
                                type="textarea"
                                v-model="form.description" 
                                label="Product Description" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm"></div>

                        
                        <div class="col col-md-12 q-mt-md">
                            <q-toolbar>
                                <span class="text-h5">Price Group Details</span>
                                <q-space />
                                <q-toggle v-model="form.hasPriceGroup" />
                            </q-toolbar>
                            
                            <q-separator />
                        </div>
                        <div 
                            v-for="(item, index) in groupBrnach" 
                            :key="index" 
                            class="col col-md-12 q-pa-sm"
                        >
                            <div v-if="form.hasPriceGroup" class="row">
                            <q-input 
                                class="col col-md-6 q-pa-sm"
                                outlined 
                                v-model="item.regionName"
                                label="Branch"
                                stack-label 
                                disable
                                dense
                            />
                            <q-input 
                                class="col col-md-6 q-pa-sm"
                                outlined 
                                v-model="item.price" 
                                label="Price"
                                stack-label 
                                dense
                            />
                            </div>
                        </div>
                    </q-form>
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn
                        v-if="processType === 'add'"
                        flat 
                        label="Submit" 
                        color="primary"
                        @click="submitModalClick" 
                    />
                    <q-btn
                        v-else
                        flat 
                        label="Update Data" 
                        color="primary"
                        @click="updateProduct" 
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

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'PrintModal',
    data(){
        return {
            openModal: false,
            form: {
                sku: '',
                productName: '',
                productCost: '',
                productSRP: '',
                unit: '',
                category: '',
                description: '',
                hasPriceGroup: false,
                costGroup: {},
                createdBy: 0,
            },
            catOptions: [
                {label: 'Box', value: 'BX'},
                {label: 'Piece', value: 'PC'},
                {label: 'Bottle', value: 'BT'},
                {label: 'Bag', value: 'BG'},
                {label: 'Pouch', value: 'PH'}
            ],
            uTypeOptions: [
                {label: 'Cat Food', value: '1'},
                {label: 'Dog Food', value: '2'},
                {label: 'OTC Medicine', value: '3'},
                {label: 'Pet Accesories', value: '4'},
                {label: 'Poultry Lines', value: '5'}
            ],
            groupBrnach: [
                {
                    "regionId": 1,
                    "regionName": 'Nueva Ecija',
                    "price": 0
                },
                {
                    "regionId": 2,
                    "regionName": 'Aurora',
                    "price": 0
                }
            ]

        }
    },
    watch:{
        modalStatus(newVal){
            this.openModal = newVal
            if(this.processType === 'edit'){
                this.fillTheDetails()
            }
        }
    },
    props: {
        appId: {
            type: Number
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
            this.clearForm();
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
                        this.addNewProduct();
                    })
                }
            })
            
        },

        async addNewProduct(){
            this.$q.loading.show();
            let payload = this.form
            payload.createdBy = this.user.userId

            // check if group pricing
            if(payload.hasPriceGroup){
                payload.costGroup = this.groupBrnach
            }

            api.post('product/add/new', payload).then((response) => {
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
                sku: '',
                productName: '',
                productCost: '',
                productSRP: '',
                unit: '',
                category: '',
                description: '',
                hasPriceGroup: false,
                costGroup: {},
                createdBy: 0,
            }
        }
    }
    
}
</script>
