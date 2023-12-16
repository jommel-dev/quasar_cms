<template>
    <div class="q-pa-md">
        <q-form
            ref="formDetails"
            class="row"
        >
            <div class="col col-xs-12 col-sm-12 col-md-12">
                <span class="text-h6">ACCOUNT CREDENTIAL</span>
            </div>
            <q-input 
                class="col col-xs-12 col-md-6 q-pa-sm q-mt-sm" 
                bottom-slots
                v-model="form.username"
                :label="$t('form_labels.user')" 
                :dense="true"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            >
                <template v-slot:prepend>
                    <q-icon name="account_circle" />
                </template>
                <template v-slot:append>
                    <q-icon name="close" @click="form.username = ''" class="cursor-pointer" />
                </template>
            </q-input>

            <q-input 
                class="col col-xs-12 col-md-6 q-pa-sm q-mt-sm" 
                bottom-slots 
                v-model="form.password"
                :type="isPwd ? 'password' : 'text'"
                :label="$t('form_labels.pass')" 
                :dense="true"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            >
                <template v-slot:prepend>
                    <q-icon name="password" />
                </template>
                <template v-slot:append>
                    <q-icon
                        :name="isPwd ? 'visibility_off' : 'visibility'"
                        class="cursor-pointer"
                        @click="isPwd = !isPwd"
                    />
                </template>
            </q-input>





            <div class="col col-xs-12 col-sm-12 col-md-12 q-mt-lg">
                <span class="text-h6">USER DETAILS</span>
            </div>
            
            <q-input
                class="col col-xs-12 col-md-4  q-pa-sm q-mt-sm"
                dense
                v-model="form.firstName"
                label="First Name *"
                hint="Given Name and Suffix (if has)"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            <q-input
                class="col col-xs-12 col-md-4 q-pa-sm  q-mt-sm"
                dense
                v-model="form.lastName"
                label="Last Name *"
                hint="Family Name"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            <q-input
                class="col col-xs-12 col-md-4 q-pa-sm  q-mt-sm"
                dense
                v-model="form.middleName"
                label="Middle Name"
                hint="Optional"
            />

            <q-input
                class="col col-xs-12 col-md-6 q-pa-sm  q-mt-sm"
                dense
                type="email"
                v-model="form.email"
                label="Email Address"
            />
            <q-input
                class="col col-xs-12 col-md-6 q-pa-sm  q-mt-sm"
                dense
                v-model="form.contact"
                label="Contact Number"
            />

            <q-select
                class="col col-xs-12 col-md-6 q-pa-sm"
                bottom-slots
                v-model="form.sex" 
                :options="sexOption" 
                label="Sex"
                dense 
                :options-dense="true"
            >
                <template v-slot:prepend>
                    <q-icon :name="form.sex === 'Male' ? `male` : `female`" @click.stop />
                </template>
            </q-select>

            <q-select
                class="col col-xs-12 col-md-6 q-pa-sm "
                dense
                v-model="form.userType"
                use-input
                input-debounce="0"
                label="User Type"
                :options="typeList"
            />


            <!-- <div class="col col-xs-12 col-sm-12 col-md-12 q-mt-lg">
                <span class="text-h6">USER E-SIGNATURE</span>
            </div> -->

            <!-- <q-file 
                class="col col-xs-12 col-md-12 q-pa-sm  q-mt-sm"
                color="grey-3" 
                outlined
                v-model="fileRacker" 
                label="Upload E-Signature File" 
                hint="PNG File is only allowed"
            >
                <template v-slot:append>
                    <q-icon name="attachment" color="orange" />
                </template>
            </q-file> -->

        </q-form>
    </div>
</template>
<script>
import moment from 'moment';
import { LocalStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'RegisterForm',
    data(){
        return {
            isPwd: true,
            form: {
                username: "",
                password: "",
                lastName: "",
                firstName: "",
                middleName: "",
                sex: "Male",
                userType: '',
                email: '',
                contact: '',
                eSignature: ''
            },
            fileRacker: '',
            typeList: [],
        }
    },
    watch:{
        fileRacker: async function(newVal){
            let vm = this;
            console.log(newVal, newVal.type != 'image/png')
            if(newVal.type != 'image/png'){
                this.$q.notify({
                    color: 'negative',
                    position: 'top-right',
                    message: `Wrong file format`,
                    icon: 'report_problem'
                })
                this.fileRacker = '';
                return false;
            }

            await this.getBase64(newVal).then((data) => {
                vm.form.eSignature = data
            }).catch(error => {
                this.$q.notify({
                    color: 'negative',
                    position: 'top-right',
                    message: `${newVal.name} file convert failed.`,
                    icon: 'report_problem'
                })
                return;
            });

        },
    },
    props: {
        appId: {
            type: Number
        },
        userDetails: {
            type: Object
        }
    },
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        },
        sexOption: function(){
            let options = this.$t('field_options.sex').split(',');
            return options;
        },
    },
    created(){
        this.getTypeList();
    },
    methods: {
        async getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
        },
        async getTypeList(){
            api.get('misc/userTypes').then((response)=>{
                let data = {...response.data}
                let sList = [];

                data.list.forEach((el, key) => {
                    sList.push({
                        label: el.description,
                        value: el.id
                    })
                });

                this.typeList = response.status < 300 ? sList : [];
            });
        },
        resetForm(){
            this.form = {
                username: "",
                password: "",
                lastName: "",
                firstName: "",
                middleName: "",
                sex: "Male",
                userType: '',
                email: '',
                contact: '',
                eSignature: ''
            }
        }

        // ending method
    },
    
}
</script>
