<template>
    <div class="q-pa-lg">
        <q-form
            @submit="onSubmit"
            ref="formDetails"
            class="row"
        >
            <div class="col col-xs-12 col-sm-12 col-md-12 text-center">
                <span class="text-h5">GENERAL SURGERY OPERATIVE RECORD</span>
                <q-option-group
                    inline
                    v-model="form.patientType"
                    :options="options"
                    color="red"
                    right-label
                />
            </div>
            
            <q-input
                class="col col-xs-12 col-md-4 text-uppercase q-pa-sm q-mt-sm"
                dense
                v-model="form.firstName"
                label="First Name *"
                hint="Given Name and Suffix (if has)"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            <q-input
                class="col col-xs-12 col-md-4 q-pa-sm text-uppercase q-mt-sm"
                dense
                v-model="form.lastName"
                label="Last Name *"
                hint="Family Name"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            <q-input
                class="col col-xs-12 col-md-4 q-pa-sm text-uppercase q-mt-sm"
                dense
                v-model="form.middleName"
                label="Middle Name"
                hint="Optional"
            />

            <q-input
                class="col col-xs-12 col-md-4 q-pa-sm q-mt-md"
                v-model="form.birthDate"
                type="date" 
                dense
                hint="Birth Date"
            />
            <div class="col col-xs-12 col-md-4 q-pa-sm q-mt-md text-center">
                <q-option-group
                    inline
                    v-model="form.sex"
                    :options="sexOption"
                    color="red"
                    right-label
                />
            </div>

            <q-input
                class="col col-xs-12 col-md-4 q-pa-sm q-mt-md"
                v-model="form.operationDate"
                dense 
                type="date"
                :rules="[ val => val && val.length > 0 || 'Please enter patient operation date']"
                hint="Date of Operation *"
            />
            <q-input
                class="col col-xs-12 col-md-4 text-uppercase q-pa-sm q-mt-md"
                dense
                v-model="form.anesthesia"
                label="Anesthesia *"
                hint="Given Anesthetics"
                lazy-rules
                :rules="[ val => val && val.length > 0 || 'This field is required']"
            />
            <q-input
                class="col col-xs-12 col-md-4 q-pa-sm q-mt-md text-uppercase"
                dense
                v-model="form.patientInfo.hospitalNumber"
                label="Hospital No."
            />
            <!-- Diagnosis Info -->
            <div class="col col-xs-12 col-md-12 text-center q-mt-md"></div>
            <q-input
                class="col col-xs-12 col-md-3 q-pa-sm text-uppercase"
                dense
                v-model="form.surgeon.label"
                input-class="surgeon-autocomp"
                label="Surgeon *"
            />
            <q-input
                class="col col-xs-12 col-md-3 q-pa-sm text-uppercase"
                dense
                v-model="form.assistant.label"
                input-class="assistant-autocomp"
                label="Assistant *"
            />
            <q-input
                class="col col-xs-12 col-md-3 q-pa-sm text-uppercase"
                dense
                input-class="anes-autocomp"
                v-model="form.nursesInfo.anesthesiologist"
                label="Anesthesiologist"
            />
            <q-input
                class="col col-xs-12 col-md-3 q-pa-sm text-uppercase"
                dense
                input-class="consult-autocomp"
                v-model="form.nursesInfo.consultant"
                label="Consultant"
            />

            <div class="col col-xs-12 col-md-12 q-pt-md text-center">
                <span class="text-h5">Diagnosis Information</span>
            </div>
            <!-- <q-input
                class="col col-xs-12 col-md-12 q-pa-sm"
                type="textarea"
                v-model="form.findingsInfo.technique"
                counter
                maxlength="600"
                dense
                autogrow
            /> -->
            <q-input
                class="col col-xs-12 col-md-6 q-pa-sm text-uppercase"
                dense
                v-model="form.diagnosisInfo.preOperative"
                label="Pre-Operative Diagnosis *"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
                counter
                maxlength="600"
                autogrow
            />
            <q-input
                class="col col-xs-12 col-md-6 q-pa-sm text-uppercase"
                dense
                v-model="form.diagnosisInfo.postOperative"
                label="Post-Operative Diagnosis *"
                :rules="[ val => val && val.length > 0 || 'This field is required']"
                counter
                maxlength="600"
                autogrow
            />
            <div class="col col-xs-12 col-md-12 q-mt-md">
                <span class="text-h6">Classification</span>
                <br/>
                <q-option-group
                    inline
                    v-model="form.classification"
                    :options="classifications"
                    color="red"
                    right-label
                />
            </div>
            <div class="col col-xs-12 col-md-12">
                <span class="text-h6">Specialities</span>
                <br/>
                <q-checkbox v-model="form.specialities" v-for="splty in speciality" :key="splty" :val="splty" :label="splty" color="red" />
            </div>

            <div class="col col-xs-12 col-md-12 q-pa-sm">
                <span class="text-subtitle1">Operation Time</span>
            </div>
            <q-input
                class="col col-xs-12 col-md-6 q-pa-sm"
                dense
                type="time"
                v-model="form.diagnosisInfo.operationTime.started"
                mask="##:## AA"
                hint="Time Started"
                fill-mask
            />
            <q-input
                class="col col-xs-12 col-md-6 q-pa-sm"
                dense
                type="time"
                v-model="form.diagnosisInfo.operationTime.ended"
                mask="##:## AA"
                hint="Time Ended"
                fill-mask
            />

            <q-select
                class="col col-xs-12 col-md-12 q-pa-sm q-mt-md"
                dense
                v-model="form.crsPerformed"
                use-input
                input-debounce="0"
                label="Operation Performed"
                :options="crsList"
                @update:model-value="setTechniqueValue"
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

            <div class="col col-xs-12 col-md-12 q-pa-sm">
                <span class="text-h5">Operative Findings</span>
            </div>
            <q-input
                class="col col-xs-12 col-md-12 q-pa-sm"
                v-model="form.findingsInfo.operativeFindings"
                counter
                maxlength="1500"
                dense
                autogrow
            />

            <div class="col col-xs-12 col-md-12 q-pa-sm">
                <span class="text-h5">Technique</span>
            </div>
            <q-input
                class="col col-xs-12 col-md-12 q-pa-sm"
                type="textarea"
                v-model="form.findingsInfo.technique"
                counter
                maxlength="1500"
                dense
                autogrow
            />
            <div class="col col-xs-12 col-md-6 q-pa-sm">
                <span class="text-subtitle2 text-uppercase">pathology:</span>
                <q-radio v-model="form.findingsInfo.isPathology" val="YES" label="YES" />
                <q-radio v-model="form.findingsInfo.isPathology" val="NO" label="NO" />
            </div>
            <div class="col col-xs-12 col-md-6 q-pa-sm">
                <span class="text-subtitle2 text-uppercase">drain:</span>
                <q-radio v-model="form.findingsInfo.isDrain" val="YES" label="YES" />
                <q-radio v-model="form.findingsInfo.isDrain" val="NO" label="NO" />
            </div>
            <div class="col col-xs-12 col-md-6 q-pa-sm">
                <span class="text-subtitle2 text-uppercase">surgical count complete:</span>
                <q-radio v-model="form.findingsInfo.isSurgicalCountComplete" val="YES" label="YES" />
                <q-radio v-model="form.findingsInfo.isSurgicalCountComplete" val="NO" label="NO" />
            </div>
            <div class="col col-xs-12 col-md-6 q-pa-sm">
                <span class="text-subtitle2 text-uppercase">blood transfused:</span>
                <q-radio v-model="form.findingsInfo.isBloodTransfuse" val="YES" label="YES" />
                <q-radio v-model="form.findingsInfo.isBloodTransfuse" val="NO" label="NO" />
            </div>
        </q-form>

        <div class="col col-xs-12 col-sm-12 col-md-12 q-mt-lg">
            <span class="text-h6">UPLOAD ATTACHMENTS</span>
        </div>

        <q-file
            class="col col-xs-12 col-md-12 q-pa-sm  q-mt-sm"
            color="grey-3"
            outlined
            multiple
            accept="*,image/*"
            v-model="fileRacker"
            label=""
            hint="Image Files is only allowed"
        >
            <template v-slot:label>
                <div><q-icon  name="attachment" color="orange" /> Upload Image</div>
            </template>
        </q-file>

        <div class="col col-xs-12 col-sm-12 col-md-12 q-mt-lg">
            <span class="text-h6">UPLOAD PREVIEWS</span>
        </div>
        <div class="col col-xs-12 col-sm-12 col-md-12 q-mt-lg">
            <div v-if="fileGenerated.length != 0" class="q-gutter-sm row items-start">
                <q-img
                    v-for="(img, key) in fileGenerated"
                    :key="key"
                    :src="img.encodedUrl"
                    fit="fill"
                    style="height: 140px; max-width: 150px"
                >
                    <q-icon class="absolute all-pointer-events" @click="removeImageItem(img)" size="32px" name="cancel" color="red" style="top: 8px; left: 8px">
                        <q-tooltip>
                            Remove Image
                        </q-tooltip>
                    </q-icon>
                </q-img>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import autocomplete from 'autocompleter';
