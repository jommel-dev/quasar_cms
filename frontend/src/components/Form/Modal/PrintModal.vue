<template>
    <div>
        <q-dialog
            v-model="openModal"
            persistent
            :maximized="maximizedToggle"
            transition-show="slide-up"
            transition-hide="slide-down"
        >
            <q-card class="bg-primary text-white">
                <q-bar>
                    <q-space />
                    <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
                        <q-tooltip v-if="maximizedToggle" class="bg-white text-primary">Minimize</q-tooltip>
                    </q-btn>
                    <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
                        <q-tooltip v-if="!maximizedToggle" class="bg-white text-primary">Maximize</q-tooltip>
                    </q-btn>
                    <q-btn dense flat icon="close" @click="closeModal">
                        <q-tooltip class="bg-white text-primary">Close</q-tooltip>
                    </q-btn>
                </q-bar>

                <q-card-section>
                    <div class="text-h6">Print Operative Report</div>
                </q-card-section>

                <q-card-section class="q-pt-none" style="height: 900px;">
                    <iframe id="pdf" style="width: 100%; height: 100%; border: none;"></iframe>
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
            maximizedToggle: true,
        }
    },
    watch:{
        modalStatus: function(newVal){
            if(newVal){
                this.openModal = newVal
                this.getPatientDetails(this.appId)
            }
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
    computed: {
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwt_decode(profile);
        }
    },
    methods: {
        async closeModal(){
            this.$emit('updatePrintStatus', false);
        },
        async getPatientDetails(id){
            let vm = this;
            await api.get(`app/getDetails/${id}`).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.createPDF(data)
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
        },

        async createPDF(data){
            const url = 'files/memo_template_2.pdf'
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
            // Create a new PDFDocument
            const pdfDoc = await PDFDocument.load(existingPdfBytes)
            // Embed the Times Roman font
            const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
            // Add a blank page to the document
            const pages = pdfDoc.getPages()
            const fpage = pages[0];
            // Get the width and height of the page
            const { width, height } = fpage.getSize()
            const fontSize = 9

            // Draw a string of text toward the top of the page
            // Birth Date
            fpage.drawText(`${data.birthDate}`, {
                x: 390,
                y: 603,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            // Sex
            if(data.sex == 'Male'){
            fpage.drawText(`x`, {
                x: 360,
                y: 618,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 391,
                y: 618,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            
            //Operation Date
            fpage.drawText(`${data.operationDate}`, {
                x: 130,
                y: 628,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            // Patient Type
            switch (data.patientType) {
            case 'ER':
                fpage.drawText(`x`, {
                x: 38,
                y: 617,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            case 'OR':
                fpage.drawText(`x`, {
                x: 74,
                y: 617,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            case 'OPD':
                fpage.drawText(`x`, {
                x: 112,
                y: 617,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            default:
                // Ward
                fpage.drawText(`x`, {
                x: 156,
                y: 617,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            }


            //Hospital Number
            fpage.drawText(`${data.patientInfo.hospitalNumber.toUpperCase()}`, {
                x: 150,
                y: 603,
                size: fontSize,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Patient Name
            fpage.drawText(`${data.lastName.toUpperCase()}, ${data.firstName.toUpperCase()} ${data.middleName.toUpperCase()}`, {
                x: 135,
                y: 585,
                size: fontSize,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            //Classification 
            if(data.classification == 'ELECTIVE'){
            fpage.drawText(`x`, {
                x: 415,
                y: 554,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 496,
                y: 554,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // First Row
            let splt = data.specialities;
            splt.forEach((el, key) => {
            fpage.drawText(`x`, {
                x: this.getXPos(el),
                y: this.getYPos(el),
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }) 

            //Consultant Name
            fpage.drawText(`${data.nursesInfo.consultant.toUpperCase()}`, {
                x: 145,
                y: 543,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            //Surgeon Name
            fpage.drawText(`${data.surgeon.lastName.toUpperCase()}, ${data.surgeon.firstName.toUpperCase()} ${data.surgeon.suffix} ${data.surgeon.middleName.toUpperCase()}`, {
                x: 145,
                y: 531,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Assistant Name
            fpage.drawText(`${data.assistant.lastName.toUpperCase()}, ${data.assistant.firstName.toUpperCase()} ${data.assistant.suffix.toUpperCase()} ${data.assistant.middleName.toUpperCase()}`, {
                x: 145,
                y: 518,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Anesthologist Name
            fpage.drawText(`${data.nursesInfo.anesthesiologist.toUpperCase()}`, {
                x: 145,
                y: 506,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Type of Anesthesia
            fpage.drawText(`${data.anesthesia.toUpperCase()}`, {
                x: 145,
                y: 494,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })


            //Pre operative
            fpage.drawText(`${data.diagnosisInfo.preOperative}`, {
                x: 180,
                y: 459,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Post operative
            fpage.drawText(`${data.diagnosisInfo.postOperative}`, {
                x: 180,
                y: 447,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Time start operative
            fpage.drawText(`${data.diagnosisInfo.operationTime.started}`, {
                x: 280,
                y: 410,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Time end operative
            fpage.drawText(`${data.diagnosisInfo.operationTime.ended}`, {
                x: 440,
                y: 410,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            //RVS Description
            fpage.drawText(`${data.crsPerformed}`, {
                x: 154,
                y: 435,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //RVS Code
            fpage.drawText(`${data.crsCode}`, {
                x: 130,
                y: 410,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
        
            //Techniques
            fpage.drawText(`${data.findingsInfo.technique}`, {
                x: 43,
                y: 350,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            // Is Pathology
            if(data.findingsInfo.isPathology == 'YES'){
            fpage.drawText(`x`, {
                x: 95,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 132,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // Is Drain
            if(data.findingsInfo.isDrain == 'YES'){
            fpage.drawText(`x`, {
                x: 325,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 363,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // Is isSurgicalCountComplete
            if(data.findingsInfo.isSurgicalCountComplete == 'YES'){
            fpage.drawText(`x`, {
                x: 165,
                y: 85,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 202,
                y: 85,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // Is isBloodTransfuse
            if(data.findingsInfo.isBloodTransfuse == 'YES'){
            fpage.drawText(`x`, {
                x: 386,
                y: 85,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 423,
                y: 85,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }

            //Surgeon Name
            fpage.drawText(`${data.surgeon.firstName} ${data.surgeon.middleName} ${data.surgeon.lastName} ${data.surgeon.suffix}`, {
                x: 388,
                y: 50,
                // x: 420,
                // y: 710,
                size: 14,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })


            // Page 2
            const fpage2 = pages[1];

            //Findings
            fpage2.drawText(`${data.findingsInfo.operativeFindings}`, {
                x: 43,
                y: 620,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 530, 
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            //Surgeon Name
            fpage2.drawText(`${data.surgeon.firstName} ${data.surgeon.middleName} ${data.surgeon.lastName} ${data.surgeon.suffix}`, {
                x: 388,
                y: 70,
                size: 14,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })


            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdf').src = pdfDataUri;

            this.selectLoading = false;
        },

        async createPDFOriginal(data){
            const url = 'files/memo_template.pdf'
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
            // Create a new PDFDocument
            const pdfDoc = await PDFDocument.load(existingPdfBytes)
            // Embed the Times Roman font
            const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
            // Add a blank page to the document
            const pages = pdfDoc.getPages()
            const fpage = pages[0];
            // Get the width and height of the page
            const { width, height } = fpage.getSize()
            const fontSize = 9
            // Draw a string of text toward the top of the page
            // Birth Date
            fpage.drawText(`${data.birthDate}`, {
                x: 400,
                y: 608,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            // Sex
            if(data.sex == 'Male'){
            fpage.drawText(`x`, {
                x: 344,
                y: 629,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 375,
                y: 629,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            
            //Operation Date
            fpage.drawText(`${data.operationDate}`, {
                x: 130,
                y: 620,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            // Patient Type
            switch (data.patientType) {
            case 'ER':
                fpage.drawText(`x`, {
                x: 38,
                y: 610,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            case 'OR':
                fpage.drawText(`x`, {
                x: 74,
                y: 610,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            case 'OPD':
                fpage.drawText(`x`, {
                x: 112,
                y: 610,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            default:
                // Ward
                fpage.drawText(`x`, {
                x: 156,
                y: 610,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
                })
                break;
            }


            //Hospital Number
            fpage.drawText(`${data.patientInfo.hospitalNumber.toUpperCase()}`, {
                x: 150,
                y: 593,
                size: fontSize,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Patient Name
            fpage.drawText(`${data.lastName.toUpperCase()}, ${data.firstName.toUpperCase()} ${data.middleName.toUpperCase()}`, {
                x: 150,
                y: 575,
                size: fontSize,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Consultant Name
            fpage.drawText(`${data.nursesInfo.consultant.toUpperCase()}`, {
                x: 145,
                y: 535,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            //Classification 
            if(data.classification == 'ELECTIVE'){
            fpage.drawText(`x`, {
                x: 415,
                y: 550,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 496,
                y: 550,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // First Row
            let splt = data.specialities;
            splt.forEach((el, key) => {
            fpage.drawText(`x`, {
                x: this.getXPos(el),
                y: this.getYPos(el),
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            })


            //Surgeon Name
            fpage.drawText(`${data.surgeon.lastName.toUpperCase()}, ${data.surgeon.firstName.toUpperCase()} ${data.surgeon.suffix} ${data.surgeon.middleName.toUpperCase()}`, {
                x: 145,
                y: 523,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Assistant Name
            fpage.drawText(`${data.assistant.lastName.toUpperCase()}, ${data.assistant.firstName.toUpperCase()} ${data.assistant.suffix.toUpperCase()} ${data.assistant.middleName.toUpperCase()}`, {
                x: 145,
                y: 510,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Anesthologist Name
            fpage.drawText(`${data.nursesInfo.anesthesiologist.toUpperCase()}`, {
                x: 145,
                y: 498,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Type of Anesthesia
            fpage.drawText(`${data.anesthesia.toUpperCase()}`, {
                x: 145,
                y: 485,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })


            //Pre operative
            fpage.drawText(`${data.diagnosisInfo.preOperative}`, {
                x: 180,
                y: 447,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Post operative
            fpage.drawText(`${data.diagnosisInfo.postOperative}`, {
                x: 180,
                y: 433,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Time start operative
            fpage.drawText(`${data.diagnosisInfo.operationTime.started}`, {
                x: 280,
                y: 395,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Time end operative
            fpage.drawText(`${data.diagnosisInfo.operationTime.ended}`, {
                x: 440,
                y: 395,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            //RVS Description
            fpage.drawText(`${data.crsPerformed}`, {
                x: 154,
                y: 420,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //RVS Code
            fpage.drawText(`${data.crsCode}`, {
                x: 130,
                y: 395,
                size: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            //Findings
            fpage.drawText(`${data.findingsInfo.operativeFindings}`, {
                x: 43,
                y: 340,
                size: 9,
                spacing: 1,
                lineHeight: 11,
                maxWidth: 530, 
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            //Techniques
            fpage.drawText(`${data.findingsInfo.technique}`, {
                x: 43,
                y: 235,
                size: 9,
                cellCount: 3,
                maxWidth: 530,
                lineHeight: 11,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            // Is Pathology
            if(data.findingsInfo.isPathology == 'YES'){
            fpage.drawText(`x`, {
                x: 95,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 132,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // Is Drain
            if(data.findingsInfo.isDrain == 'YES'){
            fpage.drawText(`x`, {
                x: 325,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 363,
                y: 96,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // Is isSurgicalCountComplete
            if(data.findingsInfo.isSurgicalCountComplete == 'YES'){
            fpage.drawText(`x`, {
                x: 165,
                y: 86,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 202,
                y: 86,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }
            // Is isBloodTransfuse
            if(data.findingsInfo.isBloodTransfuse == 'YES'){
            fpage.drawText(`x`, {
                x: 386,
                y: 86,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            } else {
            fpage.drawText(`x`, {
                x: 423,
                y: 86,
                size: 9,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })
            }

            //Surgeon Signatory
            // const pngUrl = data.surgeon.eSignature;
            // const pngImageBytes = await fetch(pngUrl).then((res) => res.arrayBuffer());
            // const signImage = await pdfDoc.embedPng(pngImageBytes)
            // const signImageDims = signImage.scale(0.5)
            
            // fpage.drawImage(signImage, {
            //     x: 400,
            //     y: 37,
            //     // x: 400,
            //     // y: 690,
            //     width: signImageDims.width,
            //     height: signImageDims.height,
            // })
            //Surgeon Name
            fpage.drawText(`${data.surgeon.firstName} ${data.surgeon.middleName} ${data.surgeon.lastName} ${data.surgeon.suffix}`, {
                x: 388,
                y: 50,
                // x: 420,
                // y: 710,
                size: 14,
                font: timesRomanFont,
                color: rgb(0, 0, 0),
            })

            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdf').src = pdfDataUri;

            this.selectLoading = false;
        },

        getXPos(special){
            let firstRow = "GS,BREAST,MIS,PLASTICS";
            let secondRow = "TRAUMA,HBT,UROLOGY,PEDIA";
            let thirdtRow = "HEAD & NECK,COLORECTAL,NEURO,TCVS";

            if(firstRow.includes(special)){
                return 325
            }
            if(secondRow.includes(special)){
                return 415
            }
            if(thirdtRow.includes(special)){
                return 496
            }
        },
        getYPos(special){
            let firstRow = "GS,TRAUMA,HEAD & NECK";
            let secondRow = "BREAST,HBT,COLORECTAL";
            let thirdtRow = "MIS,UROLOGY,NEURO";
            let fourthtRow = "PLASTICS,PEDIA,TCVS";

            switch (true) {
                case firstRow.includes(special):
                return 530;
                case secondRow.includes(special):
                return 520
                case thirdtRow.includes(special):
                return 509
                case fourthtRow.includes(special):
                return 498
                default:
                return 0
            }
        },

        // ending method
    },
    
}
</script>
