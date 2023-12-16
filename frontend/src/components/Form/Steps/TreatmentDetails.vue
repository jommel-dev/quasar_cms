<template>
    <q-form
        ref="formDetails"
        class="row"
    >
        <div class="col col-xs-12 col-md-12 q-pa-sm">
            <span class="text-h5">Treatment Given in the Operating Room</span>
        </div>

        <q-input
            class="col col-xs-12 col-md-12 q-pa-sm"
            dense
            autogrow
            v-model="form.treatmentInfo.beforeOperation"
            label="Before"
        />
        <q-input
            class="col col-xs-12 col-md-12 q-pa-sm"
            dense
            autogrow
            v-model="form.treatmentInfo.duringOperation"
            label="During"
        />

        <q-input
            class="col col-xs-12 col-md-12 q-pa-sm"
            dense
            autogrow
            v-model="form.treatmentInfo.afterOperation"
            label="After"
        />

        <q-select
            class="col col-xs-12 col-md-12 q-pa-sm"
            dense
            v-model="form.crsPerformed"
            use-input
            input-debounce="0"
            label="Operation Performed"
            :options="crsList"
            @filter="filterFn"
        >
            <template v-slot:no-option>
                <q-item>
                    <q-item-section class="text-grey">
                    No Surgeon Found
                    </q-item-section>
                </q-item>
            </template>
        </q-select>

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
            crsList: [],
            options: stringOptions,
            form: {
                treatmentInfo:{
                    beforeOperation: "",
                    duringOperation: "",
                    afterOperation: "",
                },
                crsPerformed: "",
                crsCode: "",
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
    created(){
        this.getCRSList();
    },
    methods: {
        setCRSCode(val){
            console.log(val)
        },
        filterFn (val, update, abort) {
            if (val === '') {
                update(() => {
                   this.getCRSList();
                })
                return
            }
            // call abort() at any time if you can't retrieve data somehow
            setTimeout(() => {
                update(() => {
                    const needle = val.toLowerCase()
                    this.crsList = this.crsList.filter(v => v.toLowerCase().indexOf(needle) > -1)
                })
            }, 1500)
        },
        async getCRSList(){
            api.get('misc/philhealthCrs').then((response)=>{
                let data = {...response.data}
                let sList = [];

                data.list.forEach((el, key) => {
                    sList.push({
                        label: `${el.Description}`,
                        value: el.rvsCode
                    })
                });

                this.crsList = response.status < 300 ? sList : [];
            });
        },
    },
    
}
</script>
