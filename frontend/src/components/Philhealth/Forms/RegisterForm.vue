<template>
    <div class="q-pa-md">
        <q-form
            ref="formDetails"
            class="row"
        >
            <div class="col col-xs-12 col-sm-12 col-md-12">
                <span class="text-h6">CRS DETAILS</span>
            </div>
            <q-input 
                class="col col-xs-12 col-md-6 q-pa-sm q-mt-sm" 
                bottom-slots
                v-model="form.rvsCode"
                label="RVS Code" 
                :dense="true"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            >
                <template v-slot:prepend>
                    <q-icon name="code" />
                </template>
                <template v-slot:append>
                    <q-icon name="close" @click="form.username = ''" class="cursor-pointer" />
                </template>
            </q-input>

            <q-input
                class="col col-xs-12 col-md-12  q-pa-sm q-mt-sm"
                dense
                v-model="form.Description"
                label="Description *"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            
            <div class="col col-xs-12 col-md-12 q-pa-md">
                <span class="text-h5">Technique Template</span>
            </div>
            <q-input
                class="col col-xs-12 col-md-12 q-pa-sm"
                type="textarea"
                v-model="form.typeTemplate"
                counter
                maxlength="600"
                dense
                autogrow
            />
        </q-form>
    </div>
</template>
<script>
import { LocalStorage } from 'quasar'
import jwt_decode from 'jwt-decode'

export default{
    name: 'RegisterCRSForm',
    data(){
        return {
            form: {
                rvsCode: "",
                Description: "",
                typeTemplate: "",
            }
        }
    },
    props: {
        appId: {
            type: Number
        },
        crsDetails: {
            type: Object
        }
    },
    computed: {
        hasData: function(){
            return Object.keys(this.crsDetails).length !== 0 ? true : false;
        },
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    watch:{
        crsDetails: function(newVal){
            this.loadSavedData();
        }
    },
    // created(){
    //     this.loadSavedData();
    // },
    methods: {
        async loadSavedData(){
            this.form.rvsCode = this.hasData ? this.crsDetails.rvsCode : "";
            this.form.Description= this.hasData ? this.crsDetails.Description : "";
            this.form.typeTemplate = this.hasData ? this.crsDetails.typeTemplate : "";
        },
        resetForm(){
            this.form = {
                rvsCode: "",
                Description: "",
                typeTemplate: "",
            }
        },
       

        // ending method
    },
    
}
</script>
