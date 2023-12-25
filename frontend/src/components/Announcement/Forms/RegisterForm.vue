<template>
    <div class="q-pa-md">
        <q-form
            ref="formDetails"
            class="row"
        >
            <q-select
                class="col col-xs-12 col-md-6 q-pa-sm"
                bottom-slots
                v-model="form.subject" 
                :options="subjects" 
                label="Type"
                dense 
                :options-dense="true"
            >
            </q-select>
            <q-input 
                class="col col-xs-12 col-md-6 q-mt-sm" 
                bottom-slots
                v-model="form.tags"
                label="Announced To" 
                hint="value must be comma separated"
                :dense="true"
            >
                <template v-slot:prepend>
                    <q-icon name="groups" />
                </template>
            </q-input>

            <q-input
                class="col col-xs-12 col-md-12  q-pa-sm q-mt-sm"
                dense
                v-model="form.title"
                label="Title*"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            
            <div class="col col-xs-12 col-md-12 q-pa-md">
                <span class="text-h5">Announcement content*</span>
            </div>
            <q-input
                class="col col-xs-12 col-md-12 q-pa-sm"
                type="textarea"
                v-model="form.description"
                dense
                autogrow
                :rules="[ val => val && val.length > 0 || 'This field is required']"
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
                subject: "",
                tags: "",
                description: "",
                title: "",
                createdBy: "",
            },
            subjects: [
                {
                    label:"Enrollment",
                    value: "Enrollment",
                    icon: "edit_calendar"
                },
                {
                    label:"Announcement",
                    value: "Announcement",
                    icon: "campaign"
                },
                {
                    label:"Notice",
                    value: "Notice",
                    icon: "notification_important"
                },
            ]
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
                subject: "",
                tags: "",
                description: "",
                title: "",
                createdBy: "",
            }
        },
    
        // ending method
    },
    
}
</script>
