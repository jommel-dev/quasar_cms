<template>
    <q-form
        ref="formDetails"
        class="row"
    >
        <div class="col col-xs-12 col-md-12">
            <span class="text-h5">Nurses Information</span>
        </div>
        <q-select
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.surgeon"
            use-input
            input-debounce="0"
            label="Surgeon"
            :options="surgeonList"
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
        <q-select
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.assistant"
            use-input
            input-debounce="0"
            label="Assitant"
            :options="assistList"
            @filter="filterAssistFn"
        >
            <template v-slot:no-option>
                <q-item>
                    <q-item-section class="text-grey">
                    No Surgeon Found
                    </q-item-section>
                </q-item>
            </template>
        </q-select>
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.nursesInfo.anesthesiologist"
            label="Anesthesiologist"
        />
        <div class="col col-md-12"></div>
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.nursesInfo.instrument"
            label="Instrument"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.nursesInfo.suture"
            label="Suture"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm text-uppercase"
            dense
            v-model="form.nursesInfo.circulating"
            label="Circulating"
        />

        <div class="col col-xs-12 col-md-12 q-pt-md">
            <span class="text-h5">Diagnosis Information</span>
        </div>
        <q-input
            class="col col-xs-12 col-md-6 q-pa-sm text-uppercase"
            dense
            v-model="form.diagnosisInfo.preOperative"
            label="Pre-Operative Diagnosis"
        />
        <q-input
            class="col col-xs-12 col-md-6 q-pa-sm text-uppercase"
            dense
            v-model="form.diagnosisInfo.postOperative"
            label="Post-Operative Diagnosis"
        />
        <div class="col col-xs-12 col-md-12">
            <span class="text-h5">Specialities</span>
            <br/>
            <q-checkbox v-model="form.specialities" v-for="splty in speciality" :key="splty" :val="splty" :label="splty" color="red" />
        </div>
        <div class="col col-md-12"></div>
        <div class="col col-xs-12 col-md-12">
            <span class="text-subtitle1">Send to Laboratory:</span>
            <q-radio v-model="form.diagnosisInfo.isSendLab" val="Yes" label="Yes" />
            <q-radio v-model="form.diagnosisInfo.isSendLab" val="No" label="No" />
        </div>
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm"
            dense
            v-model="form.diagnosisInfo.tissueRemove"
            label="Tissue Removed"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm"
            dense
            v-model="form.diagnosisInfo.anestheticUsed"
            label="Anesthetic Used"
        />
        <q-input
            class="col col-xs-12 col-md-4 q-pa-sm"
            dense
            v-model="form.diagnosisInfo.anestheticQty"
            label="Quantity"
        />
        <div class="col col-xs-12 col-md-12 q-pa-sm">
            <span class="text-subtitle1">Anesthetic Time</span>
        </div>
        <q-input
            class="col col-xs-12 col-md-6 q-pa-sm"
            dense
            v-model="form.diagnosisInfo.anestheticTime.started"
            mask="##:## AA"
            fill-mask
            hint="Time Format HH:MM AM/PM"
            label="Started"
        />
        <q-input
            class="col col-xs-12 col-md-6 q-pa-sm"
            dense
            v-model="form.diagnosisInfo.anestheticTime.ended"
            mask="##:## AA"
            hint="Time Format HH:MM AM/PM"
            fill-mask
            label="Ended"
        />
        <div class="col col-xs-12 col-md-12 q-pa-sm">
            <span class="text-subtitle1">Operation Time</span>
        </div>
        <q-input
            class="col col-xs-12 col-md-6 q-pa-sm"
            dense
            v-model="form.diagnosisInfo.operationTime.started"
            mask="##:## AA"
            hint="Time Format HH:MM AM/PM"
            fill-mask
            label="Started"
        />
        <q-input
            class="col col-xs-12 col-md-6 q-pa-sm"
            dense
            v-model="form.diagnosisInfo.operationTime.ended"
            mask="##:## AA"
            hint="Time Format HH:MM AM/PM"
            fill-mask
            label="Ended"
        />
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
            surgeonList: [],
            assistList: [],
            speciality: ["GS", "TRAUMA", "HEAD & NECK", "BREAST", "HBT", "COLORECTAL", "MIS", "UROLOGY", "NEURO", "PLASTICS", "PEDIA", "TCVS"],
            form: {
                surgeon: "",
                assistant: "",
                nursesInfo: {
                    anesthesiologist: "",
                    instrument: "",
                    suture: "",
                    circulating: "",
                },
                specialities: [],
                diagnosisInfo: {
                    preOperative: "",
                    postOperative: "",
                    tissueRemove: "",
                    isSendLab: "No",
                    anestheticUsed: "",
                    anestheticQty: "",
                    anestheticTime: {
                        started: "",
                        ended: "",
                    },
                    operationTime: {
                        started: "",
                        ended: "",
                    },
                },
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
        this.getSurgeonList().then(() => {
            this.getAssistantList();
        });
    },
    methods: {
        getSurgeonId(val){
            this.form.surgeon = val.value;
        },
        filterFn (val, update, abort) {
            if (val === '') {
                update(() => {
                    this.getSurgeonList();
                })
                return
            }
            // call abort() at any time if you can't retrieve data somehow
            setTimeout(() => {
                update(() => {
                    const needle = val.toLowerCase()
                    this.surgeonList = this.surgeonList.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
                })
            }, 1500)
        },
        filterAssistFn (val, update, abort) {
            if (val === '') {
                update(() => {
                    this.getAssistantList();
                })
                return
            }
            // call abort() at any time if you can't retrieve data somehow
            setTimeout(() => {
                update(() => {
                    const needle = val.toLowerCase()
                    this.assistList = this.assistList.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
                })
            }, 1500)
        },
        
        async getSurgeonList(){
            api.get('misc/getSurgeonList').then((response)=>{
                let data = {...response.data}
                let sList = [];

                data.list.forEach((el, key) => {
                    sList.push({
                        label: `${el.firstName} ${el.suffix} ${el.lastName}`,
                        value: el.id
                    })
                });

                this.surgeonList = response.status < 300 ? sList : [];
            });
        },
        async getAssistantList(){
            api.get('misc/getAssistantList').then((response)=>{
                let data = {...response.data}
                let aList = [];

                data.list.forEach((el, key) => {
                    aList.push({
                        label: `${el.firstName} ${el.suffix} ${el.lastName}`,
                        value: el.id
                    })
                });

                this.assistList = response.status < 300 ? aList : [];
            });
        },
    },
    
}
</script>
