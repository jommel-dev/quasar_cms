<template>
    <div class="q-pa-lg">
        <q-form
            @submit="onSubmit"
            ref="formDetails"
            class="row"
        >
            <div class="col col-xs-12 col-sm-12 col-md-6 q-pa-md">
                <q-btn
                    size="sm"
                    unelevated 
                    @click="openAddModal"
                    color="primary"
                >
                    <q-icon left name="add" />
                    <div>Search Item</div>
                </q-btn>
                <q-btn
                    :disable="transactions.length === 0"
                    size="sm"
                    class="q-ml-sm"
                    unelevated 
                    @click="clearItemList"
                    color="red"
                >
                    <q-icon left name="delete" />
                    <div>Clear Items</div>
                </q-btn>

                <div class="row q-mt-md">
                    <div class="col col-md-12">
                        <q-list bordered class="rounded-borders">
                            
                            <q-item>
                                <q-item-section avatar top>
                                </q-item-section>
                                <q-item-section top class="col-1 text-center">
                                    QTY
                                </q-item-section>
                                <q-item-section top class="col-1 text-center">
                                    Unit
                                </q-item-section>
                                <q-item-section top class="q-pl-md ">
                                    Description
                                </q-item-section>
                                <q-item-section top side>
                                    Actions
                                </q-item-section>
                            </q-item>
                            <q-separator spaced />

                            <q-item
                                v-for="(item, index) in transactions"
                                :key="index"
                            >
                                <q-item-section avatar top>
                                    <q-icon name="qr_code_2" color="black" size="34px" />
                                </q-item-section>

                                <q-item-section top class="col-1 gt-sm text-center">
                                    <q-input
                                        v-model="item.qty"
                                        outlined 
                                        dense
                                    >
                                    </q-input>
                                </q-item-section>

                                <q-item-section class="col-1 gt-sm text-center">
                                    <q-item-label caption>
                                        {{ item.itemUnit }}
                                    </q-item-label>
                                </q-item-section>

                                <q-item-section top class="q-pl-md">
                                    <q-item-label lines="1">
                                        <span class="text-weight-medium">[{{item.itemCount}}]</span>
                                        <span class="text-grey-8"> - {{item.itemName}}</span>
                                    </q-item-label>
                                    <q-item-label caption lines="1">
                                        Serial No.: {{item.itemSerial}}
                                    </q-item-label>
                                </q-item-section>

                                <q-item-section top side>
                                    <div class="text-grey-8 q-gutter-xs">
                                        <q-btn class="gt-xs" size="12px" flat dense round icon="delete" />
                                    </div>
                                </q-item-section>
                            </q-item>
                            
                        </q-list>
                    </div>
                </div>
            </div>
            <div class="col col-xs-12 col-sm-12 col-md-6">
                <div class="row">
                    <div class="col col-xs-12 col-sm-12 col-md-12 q-pa-sm q-mt-md">
                        <span class="text-h5">Customer Details</span>
                    </div>
                    <div class="col col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                        <q-input 
                            outlined 
                            v-model="form.customerName" 
                            label="Customer Name" 
                            stack-label 
                            dense
                            :rules="[ val => val && val.length > 0 || 'This field is required']"
                        />
                    </div>
                    <div
                        v-for="(el, index) in form.customerInfo"
                        class="col col-xs-12 col-sm-6 col-md-4 q-pa-sm"
                        :key="index"
                    >
                        <q-input 
                            outlined 
                            v-model="form.customerInfo[index]" 
                            :label="customerInfoLabels[index]" 
                            stack-label 
                            dense 
                        />
                    </div>

                    <q-separator />

                    <div class="col col-xs-12 col-sm-12 col-md-12 q-pa-sm q-mt-md">
                        <span class="text-h5">Transaction Details</span>
                    </div>
                    <div class="col col-xs-12 col-sm-6 col-md-4 q-pa-sm">
                        <q-input 
                            outlined 
                            v-model="form.orNumber" 
                            label="OR Number" 
                            stack-label 
                            dense
                            :rules="[ val => val && val.length > 0 || 'This field is required']"
                        />
                    </div>
                    <div class="col col-xs-12 col-sm-6 col-md-4 q-pa-sm">
                        <q-select 
                            outlined 
                            v-model="form.transactionType" 
                            :options="transTypeOption" 
                            label="Type" 
                            stack-label 
                            dense
                            options-dense
                        />
                    </div>
                    <div class="col col-xs-12 col-sm-6 col-md-4 q-pa-sm">
                        <q-input 
                            outlined 
                            type="date"
                            disable
                            v-model="form.orderDate" 
                            label="Order Date" 
                            stack-label 
                            dense
                            :rules="[ val => val && val.length > 0 || 'This field is required']"
                        />
                    </div>

                    
                    <div class="col col-xs-12 col-sm-12 col-md-12 q-pa-sm q-mt-lg">
                        <q-separator spaced />
                        <q-btn 
                            @click="confirmData"
                            :disable="transactions.length === 0"
                            color="secondary"
                            icon-right="print" 
                            label="Submit and Print"
                        />
                    </div>
                </div>

                
            </div>
            
        </q-form>

        <searchProductModal
            :modalStatus="openModal" 
            @updateModalStatus="closeAddModal"
            @submitModalClick="addItemToList"
        />
        <printInvoiceModal
            v-if="printModal"
            :modalStatus="printModal" 
            @updatePrintStatus="closeAddModal"
        />
    </div>
