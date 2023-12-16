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
                            <q-select 
                                outlined 
                                v-model="form.prodId" 
                                :options="productOptions" 
                                label="Product" 
                                stack-label 
                                dense
                                options-dense 
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="form.unitType" 
                                :options="uTypeOptions" 
                                label="Unit Type" 
                                stack-label 
                                dense
                                options-dense
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.prodSerial" 
                                label="Product Serial" 
                                stack-label 
                                dense
                                :rules="[ val => val && val.length > 0 || 'This field is required']"
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                type="date"
                                v-model="form.deliveryDate" 
                                label="Delivery Date" 
                                stack-label 
                                dense
                                :rules="[ val => val && val.length > 0 || 'This field is required']"
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                type="date"
                                v-model="form.expirationDate" 
                                label="Expiry Date" 
                                stack-label 
                                dense
                                :rules="[ val => val && val.length > 0 || 'This field is required']"
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm"></div>

                        
                        <div class="col col-md-12 q-mt-md">
                            <span class="text-h5">Inventory Details</span>
                            <q-separator />
                        </div>

                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.qty" 
                                label="Product Quantity" 
                                stack-label 
                                dense
                                :rules="[ val => val && val.length > 0 || 'This field is required']"
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.stockNotice" 
                                label="Stock Notice" 
                                stack-label 
                                dense
                                :rules="[ val => val && val.length > 0 || 'This field is required']"
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.itemPrice" 
                                label="Price" 
                                stack-label 
                                dense
                                :rules="[ val => val && val.length > 0 || 'This field is required']"
                            />
                        </div>
                        
                        <div class="col col-md-12"></div>
                        <div
                            v-for="(el, index) in form.otherDetails"
                            class="col col-md-4 q-pa-sm"
                            :key="index"
                        >
                            <q-input 
                                outlined 
                                v-model="form.otherDetails[index]" 
                                :label="otherDetailsLabels[index]" 
                                stack-label 
                                dense 
                            />
                        </div>
                        <div class="col col-md-4 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="form.status" 
                                :options="statusOption" 
                                label="Status" 
                                stack-label 
                                dense
                                options-dense
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
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'PrintModal',
    data(){
        return {
            openModal: false,
            form: {
                name: '',
                description: '',
                category: '',
                subCategory: '',
                type: '',
                supplier: { label: '', value: '' },
                status: { label: 'Active', value: 1 },
            },
            catOptions: [
                {label: 'Box', value: 'box'},
                {label: 'Piece', value: 'pcs'}
            ],
            subCatOptions: [
                {label: 'Box', value: 'box'},
                {label: 'Piece', value: 'pcs'}
            ],
            statusOption: [
                {label: 'Active', value: 1},
                {label: 'Inactive', value: 0}
            ],
        }
    },
    watch:{
        modalStatus(newVal){
            this.openModal = newVal
        }
    },
    props: {
        appId: {
            type: Number
        },
        modalStatus: {
            type: Boolean
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    created(){
        this.getProducts();
    },
    methods: {
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
                        this.$emit('submitModalClick', vm.form);
                    })
                }
            })
            
        },
        getProducts(){
            api.get('inventory/product/get').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    let listVal = data.list;

                    this.productOptions = listVal.map((el) => {
                        return {
                            label: el.productName,
                            value: el.key
                        }
                    })
                    // this.tableRow = response.status < 300 ? data.list : [];
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
        }
    }
    
}
</script>
