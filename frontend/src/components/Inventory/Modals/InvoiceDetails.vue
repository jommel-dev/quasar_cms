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
                    <div class="text-h6">Invoice Details</div>
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
                            <span class="text-h5">Transaction Details</span>
                        </div>
                        <div class="col col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.invoiceNo" 
                                label="Invoice No." 
                                stack-label 
                                dense
                                :rules="[ val => val && val.length > 0 || 'This field is required']"
                            />
                        </div>
                        <div class="col col-xs-12 col-sm-6 col-md-6 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="otherDetails.transType" 
                                :options="transTypeOption" 
                                label="Type of Transaction" 
                                stack-label 
                                dense
                                options-dense
                            />
                        </div>
                        <div class="col col-xs-12 col-sm-6 col-md-6 q-pa-sm">
                            <q-select 
                                outlined 
                                v-model="form.termsOfPayment" 
                                :options="modePaymentOption" 
                                label="termOfPayment" 
                                stack-label 
                                dense
                                options-dense
                            />
                        </div>
                       

                        <div class="col col-md-12">
                            <span class="text-h5">Customer Details</span>
                        </div>
                        <q-card v-if="details.customerInfo" class="my-card q-mb-md col col-md-12" flat bordered>
                            <q-item>
                                <q-item-section avatar>
                                    <q-avatar>
                                        <q-icon name="account_box" size="lg" />
                                    </q-avatar>
                                </q-item-section>

                                <q-item-section>
                                    <q-item-label>{{`${details.customerInfo.storeName}`}}</q-item-label>
                                    <q-item-label caption>
                                        {{ `${details.customerInfo.address} ${genAddress(details.customerInfo.addressInfo)}` }}
                                    </q-item-label>
                                </q-item-section>
                            </q-item>

                            <q-separator />

                            <q-card-section horizontal>
                                <q-card-section>
                                    <span class="text-bold">Contact Person: </span> <br>
                                    <span class="text-bold">Contact Number: </span>
                                </q-card-section>
                                <q-separator vertical />
                                <q-card-section class="col-4">
                                    <span>{{genContact(details.customerInfo.contactPerson).name}}</span><br>
                                    <span>{{genContact(details.customerInfo.contactPerson).contactNum}}</span>
                                </q-card-section>
                            </q-card-section>
                        </q-card>

                        <div class="col col-md-12">
                            <span class="text-h5">Order Details</span>
                            <q-separator />
                        </div>
                        <div class="col col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.gross" 
                                label="Gross Total Amount" 
                                stack-label 
                                dense
                                disable
                            />
                        </div>
                        <div class="col col-xs-6 col-sm-6 col-md-6 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="otherDetails.discountPercentage"
                                @change="computeDiscountedAmount"
                                label="Discount percentage" 
                                stack-label 
                                dense
                            />
                        </div>
                        <div class="col col-xs-6 col-sm-6 col-md-6 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.discount" 
                                label="Discount" 
                                stack-label 
                                dense
                                disable
                            />
                        </div>
                        <div class="col col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                            <q-input 
                                outlined 
                                v-model="form.netAmount" 
                                label="Gross Total Amount" 
                                stack-label 
                                dense
                                disable
                            />
                        </div>
                        <div class="col col-md-12">
                            <q-table
                                :rows="details.orderList"
                                :filter="filter"
                                :columns="tableColumns"
                                row-key="itemName"
                                separator="cell"
                            >
                                <template v-slot:body-cell-total="props">
                                    <q-td :props="props">
                                        {{props.row.qty * props.row.price}}
                                    </q-td>
                                </template>
                            </q-table>
                        </div>
                        
                    </q-form>
                    
                    
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn
                        flat 
                        label="Update" 
                        color="primary"
                        @click="submitModalClick" 
                    />
                    <q-btn
                        flat 
                        label="Print" 
                        color="green"
                        @click="printInvoice" 
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
            discountPercentage: 0,
            form: {
                invoiceNo: '',
                termsOfPayment: '',
                gross: 0,
                discount: 0,
                netAmount: 0
            },
            otherDetails: {
                transType: '',
                tinNo: '',
                businessStyle: '',
                checkNo: '',
                postdateCheckDate: '',
                dueDate: '',
                discountPercentage: 0
            },
            details: {},
            genderOption: [
                {
                    label: 'MALE',
                    value: 'MALE',
                },
                {
                    label: 'FEMALE',
                    value: 'FEMALE',
                },
            ],
            customerInfoLabels: {
                tinNo: 'TIN #',
                contact: 'Contact #',
                busStyle: 'Bus Style/Name',
                ccNo: 'Credit Card No.',
                pwdNo: 'OSCA/PWD ID No.',
            },
            transTypeOption: [
                {label: 'Walk-In', value: 'Walk-In'},
                {label: 'Delivery', value: 'Delivery'},
                {label: 'Pick-up', value: 'Pick-up'}
            ],
            modePaymentOption: [
                {label: 'Cash', value: 'Cash'},
                {label: 'Check', value: 'Check'},
                {label: 'Debt', value: 'Debt'}
            ]
        }
    },
    watch:{
        modalStatus(newVal, oldVal){
            this.openModal = newVal
            if(newVal){
                this.getInvoiceDetails()
            }
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
        },
        tableColumns: function(){
            // ["Business Name", "Address", "Category", "Call", "Remarks", "Action"];
            return [
                {
                    name: 'itemName',
                    required: true,
                    label: 'Item Name',
                    align: 'left',
                    field: row => row.itemName,
                    format: val => `${val}`,
                    sortable: true
                },
                // { name: 'category', label: 'Category', field: 'category', align: 'left' },
                { name: 'price', label: 'Price', field: 'price', align: 'left' },
                { name: 'qty', label: 'QTY', field: 'qty', align: 'left' },
                { name: 'total', label: 'Total', field: 'total', align: 'left' },
            ]
        }
    },

    methods: {
        computeDiscountedAmount(val){
            let percent = Number(val) / 100
            this.form.discount = this.form.gross * percent
            this.form.netAmount = this.form.gross - this.form.discount 
        },
        getInvoiceDetails(){
            this.$q.loading.show();
            let payload = {
                invoiceId: this.appId
            }
            
            api.post('invoice/fetch/byId', payload).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    console.log(data)
                    this.details = data
                    // Fill data
                    this.form.invoiceNo = data.invoiceNo
                    this.form.termsOfPayment = data.termsOfPayment
                    this.otherDetails.transType = data.otherDetails.transType
                    // for Amount Computation
                    let computeTotal = data.orderList.map((el) => {
                        return el.price * el.qty
                    }).reduce((a,b)=>a+b)
                    this.form.gross = computeTotal.toFixed(2)
                    if(data.otherDetails.discountPercentage !== undefined || data.otherDetails.discountPercentage !== "0"){
                        this.otherDetails.discountPercentage = data.otherDetails.discountPercentage
                        this.computeDiscountedAmount(data.otherDetails.discountPercentage)
                    } else {
                        this.computeDiscountedAmount(0)
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

            this.$q.loading.hide();
        },
        genAddress(addInfo){
            let add = JSON.parse(addInfo);
            return `${add.barangay.label} ${add.city.label} ${add.province.label}`;
        },
        genContact(contact){
            let cont = JSON.parse(contact);
            return cont;
        },
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
                        this.updateInvoice();
                    })
                }
            })
            
        },

        async updateInvoice(){
            this.$q.loading.show();
            let payload = {
                invoiceId: this.appId,
                invoiceDetails: {
                    ...this.form,
                    otherDetails: this.otherDetails
                }
            }

            // console.log(payload)

            // return
            
            api.post('invoice/update', payload).then((response) => {
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
        clearForm(){
            this.form = {
                invoiceNo: '',
                termsOfPayment: '',
                gross: 0,
                discount: 0,
                netAmount: 0
            }
            this.otherDetails = {
                transType: '',
                tinNo: '',
                businessStyle: '',
                checkNo: '',
                postdateCheckDate: '',
                dueDate: '',
                discountPercentage: 0
            }
        },
        async printInvoice(){
            let payload = {
                invoiceId: this.appId
            };

            await api.post(`generate/invoice`, payload).then((response) => {
                const data = {...response.data};
                if(!data.error){

                    const link = document.createElement('a');
                    link.id = "downloadResult";
                    link.href = data.urlLink;
                    link.target = "_blank";

                    link.click();
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
