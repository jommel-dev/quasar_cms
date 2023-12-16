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
                    <div class="text-h6">Booking Details</div>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 50vh;" class="q-pt-none scroll">
                    <q-table
                        :rows="productList"
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
                </q-card-section>

                <q-separator />

                <q-card-actions align="right">
                    <q-btn 
                        :disable="productList.length === 0"
                        flat 
                        @click="generateInvoice"
                        label="Generate Invoice" 
                        color="primary" 
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
    name: 'BookingModal',
    data(){
        return {
            openModal: false,
            productList: []
        }
    },
    watch:{
        modalStatus(newVal){
            this.openModal = newVal
            this.productList = this.bookedList
        }
    },
    props: {
        otherDetails: {
            type: Object
        },
        bookedList: {
            type: Array
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
        async closeModal(){
            this.$emit('updateModalStatus', false);
        },
        async generateInvoice(){
            let payload = {
                agentId: this.otherDetails.aid,
                syncId: this.otherDetails.sid,
                customerId: this.otherDetails.cid,
                orderList: JSON.stringify(this.productList),
                status: 'DSIS001',
                otherDetails: JSON.stringify({
                    transType: '',
                    tinNo: '',
                    businessStyle: '',
                    checkNo: '',
                    postdateCheckDate: '',
                    dueDate: '',
                    discountPercentage: 0
                }),
                createdBy: Number(this.user.userId),
            }
            
            api.post('invoice/create/booking', payload).then((response) => {
                const data = {...response.data};
                console.log(response)
                if(!data.error){
                    this.$emit('updateModalStatus', false);
                } else {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title:data.title,
                        message: data.message,
                        icon: 'report_problem'
                    })
                }

            })
        }
    }
    
}
</script>
