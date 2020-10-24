<template>
    <div class="template-group">
        <div class="template-group-overlay" @dblclick="hideOverlay()" v-show="showOverlay">
            <button class="view-pack-button" @click="hideOverlay()">View Pack</button>
        </div>
        <div v-if="!templateInViewId">
            <div class="template-group-name">{{templateGroup.name}}</div>
            <div class="templates">
                <!-- <div class="template-preview" :style="{backgroundImage: 'url(\''+templateStringToImageSource(i)+'\')'}" v-for="i in images" :key="i"></div> -->
                <div class="template-preview-container" v-for="template in templates" :key="template.id">
                    <img @click="viewThisTemplate(template.id)" class="template-preview" :src="templateStringToSmImageSource(template.preview_img)" alt="">
                </div>
            </div>
        </div>
        <div v-if="!!templateInViewId">
            <div class="currently-in-view">
                <div class="currently-in-view-preview-img-container">
                    <img class="currently-in-view-preview-img" :src="templateStringToLgImageSource(templateInView.preview_img)">
                </div>
                <div class="display-flex flex-direction-column">
                    <div class="currently-in-view-text">
                        These premium pack of CVs costs are re-usable within a month and they cost a sum of N1,400 per month
                    </div>
                    <div class="download-error-text" ref="downloadErrorText"></div>
                    <div class="cv-button-group-container">
                        
                        <div class="cv-button-group" v-if="!!templateGroup.has_access || !!templateInView.has_access">
                            <button class="cv-button"><div class="icon" style="background-image: url('/png/share-icon-sm.png')"></div> Share</button>
                            <button ref="templateInViewDownloadButton" class="cv-button" @click="downloadCVInView()"><div class="icon" style="background-image: url('/png/download-icon-sm.png')"></div> Download</button>
                            <button class="cv-button generate-cv" ref="saveCVButton"  @click="saveCV('recompile')" v-if="groupIntent == 'saved'">Recompile</button>
                            <button class="cv-button generate-cv" ref="saveCVButton"  @click="saveCV()" v-else>Save CV</button>
                        </div>
                        <button class="buy-now-button" v-else @click="payForPack()">Buy Now</button>
                    </div>
                </div>
            </div>
            <div class="not-in-view">
                <div class="templates">
                    <!-- <div class="template-preview" :style="{backgroundImage: 'url(\''+templateStringToImageSource(i)+'\')'}" v-for="i in images" :key="i"></div> -->
                    <div class="template-preview-container" v-for="template in templates" :key="template.id">
                        <img @click="viewThisTemplate(template.id)" class="template-preview" :class="{'in-view': templateInView.id == template.id}" :src="templateStringToLgImageSource(template.preview_img)" alt="">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            showOverlay: true,
            templateInViewId: undefined,
            savingCV: false
        }
    },
    props: ["templateGroup", "groupIntent"],
    computed: {
        templateInView(){
            return this.templates.find(el => el.id == this.templateInViewId)
        },
        templates(){
            return this.templateGroup.templates
        }
    },
    methods: {
        templateStringToImageSource(val){
            return process.env.BACKEND_BASE_URL + val
        },
        templateStringToSmImageSource(val){
            return this.templateStringToImageSource(val) + "/small.png"
        },
        templateStringToLgImageSource(val){
            return this.templateStringToImageSource(val) + "/large.png"
        },
        viewThisTemplate(id){
            this.templateInViewId = id
        },
        hideOverlay(){
            this.showOverlay = false
            this.$emit("inView")
        },
        resetView(inViewId){
            if(this.templateGroup.id == inViewId)
                return

            this.templateInViewId = undefined
            this.showOverlay = true
        },
        payForPack(){
            const data = {
                redirect_url : process.env.CLIENT_BASE_URL + "/profile/edit",
                group_id: this.templateGroup.id
            }

            let attempt = this.$store.dispatch("cv/payForPack", data);

            attempt.then(link => {
                window.location = link
            })
        },
        downloadCVInView(){
            const button = this.$refs.templateInViewDownloadButton;
            let downloadErrorText = this.$refs.downloadErrorText
            downloadErrorText.textContent = ""

            button.setAttribute("disabled", true)

            button.classList.add("loading")

            let data = {
                    template_id : this.templateInViewId,
                    intent : this.groupIntent == "saved" ? "download_saved" : "download_only"
                }

            let attempt = this.$store.dispatch("cv/downloadCVInView", data)

            attempt.then(downloadLink => {
                let a = document.createElement("a")
                a.href = downloadLink
                a.download = downloadLink
                a.click()
                // window.open(downloadLink, '_blank');
            }).catch(err => {
                if(err.response && err.response.status == 404)
                    downloadErrorText.textContent = err.response.data.message
                else
                    alert(err.response.data.message)
            })
            .finally(() => {
                button.removeAttribute("disabled")
                button.classList.remove("loading")

                setTimeout(() => {
                    downloadErrorText.textContent = ""
                }, 5000)
            })
        },
        saveCV(saveIntent = ""){
          if(this.savingCV == true)
            return

          this.savingCV = true
          let template = this.templateInView

          let button = this.$refs.saveCVButton


          button.classList.add("loading")

          let data = {template_id: template.id}
          let meta = {
                saveIntent: saveIntent,
                templateGroupId: this.templateGroup.id
            }

          let attempt = this.$store.dispatch("cv/saveCV", {data: data, meta: meta})
          attempt.then(() => {
                button.classList.add("success")
            }).catch(() => {
                button.classList.add("error")
            }).finally(() => {
                this.savingCV = false
                button.classList.remove("loading")

                setTimeout(() => {
                    button.classList.remove("error")
                    button.classList.remove("success")
                    button.classList.remove("loading")
                }, 2000);
            })
        },
    }
}
</script>

