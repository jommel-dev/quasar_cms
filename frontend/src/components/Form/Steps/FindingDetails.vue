<template>
    <q-form
        ref="formDetails"
        class="row"
    >
        <div class="col col-xs-12 col-md-12 q-pa-sm">
            <span class="text-h5">Operative Findings</span>
        </div>
        <q-input
            class="col col-xs-12 col-md-12 q-pa-sm"
            v-model="form.operativeFindings"
            dense
            autogrow
        />

        <div class="col col-xs-12 col-md-12 q-pa-sm">
            <span class="text-h5">Technique</span>
        </div>
        <q-input
            class="col col-xs-12 col-md-12 q-pa-sm"
            v-model="form.technique"
            dense
            autogrow
        />

        <div class="col col-xs-12 col-md-6 q-pa-sm">
            <span class="text-subtitle2 text-uppercase">pathology:</span>
            <q-radio v-model="form.isPathology" val="YES" label="YES" />
            <q-radio v-model="form.isPathology" val="NO" label="NO" />
        </div>
        <div class="col col-xs-12 col-md-6 q-pa-sm">
            <span class="text-subtitle2 text-uppercase">drain:</span>
            <q-radio v-model="form.isDrain" val="YES" label="YES" />
            <q-radio v-model="form.isDrain" val="NO" label="NO" />
        </div>
        <div class="col col-xs-12 col-md-6 q-pa-sm">
            <span class="text-subtitle2 text-uppercase">surgical count complete:</span>
            <q-radio v-model="form.isSurgicalCountComplete" val="YES" label="YES" />
            <q-radio v-model="form.isSurgicalCountComplete" val="NO" label="NO" />
        </div>
        <div class="col col-xs-12 col-md-6 q-pa-sm">
            <span class="text-subtitle2 text-uppercase">blood transfused:</span>
            <q-radio v-model="form.isBloodTransfuse" val="YES" label="YES" />
            <q-radio v-model="form.isBloodTransfuse" val="NO" label="NO" />
        </div>

    </q-form>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const stringOptions = [
  'Google', 'Facebook', 'Twitter', 'Apple', 'Oracle'
]

export default{
    name: 'DiagnosisDetails',
    data(){
        return {
            options: stringOptions,
            form: {
                operativeFindings: "",
                technique: "",
                isPathology: "",
                isDrain: "",
                isSurgicalCountComplete: "",
                isBloodTransfuse: "",
            }
        }
    },
    component: {},
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    created(){},
    methods: {
        filterFn (val, update, abort) {
            if (val === '') {
                update(() => {
                    this.options = stringOptions
                })
                return
            }
            // call abort() at any time if you can't retrieve data somehow
            setTimeout(() => {
                update(() => {
                    const needle = val.toLowerCase()
                    this.options = stringOptions.filter(v => v.toLowerCase().indexOf(needle) > -1)
                })
            }, 1500)
        },
        async getSurgeonList(){
            console.log()
        },
    },
    
}
</script>
