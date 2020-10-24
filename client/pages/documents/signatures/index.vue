<template>
    <div class="documents-page-content">
        <h2 style="margin-top: 0.5rem; margin-bottom: 0.5rem;">Create Signatures</h2>

        <div id="signature-tabs">
            <div id="signature-tabs-content">
                <div class="tab-navigation-container">
                    <div class="tab-navigation" 
                        :class="{active: activeTab == index}" 
                        v-for="(nav, index) in tabNavigation" 
                        :key="nav" @click="activeTab = index">
                            {{nav}}
                        </div>
                </div>
                <div class="tab-active-content">
                    <div v-show="activeTab == 0">
                        <div v-if="signatures.length <= 0">
                            <h3 style="font-weight: normal; color: black;">YOU DO NOT HAVE ANY SAVED SIGNATURES</h3>
                            <p>Select an option from the top to create a new signature.</p>
                        </div>
                        <div v-else class="saved-signatures-container">
                            Saved Signatures
                            <div class="signatures-container">
                                <div class="signature-display" 
                                    v-for="signature in signatures" 
                                    :key="signature.id"
                                    :style="{backgroundImage: 'url(\''+signature.image+'\')'}"></div>
                            </div>
                        </div>
                    </div>
                    <div v-show="activeTab == 1">
                        Type it in
                    </div>
                    <div v-show="activeTab == 2">
                        <canvas id="signature-canvas"></canvas>
                        <button id="signature-clear-button" @click="clearCanvas()">Clear</button>

                        <div class="signature-save-container">
                            <div style="font-size: 0.8rem">
                                <input type="checkbox" name="" id="">
                                I understand this is a legal representation of my signature
                            </div>
                            <button class="signature-save-button" ref="saveButton" @click="saveSignature()">Save</button>
                        </div>
                    </div>
                    <div v-show="activeTab == 3">
                        <h4>Upload a picture of your signature</h4>
                        <div>
                            <div class="upload-file-container">
                                <button ref="uploadFileButton" class="upload-file-button" @click.prevent="initiateFileUpload()"><div class="upload-file-image"></div> <span style="margin-left: 0.5rem;"> Upload File</span></button>
                                <input type="file" hidden ref="fileUploadField" @change="uploadFile()">
                            </div>
                        </div>

                        <h5 style="color:black">Maximum file size: 500KB (png, jpg, jpeg and svg formats)</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SignaturePad from "signature_pad";

export default {
    layout: "document",
    fetch({store, $axios}){
        if(store.state.signatures.list <= 0){
            return new Promise((resolve, reject) => {
                $axios.get("/api/signatures/get-all")
                        .then(response => {
                            store.commit("signatures/fetchAllSignatures", response.data)
                            resolve()
                        })
            })
        }
    },
    data(){
        return {
            activeTab: 0,
            tabNavigation: [
                "Saved Signatures",
                "Type It In",
                "Draw It In",
                "Upload Image"
            ],
            signaturePad: null,
            canvas: null
        }
    },
    mounted(){
        this.canvas = document.getElementById("signature-canvas")
        this.signaturePad = new SignaturePad(this.canvas)
        this.signaturePad.dotSize = 1;
        // this.signaturePad.penColor = "rgb(66, 133, 244)";
        window.onresize = this.resizeCanvas()
        this.resizeCanvas()
    },
    computed: {
        signatures(){
            return this.$store.state.signatures.list
        }
    },
    methods: {
        resizeCanvas() {
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
            this.canvas.width = this.canvas.offsetWidth * ratio;
            this.canvas.height = this.canvas.offsetHeight * ratio;
            this.canvas.getContext("2d").scale(ratio, ratio);
            this.signaturePad.clear(); // otherwise isEmpty() might return incorrect value
        },
        clearCanvas(){
            this.signaturePad.clear();
        },
        saveSignature(){
            if(this.signaturePad.isEmpty())
                return
            let x = this.signaturePad.toDataURL();
            let self = this

            let button = this.$refs.saveButton
            this.$URLToFile(x, 'signature.png','image/png')
                .then(function(file){ 
                    let formData = new FormData()
                    formData.append("name", "Signature")
                    formData.append("file", file)

                    button.setAttribute("disabled", "true")
                    button.classList.add("loading")
                    // debugger

                    let attempt = self.$store.dispatch("signatures/uploadSignature", formData)
                    attempt.then(() => {
                        self.signaturePad.clear();
                        button.classList.add("success")
                    }).catch((err) => {
                        // console.log(err)
                        button.classList.add("error")
                    }).finally(() => {
                        // debugger
                        button.removeAttribute("disabled")
                        button.classList.remove("loading")

                        setTimeout(() => {
                            button.classList.remove("error")
                            button.classList.remove("success")
                            button.classList.remove("loading")
                        }, 2000);
                    })
                });
        },
        initiateFileUpload(){
            this.$refs.fileUploadField.click()
        },
        uploadFile(){
            let formData = new FormData()
            formData.append("name", this.$refs.fileUploadField.files[0].name.replace(/(\.[^.]*)$/, ""))
            formData.append("file", this.$refs.fileUploadField.files[0])

            let button = this.$refs.uploadFileButton

            button.classList.add("loading")

            let attempt = this.$store.dispatch("signatures/uploadSignature", formData)

            attempt.then(() => {
                button.classList.add("success")
            }).catch(() => {
                button.classList.add("error")
            }).finally(() => {
                button.classList.remove("loading")

                setTimeout(() => {
                    button.classList.remove("error")
                    button.classList.remove("success")
                    button.classList.remove("loading")
                }, 2000);
            })
        }
    },
    updated(){
        if(this.canvas)
            this.resizeCanvas()
    }
}
</script>

