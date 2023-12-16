<template>
    <q-form
        @submit="onSubmit"
        ref="formDetails"
        class="row"
    >
        <div class="col col-xs-12 col-sm-12 col-md-12">
            <span class="text-h5">Patient Information</span>
        </div>
        
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm"
            dense
            v-model="form.firstName"
            label="First Name *"
            hint="Given Name and Suffix (if has)"
            :rules="[ val => val && val.length > 0 || 'Please type something']"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.lastName"
            label="Last Name *"
            hint="Family Name"
            :rules="[ val => val && val.length > 0 || 'Please type something']"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.middleName"
            label="Middle Name"
            hint="Optional"
        />
        <div class="col col-md-12"></div>
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm"
            v-model="form.birthDate"
            dense
            mask="##/##/####"
            hint="Birth Date (MM/DD/YYYY)"
            @change="computeAge"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.age"
            label="Age"
            disable
            hint="This field is auto compute"
        />
        <q-select
            class="col col-xs-12 col-md-4 q-pa-sm"
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

        <div class="col col-md-12"></div>

        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm"
            v-model="form.admittedDate"
            dense 
            mask="##/##/####"
            :rules="[ val => val && val.length > 0 || 'Please enter patient admission date']"
            hint="Date of Admission(MM/DD/YYYY) *"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm"
            v-model="form.operationDate"
            dense 
            mask="##/##/####"
            :rules="[ val => val && val.length > 0 || 'Please enter patient operation date']"
            hint="Date of Operation(MM/DD/YYYY) *"
        />
        <q-input
            class="col col-xs-12 col-md-4 text-uppercase q-pa-sm"
            dense
            v-model="form.anesthesia"
            label="Anesthesia *"
            hint="Given Anesthetics"
            lazy-rules
            :rules="[ val => val && val.length > 0 || 'Please type something']"
        />

        <div class="col col-xs-12 col-md-12 q-pt-md">
            <span class="text-h5">Room Details</span>
            <q-option-group
            inline
                v-model="form.patientType"
                :options="options"
                color="red"
                right-label
            />
        
        </div>

        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.patientInfo.patientNo"
            label="Patient No."
            hint="Optional"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            :disable="form.patientType != 'WARD'"
            v-model="form.patientInfo.wardNo"
            label="Ward No."
            hint="Optional"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.patientInfo.roomNo"
            label="Room No."
            hint="Optional"
        />
        
    </q-form>
</template>

<script>
import moment from 'moment';
import { LocalStorage, SessionStorage, Platform } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('MM/DD/YYYY');

export default{
    name: 'PatientDetails',
    data(){
        return {

            form: {
                lastName: "",
                firstName: "",
                middleName: "",
                birthDate: dateNow,
                age: 0,
                sex: "Male",
                admittedDate: dateNow,
                operationDate: dateNow,
                anesthesia: "",
                patientType: "er",
                patientInfo: {
                    patientNo: "",
                    wardNo: "",
                    roomNo: "",
                },
            },
            options: [
                {
                    label: 'EMERGENCY',
                    value: 'EMERGENCY'
                },
                {
                    label: 'ELECTIVE',
                    value: 'ELECTIVE'
                },
                {
                    label: 'OR',
                    value: 'OPERATING ROOM'
                },
                {
                    label: 'OPD',
                    value: 'OUT PATIENT'
                },
                {
                    label: 'WARD',
                    value: 'WARD'
                }
            ]
        }
    },
    component: {},
    computed: {
        sexOption: function(){
            let options = this.$t('field_options.sex').split(',');
            return options;
        },
    },
    created(){
        
    },
    methods: {
        emitValues(){
            let val = this.form;
            this.$emit("setComponentValues", val);
        },
        onSubmit(){
            console.log('form submitted')
        },
        computeAge(){
            var today = new Date();
            var birthDate = new Date(this.form.birthDate);
            var age = today.getFullYear() - birthDate.getFullYear();
            var m = today.getMonth() - birthDate.getMonth();
            if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) 
            {
                age--;
            }
            
            this.form.age = age;
        }
    },
    
}
</script>