</template>

<script>
import moment from 'moment';
import searchProductModal from '../Modals/SearchProductModal.vue';
import printInvoiceModal from '../Modals/PrintInvoice.vue';
import { LocalStorage } from 'quasar'
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('YYYY-MM-DD');

export default{
    name: 'SalesForm',
    components: {
        searchProductModal,
        printInvoiceModal
    },
    data(){
        return {
            openModal: false,
            printModal: false,
            form: {
                // Sales Section
                customerName: "",
                customerInfo: {
                    address: '',
                    TIN: '',
                    contact: '',
                    busStyle: '',
                    ccNo: '',
                    pwdNo: '',
                },
                orNumber: "",
                transactionType: {label: '', value: ''},
                status: 'Processing',
                orderDate: dateNow,
                empId: '',
                branchId: '',
                transactions: null
            },

            transactions: [],
            customerInfoLabels: {
                address: 'Address',
                TIN: 'TIN #',
                contact: 'Contact #',
                busStyle: 'Bus Style/Name',
                ccNo: 'Credit Card No.',
                pwdNo: 'OSCA/PWD ID No.',
            },
            transTypeOption: [
                {label: 'Walk-In', value: 'Walk-In'},
                {label: 'Delivery', value: 'Delivery'},
                {label: 'Pick-up', value: 'Pick-up'}
            ]
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    methods: {
        openAddModal(){
            this.openModal = true;
        },
        closeAddModal(val){
            this.openModal = val;
            this.printModal = val;
        },
        addItemToList(rowData, qty){
            this.transactions.push({
                itemId: rowData.key,
                itemName: rowData.productName,
                itemSerial: rowData.productSerial,
                itemCount: rowData.qty,
                itemUnit: rowData.unitType,
                qty: qty
            })
        },
        clearItemList(){
            this.$q.dialog({
                title: 'Clear Items',
                message: 'Would you like to re-enter item lists?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                this.transactions = [];
            })
        },
        confirmData(){
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
                        title: 'Submit & Print',
                        message: 'Would you like to finalize your order?',
                        ok: {
                            label: 'Yes'
                        },
                        cancel: {
                            label: 'No',
                            color: 'negative'
                        },
                        persistent: true
                    }).onOk(() => {
                        this.submitData();
                    })
                }
            })
        },
        submitData(){
            this.form = {
                customerName: "",
                customerInfo: {
                    address: '',
                    TIN: '',
                    contact: '',
                    busStyle: '',
                    ccNo: '',
                    pwdNo: '',
                },
                orNumber: "",
                transactionType: {label: '', value: ''},
                status: 'Processing',
                orderDate: dateNow,
                empId: '',
                branchId: '',
                transactions: null
            }
            this.transactions = []
            this.printModal = true
            // api.post('sales/transaction/save', ).then((response) => {
            //     const data = {...response.data};
            //     if(!data.error){
            //         this.$q.notify({
            //             color: 'positive',
            //             position: 'top-right',
            //             title:data.title,
            //             message: data.message,
            //             icon: 'report_problem'
            //         })
            //         this.clearItemList();
            //     } else {
            //         this.$q.notify({
            //             color: 'negative',
            //             position: 'top-right',
            //             title: data.title,
            //             message: data.message,
            //             icon: 'report_problem'
            //         })
            //     }

            // })
        }
    },
    
}
</script>

<style>
.autocomplete{
    background: gainsboro;
}
.autocomplete > div {
    cursor: pointer;
    padding: 5px;
}
.autocomplete > div:hover {
    background: whitesmoke;
}
</style>