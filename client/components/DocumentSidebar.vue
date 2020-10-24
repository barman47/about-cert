<template>
    <div id="document-sidebar" :style="style">
        <div class="collapse-button-container">
            <div @click="toggleExpanded()" class="collapse-button"></div>
        </div>
        <div class="upload-file-container">
            <button ref="uploadFileButton" class="upload-file-button" @click.prevent="initiateFileUpload()"><div class="upload-file-image"></div> <span v-show="expanded" style="margin-left: 0.5rem;"> Upload File</span></button>
            <input type="file" hidden ref="fileUploadField" @change="uploadFile()">
        </div>

        <div class="document-sidebar-navigations">
            <div class="document-sidebar-navigations-item" :class="{active: $nuxt.$route.name == 'documents-id'}" @click="goToLink('/documents')"> 
                <div class="document-sidebar-navigations-image-container">
                    <span class="document-sidebar-navigations-image" style="background-image: url('/png/folder-sm.png')"></span> 
                </div>
                <span v-show="expanded" class="document-sidebar-navigations-item-text">
                    Folder
                </span>
            </div>
            <div class="document-sidebar-navigations-item" :class="{active: $nuxt.$route.path == '/documents/create'}" @click="goToLink('/documents/create')">
                <div class="document-sidebar-navigations-image-container">
                    <span class="document-sidebar-navigations-image" style="background-image: url('/png/add.png')"></span> 
                </div>
                <span v-show="expanded" class="document-sidebar-navigations-item-text">
                    Create Document
                </span>
            </div>
            <div class="document-sidebar-navigations-item" :class="{active: $nuxt.$route.path.startsWith('/documents/signatures/documents')}" @click="goToLink('/documents/signatures/documents/sent')"> 
                <div class="document-sidebar-navigations-image-container">
                    <span class="document-sidebar-navigations-image" style="background-image: url('/png/signature.png')"></span> 
                </div>
                <span v-show="expanded" class="document-sidebar-navigations-item-text">
                    Create Signature
                </span>
            </div>
            <div class="document-sidebar-navigations-item" :class="{active: $nuxt.$route.path == '/documents/trash'}" @click="goToLink('/documents/trash')">
                <div class="document-sidebar-navigations-image-container">
                    <span class="document-sidebar-navigations-image" style="background-image: url('/png/delete.png')"></span> 
                </div> 
                <span v-show="expanded" class="document-sidebar-navigations-item-text">
                    Trash
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                // expanded: false,
                styles: [
                    {"grid-area": "documentBar-start / documentBar-start / span 1 / span 2"},
                    {"grid-area": "documentBar-start / documentBar-start / span 1 / span 1"},
                ],
                swipe: {
                    startX: undefined,
                    startY: undefined,
                    dist: undefined,
                    threshold: 75, //required min distance traveled to be considered swipe
                    allowedTime: 500, // maximum time allowed to travel that distance
                    elapsedTime: undefined,
                    startTime: undefined
                }
            }
        },
        props: {
            expanded: {
                required: true
            }
        },
        computed: {
            style(){
                return this.styles[this.expanded ? 0 : 1]
            }
        },
        mounted(){
            let touchsurface = document.getElementById('document-sidebar')
            touchsurface.addEventListener('touchstart', this.touchstartEventListener, false)

            touchsurface.addEventListener('touchmove', function(e){
                e.preventDefault() // prevent scrolling when inside DIV
            }, false)

            touchsurface.addEventListener('touchend', this.touchendEventListener, false)
        },
        methods: {
            touchstartEventListener(e){
                let touchobj = e.changedTouches[0]
                this.swipe.dist = 0
                this.swipe.startX = touchobj.pageX
                this.swipe.startY = touchobj.pageY
                this.swipe.startTime = new Date().getTime() // record time when finger first makes contact with surface
                // e.preventDefault()
            },
            touchendEventListener(e){
                let touchobj = e.changedTouches[0]
                this.swipe.dist = touchobj.pageX - this.swipe.startX // get total dist traveled by finger while in contact with surface
                this.swipe.elapsedTime = new Date().getTime() - this.swipe.startTime // get time elapsed
                // check that elapsed time is within specified, horizontal dist traveled >= threshold, and vertical dist traveled <= 100
                // let swipeOccurred = (this.swipe.elapsedTime <= this.swipe.allowedTime && this.swipe.dist >= this.swipe.threshold && Math.abs(touchobj.pageY - startY) <= 100)
                let swipeOccurred = (this.swipe.elapsedTime <= this.swipe.allowedTime && Math.abs(this.swipe.dist) >= this.swipe.threshold && Math.abs(touchobj.pageY - this.swipe.startY) <= 100)

                // debugger

                if(swipeOccurred){
                    let swipeDirection = this.swipe.dist >= this.swipe.threshold ? "right" : "left"
                    this.handleswipe(swipeDirection)
                }
                // e.preventDefault()
            },
            handleswipe(swipeDirection){
                if(swipeDirection == "right"){
                    if(!this.expanded)
                        this.toggleExpanded()
                }else{
                    if(this.expanded)
                        this.toggleExpanded()
                }
            },
            toggleExpanded(){
                this.expanded = !this.expanded
                this.$emit('toggleExpanded')
            },
            goToLink(path){
                if(this.$nuxt.$route.path == path)
                    return
                return this.$nuxt.$router.push(path)
            },
            initiateFileUpload(){
                this.$refs.fileUploadField.click()
            },
            uploadFile(){
                let formData = new FormData()
                formData.append("name", this.$refs.fileUploadField.files[0].name.replace(/(\.[^.]*)$/, ""))
                formData.append("file", this.$refs.fileUploadField.files[0])
                formData.append("type", "file")

                if(this.$nuxt.$route.name == "documents-id" && this.$nuxt.$route.params.id)
                    formData.append("folder_id", this.$nuxt.$route.params.id)

                let button = this.$refs.uploadFileButton

                button.classList.add("loading")

                let attempt = this.$store.dispatch("documents/makeDocument", formData)

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
        }
    }
