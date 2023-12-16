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
                    <div class="text-h6">Print Invoice</div>
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
    props: {
        appId: {
            type: Number
        },
        modalStatus: {
            type: Boolean
        }
    },
    watch:{
        modalStatus(newVal){
            this.openModal = newVal
        }
    },
    created(){
        this.createPDF()
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
        async createPDF(){
            const url = 'files/invoiceSample.pdf'
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
            // Create a new PDFDocument
            const pdfDoc = await PDFDocument.load(existingPdfBytes)
            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdf').src = pdfDataUri;
        }
        // ending method
    },
    
}
</script>