<style scoped>
    .documents-page-content{
        color: #7574A0;
    }

    #signature-tabs{
        height: calc(var(--viewport-height, 100vh) - 150px);
        width: 100%;
    }

    #signature-tabs-content{
        height: 100%;
        width: 100%;
        overflow: hidden;
        border-radius: 10px;
        background: rgba(255,255,255, 0.6);
    }

    .tab-navigation-container{
        display: flex;
        align-items: center;
        background: rgba(240, 240, 240, 0.6);
    }

    .tab-active-content{
        padding: 1rem;
        overflow-y: auto;
    }

    .tab-navigation{
        text-transform: capitalize;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 40px;
        color: #797979;
        font-size: 0.8rem;
        cursor: pointer;
    }

    .tab-navigation.active{
        color: #FF5B45;
        border-bottom: 2px solid #FF5B45;
        background: rgba(255, 91, 69, 0.0784313725490196);;
    }

    #signature-canvas{
        width:100%;
        height: 250px;
        border: #9b9b9b 1px solid;
        border-radius: 5px;
    }

    #signature-clear-button{
        height: 35px;
        font-size: 0.9rem;
        color: #9b9b9b;
        border: 1.2px solid #9b9b9b;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .signature-save-container{
        margin-top: 3rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .signature-save-button{
        margin-left: 3rem;
        height: 35px;
        padding-left: 1.2rem;
        padding-right: 1.2rem;
        background: #0084ff;
        border:none;
        border-radius: 5px;
        text-transform: uppercase;
        color: white;
    }

    .signature-save-button.error{
        background: #e86262;
    }

    .signature-save-button.loading{
        background: #fcd462;
    }

    .signature-save-button.success{
        background: #7ed202;
    }

    .signatures-container{
        margin-top: 1.5rem;
        display: flex;
        flex-wrap: wrap;
    }

    .signature-display{
        height: 100px;
        width: 150px;
        border: 1px solid #9b9b9b;
        border-radius: 5px;
        background-position: center center;
        background-size: contain;
        background-repeat: no-repeat;
        margin: 0.5rem;
        box-shadow: 1px 1px 16px rgba(240, 240, 240, 0.6);
    }

    .upload-file-container{
        display: flex;
        justify-content: flex-start;
        align-items: center;
    }

    .upload-file-button{
        height: 30px;
        font-size: 0.8rem;
        background: #0084ff;
        color: white;
        border: none;
        cursor: pointer;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 5px;
    }

    .upload-file-button.error{
        background: #e86262;
    }

    .upload-file-button.loading{
        background: #fcd462;
    }

    .upload-file-button.success{
        background: #7ed202;
    }

    .upload-file-image{
        height:20px;
        width: 20px;
        display: inline-block;
        background-image: url("/png/cloud-upload.png");
        background-repeat: no-repeat;
        background-position: center center;
        background-size: contain;
    }
</style>