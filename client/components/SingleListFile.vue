<template>
    <div class="list-item" ref="currentSingleFile">
        <div class="file-icon" :class="classes"></div>
        <div><span>{{fileName}}</span></div>
        <div><span>{{file.size || "-"}}</span></div>
        <div><span>{{formattedDate}}</span></div>
        <div class="file-option-icon" ref="dropdownButton" @click="showTopIconDropDown=true">
        </div>
        <div class="dropdown" ref="dropdown" v-show="showTopIconDropDown">
            <ul v-if="isInTrash">
                <li @click="restoreDocument()">Restore</li>
                <li @click="permanentlyDeleteDocument()">Delete Permanently</li>
            </ul>
            <ul v-else>
                <li @click="openDocument()">Open</li>
                <li class="demacation"></li>
                <li>Download</li>
                <!-- <li>Convert</li> -->
                <li @click="showShareDocumentModal()">Share</li>
                <li @click="showDocumentSignatureRequestForm()" v-if="canBeSigned">Signature Request</li>
                <li class="demacation"></li>
                <li @click="openCopyToModal()">Copy To</li>
                <li @click="openMoveToModal()">Move To</li>
                <li class="demacation"></li>
                <li @click="deleteDocument()">Delete</li>
            </ul>
        </div>

        <!-- Other stuffs -->
        <DocumentSignatureRequestForm v-if="canBeSigned" :file="file" ref="documentSignatureRequestForm" @noPriviledge="noSigningPriviledge()" @hide="hideDocumentSignatureRequestForm()" />

        <div class="signature-plans-modal" v-if="canBeSigned && showingSignaturePlans">
            <div class="signature-plans-modal-content">
            <SignaturePlans @closed="hideSignaturePlans()" />
            </div>
        </div>

        <share-document-modal :document_id="file.id" @closed="renderShareDocumentModal = false" v-if="renderShareDocumentModal"></share-document-modal>

        <folder-tree-modal ref="copyToModal" @submitted="commitCopyTo">
            <template v-slot:headerText>Copy To:</template>
            <template v-slot:submitButtonText>Copy</template>
        </folder-tree-modal>

        <folder-tree-modal ref="moveToModal" @submitted="commitMoveTo">
            <template v-slot:headerText>Move To:</template>
            <template v-slot:submitButtonText>Move</template>
        </folder-tree-modal>
    </div>
</template>

<script>
    import ShareDocumentModal from "~/components/ShareDocumentModal.vue"
    import FolderTreeModal from "~/components/FolderTreeModal.vue"
    import Modal from "~/components/Modal.vue"
    import DocumentSignatureRequestForm from "~/components/DocumentSignatureRequestForm.vue";
    import SignaturePlans from "~/components/SignaturePlans.vue";

    export default {
        components: {
            ShareDocumentModal,
            FolderTreeModal,
            Modal,
            DocumentSignatureRequestForm,
            SignaturePlans
        },
        props: ["file", "intent"],
        data(){
            return {
                showTopIconDropDown: false,
                renderShareDocumentModal: false,
                showingSignaturePlans: false,
            }
        },
        computed: {
            classes(){
                let data = {}
                let file = this.file
                if(file.type.toLowerCase() == "folder"){
                    Object.assign(data, {folder: true})
                }else{
                    let extensions = [
                    "pdf",
                    "svg",
                    "epub",
                    "txt",
                    "png",
                    "jpg",
                    "doc",
                    "html",
                    "css",
                    "js",
                    "md",
                    "yml",
                    "dll",
                    "exe",
                    "log",
                    "tmp",
                    "php",
                    "java",
                    "ai",
                    "mp3",
                    "m4a"
                    ]

                    if(extensions.some(el => el == file.extension)){
                    let temp = {}
                    temp[file.extension] = true
                    Object.assign(data, temp)
                    }else if((file.extension == "docx")){
                        Object.assign(data, {doc: true})
                    }else if((file.extension == "jpeg")){
                        Object.assign(data, {jpg: true})
                    }else if((file.extension == "htm")){
                        Object.assign(data, {html: true})
                    }else if((file.extension == "yaml")){
                        Object.assign(data, {yml: true})
                    }else if((file.extension == "s")){
                        Object.assign(data, {css: true})
                    }else{
                        Object.assign(data, {"generic-file":true})
                    }
                }
                return data
            },
            fileName(){
                return this.file.name + (!!this.file.extension ? "." + this.file.extension : "")
            },
            date(){
                return new Date(this.file.updated_at)
            },
            formattedDate(){
                return this.date.getDay()  + "/"  + (this.date.getMonth() + 1) + "/" + this.date.getFullYear()
            },
            canBeSigned(){
                let ext = this.file.extension
                let list = ["doc", "pdf", "docx"]
                return list.some(el => el == ext)
            },
            isInTrash(){
                return (this.intent || '').toLowerCase() == 'trash'
            }
        },
        methods: {
            openFolder(){
                if(this.file.type.toLowerCase() == "folder"){
                    this.$store.dispatch("documents/getBreadCrumb", {id: this.$nuxt.$route.params.id})
                    this.$nuxt.$router.push("/documents/"+this.file.id)
                }
            },
            openDocument(){
            if(this.file.type.toLowerCase() == "folder"){
                this.openFolder()
                return
            }

            this.$nuxt.$router.push('/documents/view/' + this.file.id)
            },
            documentMonitor(e) {
                const path = e.path || (e.composedPath ? e.composedPath() : [])

                if( path.some(el => el == this.$refs.dropdownButton)){
                    if(this.showTopIconDropDown  == false)
                        this.showTopIconDropDown = true
                }

                else if(path.some(el => el == this.$refs.dropdown)){
                }else{
                    this.showTopIconDropDown = false
                }
            },
            deleteDocument(){
            let attempt = this.$store.dispatch("documents/deleteDocument", {document_id: this.file.id})
            },
            hideTopIcons(){
                this.showTopIconDropDown = false
            },
            showShareDocumentModal(){
                this.renderShareDocumentModal = true
                this.hideTopIcons()
            },
            openCopyToModal(){
                this.hideTopIcons()
                this.$refs.copyToModal.openModal()
            },
            openMoveToModal(){
                this.hideTopIcons()
                this.$refs.moveToModal.openModal()
            },
            commitCopyTo(val){
                let data = {
                    document_id : this.file.id,
                    folder_id : !val ? "root" : val
                }

                let attempt = this.$store.dispatch("documents/copyDocumentToFolder", data)
            },
            commitMoveTo(val){
                let data = {
                    document_id : this.file.id,
                    folder_id : !val ? "root" : val
                }

                let attempt = this.$store.dispatch("documents/moveDocumentToFolder", data)
            },
            showDocumentSignatureRequestForm(){
                this.hideTopIcons()
                this.$refs.documentSignatureRequestForm.show()
            },
            hideDocumentSignatureRequestForm(){
                this.$refs.documentSignatureRequestForm.close()
            },
            showSignaturePlans(){
            this.showingSignaturePlans = true
            },
            hideSignaturePlans(){
            this.showingSignaturePlans = false
            },
            noSigningPriviledge(){
                this.hideDocumentSignatureRequestForm()
                setTimeout(() => {
                    this.showSignaturePlans()
                }, 50)
            },
            permanentlyDeleteDocument(){
                let attempt = this.$store.dispatch("documents/permanentlyDeleteDocument", {document_id: this.file.id})
            },
            restoreDocument(){
                this.$store.dispatch("documents/restoreDocument", { document_id: this.file.id })
            },
        },

        mounted(){
            document.addEventListener("click", this.documentMonitor)
        }
    }