import { LocalStorage, dom } from 'quasar'
import jwt_decode from 'jwt-decode'
import { api } from 'boot/axios'

const dateNow = moment().format('YYYY-MM-DD');
const { offset } = dom

export default{
    name: 'ORForm',
    props: {
        savedData: {
            type: Object
        },
        savedId: {
            type: Number
        }
    },
    data(){
        return {
            form: {
                // Patient Section
                lastName: "",
                firstName: "",
                middleName: "",
                birthDate: dateNow,
                sex: "Male",
                operationDate: dateNow,
                anesthesia: "",
                patientType: "OPD",
                patientInfo: {
                    hospitalNumber: ""
                },

                // Surgeon Incharge Section
                surgeon: {label:'', value:''},
                assistant: {label:'', value:''},
                nursesInfo: {
                    consultant: "",
                    anesthesiologist: "",
                },
                classification: "ELECTIVE",
                specialities: [],

                // Diagnosis Section
                diagnosisInfo: {
                    preOperative: "",
                    postOperative: "",
                    operationTime: {
                        started: "",
                        ended: "",
                    }
                },
                crsPerformed: "",
                // Findings Section
                findingsInfo: {
                    operativeFindings: "",
                    technique: "",
                    isPathology: "NO",
                    isDrain: "NO",
                    isSurgicalCountComplete: "NO",
                    isBloodTransfuse: "NO",
                }
                
            },

            fileGenerated: [],
            fileRacker:[],

            // Components for dropdowns
            usersLists: [],
            surgeonList: [],
            assistList: [],
            crsList: [],
            classifications: [
                {
                    label: 'ELECTIVE',
                    value: 'ELECTIVE'
                },
                {
                    label: 'EMERGENCY',
                    value: 'EMERGENCY'
                },
            ],
            speciality: ["GS", "TRAUMA", "HEAD & NECK", "BREAST", "HBT", "COLORECTAL", "MIS", "UROLOGY", "NEURO", "PLASTICS", "PEDIA", "TCVS"],
            options: [
                {
                    label: 'OPD',
                    value: 'OPD'
                },
                {
                    label: 'ER',
                    value: 'ER'
                },
                {
                    label: 'OR',
                    value: 'OR'
                },
                {
                    label: 'WARD',
                    value: 'WARD'
                }
            ],
            sexOption: [
                {
                    label: 'Male',
                    value: 'Male'
                },
                {
                    label: 'Female',
                    value: 'Female'
                }
            ],
        }
    },
    watch:{
        fileRacker: async function(newVal){
            let vm = this;
            for (const [key, value] of Object.entries(newVal)) {
                
                await this.getBase64(value).then((data) => {
                    let fileData = {
                        encodedUrl: data,
                        file: value,
                    };
                    vm.fileGenerated.push(fileData)
                }).catch(error => {
                    this.$q.notify({
                        color: 'negative',
                        position: 'top-right',
                        message: `${value.name} file convert failed.`,
                        icon: 'report_problem'
                    })
                    return;
                });
            }


        }
    },
    computed: {
        isSaveData: function(){
            return this.savedData !== undefined ? true : false;
        },
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    created(){
        // console.log(this.isSaveData)
       
        this.getSurgeonList().then(() => {
            this.getAssistantList().then(() => {
                this.getCRSList().then(() => {
                    this.$nextTick(() => {
                        this.loadSavedData();
                    });
                });
            });
        });
    },
    mounted(){
        let vm = this;
        let surinput = document.getElementsByClassName('surgeon-autocomp');
        let assistinput = document.getElementsByClassName('assistant-autocomp');
        let anesinput = document.getElementsByClassName('anes-autocomp');
        let coninput = document.getElementsByClassName('consult-autocomp');


        autocomplete({
            input: surinput[0],
            fetch: function(text, update) {
                text = text.toLowerCase();
                // you can also use AJAX requests instead of preloaded data
                var suggestions = vm.usersLists.filter(v => v.label.toLowerCase().indexOf(text) > -1)
                update(suggestions);
            },
            onSelect: function(item) {
                vm.form.surgeon = item;
            }
        });
        autocomplete({
            input: assistinput[0],
            fetch: function(text, update) {
                text = text.toLowerCase();
                // you can also use AJAX requests instead of preloaded data
                var suggestions = vm.usersLists.filter(v => v.label.toLowerCase().indexOf(text) > -1)
                update(suggestions);
            },
            onSelect: function(item) {
                vm.form.assistant = item;
            }
        });
        autocomplete({
            input: anesinput[0],
            fetch: function(text, update) {
                text = text.toLowerCase();
                // you can also use AJAX requests instead of preloaded data
                var suggestions = vm.usersLists.filter(v => v.label.toLowerCase().indexOf(text) > -1)
                update(suggestions);
            },
            onSelect: function(item) {
                vm.form.nursesInfo.anesthesiologist = item.label;
            }
        });
        autocomplete({
            input: coninput[0],
            fetch: function(text, update) {
                text = text.toLowerCase();
                // you can also use AJAX requests instead of preloaded data
                var suggestions = vm.usersLists.filter(v => v.label.toLowerCase().indexOf(text) > -1)
                update(suggestions);
            },
            onSelect: function(item) {
                vm.form.nursesInfo.consultant = item.label;
            }
        });
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
        removeImageItem(item){
            let index = this.fileGenerated.indexOf(item);
            if (index !== -1) {
                this.fileGenerated.splice(index, 1);
            }

            let rackIndex = this.fileRacker.indexOf(item.file);
            if (rackIndex !== -1) {
                this.fileRacker.splice(rackIndex, 1);
            }
        },
        setTechniqueValue(val){
            console.log(val)
            let tech = val.techniqueTemplate.replace(/\\n/g, '\n');
            this.form.findingsInfo.technique = tech;
        },
        loadSavedData(){
            // Patient Section
            
            this.form.lastName = this.isSaveData ? this.savedData.lastName : "";
            this.form.firstName = this.isSaveData ? this.savedData.firstName : "";
            this.form.middleName = this.isSaveData ? this.savedData.middleName : "";
            this.form.birthDate = this.isSaveData ? this.savedData.birthDate : dateNow;
            this.form.sex = this.isSaveData ? this.savedData.sex : "Male";
            this.form.operationDate = this.isSaveData ? this.savedData.operationDate : dateNow;
            this.form.anesthesia = this.isSaveData ? this.savedData.anesthesia : "";
            this.form.patientType = this.isSaveData ? this.savedData.patientType : "OPD";
            this.form.patientInfo.hospitalNumber = this.isSaveData ? this.savedData.patientInfo.hospitalNumber : "";
            this.form.surgeon = this.isSaveData ? this.savedData.surgeon : {label:'', value:''};
            this.form.assistant = this.isSaveData ? this.savedData.assistant : {label:'', value:''};
            this.form.nursesInfo.consultant = this.isSaveData ? this.savedData.nursesInfo.consultant : "";
            this.form.nursesInfo.anesthesiologist = this.isSaveData ? this.savedData.nursesInfo.anesthesiologist : "";
            this.form.classification = this.isSaveData ? this.savedData.classification : "ELECTIVE";
            this.form.specialities = this.isSaveData ? this.savedData.specialities : [];
            // Diagnosis Section
            this.form.diagnosisInfo.preOperative = this.isSaveData ? this.savedData.diagnosisInfo.preOperative : "";
            this.form.diagnosisInfo.postOperative = this.isSaveData ? this.savedData.diagnosisInfo.postOperative : "";
            this.form.diagnosisInfo.operationTime.started = this.isSaveData ? this.savedData.diagnosisInfo.operationTime.started : "";
            this.form.diagnosisInfo.operationTime.ended = this.isSaveData ? this.savedData.diagnosisInfo.operationTime.ended : "";
            this.form.crsPerformed = this.isSaveData ? this.savedData.crsPerformed : "";
            // Findings Section
            this.form.findingsInfo.operativeFindings = this.isSaveData ? this.savedData.findingsInfo.operativeFindings : "";
            this.form.findingsInfo.technique = this.isSaveData ? this.savedData.findingsInfo.technique : "";
        },
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
                        label: `${el.lastName}, ${el.firstName} ${el.suffix}`,
                        value: el.id
                    })
                });

                this.usersLists = response.status < 300 ? sList : [];
            });
        },
        async getAssistantList(){
            api.get('misc/getAssistantList').then((response)=>{
                let data = {...response.data}
                let aList = [];

                data.list.forEach((el, key) => {
                    aList.push({
                        label: `${el.lastName}, ${el.firstName} ${el.suffix}`,
                        value: el.id
                    })
                });

                this.assistList = response.status < 300 ? aList : [];
            });
        },


        filterCRSFn (val, update, abort) {
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
                        value: el.rvsCode,
                        techniqueTemplate: `${el.typeTemplate}`
                    })
                });

                this.crsList = response.status < 300 ? sList : [];
            });
        },

        resetForm(){
            this.form = {
                // Patient Section
                lastName: "",
                firstName: "",
                middleName: "",
                birthDate: dateNow,
                sex: "Male",
                operationDate: dateNow,
                anesthesia: "",
                patientType: "OPD",
                patientInfo: {
                    hospitalNumber: ""
                },

                // Surgeon Incharge Section
                surgeon: "",
                assistant: "",
                nursesInfo: {
                    consultant: "",
                    anesthesiologist: "",
                },
                classification: "ELECTIVE",
                specialities: [],

                // Diagnosis Section
                diagnosisInfo: {
                    preOperative: "",
                    postOperative: "",
                    operationTime: {
                        started: "",
                        ended: "",
                    }
                },
                crsPerformed: "",
                // Findings Section
                findingsInfo: {
                    operativeFindings: "",
                    technique: "",
                }
                
            }

            this.fileRacker = []
            this.fileGenerated = []
        }
    },
    
}
</script>

<style>
.autocomplete{
    background: gainsboro;
}
.autocomplete > div {
    cursor: pointer;
    padding: 5px;
}
.autocomplete > div:hover {
    background: whitesmoke;
}
</style>