</script>

<style scoped>
#document-sidebar{
    /* padding: 0.3rem; */
    display: flex;
    flex-direction: column;
    z-index: 10;
}



.collapse-button-container{
    height: 30px;
    display: flex;
    align-items:center;
    justify-content: flex-end;
    margin-bottom: 1rem;
    margin-top: 0.5rem;
}

.collapse-button{
    height: 25px;
    width: 25px;
    background-image: url("/png/sidebar-collapse.png");
    background-repeat: no-repeat;
    background-position: center center;
    background-size: contain;
    cursor: pointer;
    margin-right: calc(50px/2 - 25px /2);
}

.upload-file-container{
    display: flex;
    justify-content: center;
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

.document-sidebar-navigations{
    display: flex;
    flex: 1;
    flex-direction: column;
    justify-content: flex-start;
    color: #9b9b9b;
    font-size: 0.9rem;
    margin-top: 3rem;
}

.document-sidebar-navigations-item{
    display: flex;
    align-items: center;
    height: calc(30px + 0.8rem);
    margin-top: 0.1rem;
    margin-bottom: 0.1rem;
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
    /* background: green; */
    cursor: pointer;
}

.document-sidebar-navigations-item.active{
    border-left: #0084ff 3px solid;
    background: #f7f7f7;
}

.dark-theme .document-sidebar-navigations-item.active{
    background: #555286;
}

.document-sidebar-navigations-image-container{
    /* width: calc(50px - 0.6rem); */
    width: 50px;
    height: 100%;
    padding: 0.1rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.document-sidebar-navigations-image{
    width: 30px;
    height: 100%;
    display: inline-block;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: contain;
}
.document-sidebar-navigations-item-text{
    margin-left: 0.1rem;
}

@media (max-width: 575.98px) {
    #document-sidebar{
        border-radius: 0;
    }
}
</style>