<style scoped>
    .template-group{
        background: #263238;
        border-radius: 10px;
        padding: 1.5rem;
        color: white;

        position: relative;
        overflow: hidden;
    }

    .template-group-overlay{
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        z-index: 2;
        background: rgba(38, 50, 56, 0.7);
    }

    .view-pack-button{
        position: absolute;
        bottom: 1.5rem;
        right: 1rem;

        background: #27DEBF;
        border-radius: 5px;

        border:none;
        color: white;
        line-height: 1rem;
        height: 2rem;
    }


    .template-group-name{
        margin-bottom: 1rem;
    }

    .templates{
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: 1fr;
        grid-gap: 1rem;
    }

    .template-preview-container{
        height: 10rem;
    }

    .template-preview{
        max-height: 100%;
        max-width: 100%;
        border-radius: 5px;

        cursor: pointer;
    }

    .currently-in-view{
        display: grid;
        grid-template-columns: 15rem 1fr;
        column-gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .currently-in-view-preview-img-container{
        height: 20rem;
        max-height: calc(var(--viewport-height, 100vh) - 10rem);
    }

    .currently-in-view-preview-img{
         object-fit: contain; 
        /*height: 100%;
        width: auto;*/
        max-height: 100%;
        max-width: 100%;

        border-radius: 8px;
    }

    .currently-in-view-text{
        padding-right: 20%;
        flex: 1;
    }

    .not-in-view .templates{
        /*grid-template-columns: repeat(7, 1fr);
        grid-gap: 0.5rem;*/
        display: flex;
        flex-direction: row;
        overflow-x: auto;
        flex-wrap: nowrap;
    }

    .not-in-view .template-preview-container{
        height: 7rem;
        width: 5rem;
        min-height: 7rem;
        min-width: 5rem;
        margin-right: 10px;
    }

    .not-in-view .template-preview-container:last-child{
        margin-right: 0;
    }

    .not-in-view .template-preview.in-view{
        border: 2px dashed white;
    }

    /* For the button groups */
    .buy-now-button{
        /*position: absolute;
        bottom: 0;
        right: 0;*/

        background: #27DEBF;
        border-radius: 5px;

        border:none;
        color: white;
        line-height: 1rem;
        height: 2rem;
    }

    .cv-button-group-container{
        display: flex;
        justify-content: flex-end;
    }

    .cv-button-group{
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;

        border: 1px solid white;
        border-radius: 3px;
        overflow: hidden;
    }

    .cv-button{
        background: #263238;
        color: white;
        height: 27px;
        font-size: 0.75rem;
        border: none;
        margin-left: 0.5rem;
        flex: 1;
        white-space: nowrap;
    }



    .cv-button.generate-cv{
        background: #0084ff;
    }

    .cv-button.loading{
        animation-name: colorLoading;
        animation-duration: 1.5s;
        animation-iteration-count: infinite;
    }

    .cv-button.error{
        background: #e86262;
    }

    .cv-button.success{
        background: #7ed202;
    }

    .cv-button .icon{
        height: 0.8rem;
        width: 0.8rem;
        background-position: center center;
        background-repeat: no-repeat;
        background-size: contain;
        display: inline-block;
    }

    .download-error-text{
        font-size: 0.8rem;
        color: #f59c5d;
        margin-bottom: 0.2rem;
    }

    @media (max-width: 1199.98px) {
        .template-preview-container{
            height: 8rem;
        }

        .currently-in-view{
            grid-template-columns: 10rem 1fr;
        }

        .currently-in-view-preview-img-container{
            height: 12rem;
        }

        .currently-in-view-text{
            font-size: 0.8rem;
        }

    }

    @media (max-width: 991.98px) {
        .template-preview-container{
            height: 5.2rem;
        }

        .currently-in-view{
            grid-template-columns: 7rem 1fr;
        }

        .cv-button{
            font-size: 0.7rem;
        }

        .cv-button .icon{
            height: 0.7rem;
            width: 0.7rem;
        }
    }

    @media (max-width: 767.98px) {
        .template-preview-container{
            height: 6rem;
        }

        .templates{
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 575.98px) {
        .template-preview-container{
            height: 5.2rem;
        }

        .currently-in-view{
            grid-template-columns: 1fr;
        }

        .currently-in-view-text{
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .currently-in-view-preview-img-container{
            display: flex;
            justify-content: center;
            margin-bottom: 5px;
            padding: 5px;
            border-radius: 5px;
            background: #e5e5e5;
            margin-bottom: 10px;
        }

        .cv-button-group-container{
            justify-content: unset;
        }

        .cv-button-group{
            display: flex;
            width: 100%;
        }
    }
</style>
