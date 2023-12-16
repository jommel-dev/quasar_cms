<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card style="width: 900px; max-width: 80vw;" >
                <q-bar class="q-mb-md">
                    <div class="text-h6">Search for Product</div>
                    <q-space />
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section style="max-height: 70vh; height: 100%;" class=" scroll">
                    <q-table
                        :rows="tableRow"
                        :filter="searchInput"
                        :columns="tableColumns"
                        row-key="productName"
                    >
                        <template v-slot:top-right>
                            <q-form
                                ref="formDetails"
                                class="row"
                            >   
                                <q-input
                                    dense
                                    v-model="itemQty" 
                                    placeholder="Item Quantity"
                                    class="q-ml-md"
                                    :rules="[ val => val && val.length > 0 || 'This field is required']"
                                >
                                    <template v-slot:append>
                                        <q-icon name="tag" />
                                    </template>
                                </q-input>
                            </q-form>
                        </template>
                        <template v-slot:top-left>
                            <q-input 
                                dense 
                                debounce="300" 
                                v-model="searchInput" 
                                placeholder="Search"
                                class="q-ml-md"
                            >
                                <template v-slot:append>
                                    <q-icon name="search" />
                                </template>
                            </q-input>
                        </template>
                        <template v-slot:body-cell-actions="props">
                            <q-td :props="props">
                                <q-btn 
                                    dense
                                    flat
                                    outline
                                    size="md"
                                    color="primary" 
                                    label="Select"
                                    @click="submitModalClick(props.row)"
                                />
                            </q-td>
                        </template>
                    </q-table>
                </q-card-section>
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
            searchInput: '',
            itemQty: '',
            tableRow: []
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
    watch: {
        modalStatus(newVal){
            this.openModal = newVal
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        tableColumns: function(){
            return [
                {
                    name: 'productName',
                    required: true,
                    label: 'Product Name',
                    align: 'left',
                    field: row => row.productName,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'unitType', label: 'Unit Type', field: 'unitType' },
                { name: 'qty', label: 'Quantity', field: 'qty' },
                { name: 'actions', label: 'Action', field: 'actions' }
            ]
        }
    },
    created(){
        this.getProducts();
    },
    methods: {
        async closeModal(){
            this.$emit('updateModalStatus', false);
        },
        async submitModalClick(rowData){
            this.$refs.formDetails.validate().then(success => {
                if(!success){
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        title: 'Incomplete Details',
                        message: 'Please fill the required fields',
                        icon: 'report_problem'
                    })
                    return false;
                } else {
                    // Confirm
                    this.$emit('updateModalStatus', false);
                    this.$emit('submitModalClick', rowData, this.itemQty);
                    this.itemQty = '';
                }
            })
            
        },
        getProducts(){
            api.post('product/get/products').then((response) => {
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
        }
    }
    
}
</script>