</script>

<style scoped>
    .list-item{
        padding: 5px;
        font-size: 0.75rem;
        position: relative;

        display: grid;
        grid-template-columns: 2.5rem 2fr 1fr 3fr 25px;
    }

    .list-item > *{
        display: flex;
        align-items: center;
        color: #797979;
        overflow: hidden;
        
        padding-right: 5px;
    }

    .list-item > * > span{
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .list-item .dropdown{
        position: absolute;
        top: 1px;
        right: 1px;
        background: #EFEFEF;
        box-shadow: 2px 2px 4px #C4C4C4;
        z-index: 2;
        max-width: calc(100% + 1.5rem);
        overflow: hidden;
    }

    .list-item .dropdown ul{
        list-style: none;
        padding-inline-start: 0;
        padding: 0;

    }
    .list-item .dropdown ul li {
        padding-right: 0.8rem;
        padding-left: 0.8rem;
        padding-top: 0.4rem;
        padding-bottom: 0.4rem;
        font-size: 0.8rem;
        cursor:pointer;
        border-radius: 5px;

        border: none;
        border-bottom: 2px solid white;
        white-space: nowrap;
        width: 100%;
        text-overflow:ellipsis;
        overflow: hidden;
    }

    .list-item .dropdown ul li.demacation{
        padding: 0;
        height: 5px;
        background: #9b9b9ba1;
    }

    .list-item .dropdown ul li:first-child{
        padding-top: 0;
    }

    .list-item .dropdown ul li:last-child{
        border-bottom: none;
        padding-bottom:0
    }

    .list-item .dropdown ul li:hover{
        color: black;
    }

    .file-icon{
        background-position: center center;
        background-repeat: no-repeat;
        background-size: contain;
        height: 1.5rem;
    }

    .file-option-icon{
        background-position: center center;
        background-repeat: no-repeat;
        background-size: contain;
        background-origin: content-box;
        height: 100%;
        background-image: url("/png/triple-dots.png");
        padding: 0.2rem;
        cursor: pointer;
    }

    

    .folder{
        background-image: url("/png/folder.png");
    }

    .pdf{
        background-image: url("/png/pdf-icon.png");
    }

    .svg{
        background-image: url("/png/svg-icon.png");
    }

    .jpg{
        background-image: url("/png/jpg-icon.png");
    }

    .png{
        background-image: url("/png/png-icon.png");
    }

    .doc{
        background-image: url("/png/doc-icon.png");
    }

    .epub{
        background-image: url("/png/epub-icon.png");
    }

    .tiff{
        background-image: url("/png/tiff-icon.png");
    }

    .txt{
        background-image: url("/png/txt-icon.png");
    }

    .js{
      background-image: url("/png/js-icon.png");
    }

    .md{
      background-image: url("/png/md-icon.png");
    }

    .yml{
      background-image: url("/png/yml-icon.png");
    }

    .dll{
      background-image: url("/png/dll-icon.png");
    }

    .log{
      background-image: url("/png/log-icon.png");
    }

    .tmp{
      background-image: url("/png/tmp-icon.png");
    }

    .php{
      background-image: url("/png/php-icon.png");
    }

    .java{
      background-image: url("/png/java-icon.png");
    }

    .ai{
      background-image: url("/png/ai-icon.png");
    }

    .exe{
      background-image: url("/png/exe-icon.png");
    }

    .html{
        background-image: url("/png/html-icon.png");
    }

    .css{
        background-image: url("/png/css-icon.png");
    }

    .mp3{
        background-image: url("/png/mp3.png");
    }

    .m4a{
        background-image: url("/png/m4a.png");
    }

    .generic-file{
        background-image: url("/png/generic-file-icon.png");
    }
</style>