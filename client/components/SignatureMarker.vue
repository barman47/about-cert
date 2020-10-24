<template>
  <div class="single-file" ref="currentSingleFile">
      <div class="top-icons">
          <div class="image check" :class="classes"></div>
          <div @click="showTopIconDropDown=true" class="image triple-dots" style="background-image: url('/png/triple-dots.png')"></div>
          <div  class="top-icon-dropdown" v-show="showTopIconDropDown">
              <ul>
                <li v-if="action == 'received'" class="name-text">From: {{file.user_name}}</li>
                <li class="demacation" v-if="action == 'received'"></li>
                <li @click="viewDetails()">View Details</li>
                <li v-if="action == 'received'" @click="signDocument()">Sign Document</li>
                <li v-if="action == 'sent' && file.sent == 1">Sent For Signing</li>
                <li v-if="action == 'sent' && file.sent == 0" @click="sendSignatureRequest()">Send Signature Request</li>
              </ul>
          </div>
      </div>

      <div class="middle-section image" :class="classes" @dblclick="viewDetails()"></div>
      <div class="file-name" @click="viewDetails()">
          {{file.document_name}}
      </div>

      <div class="file-footer" >
          <div class="date">{{formattedDate}}</div>
      </div>
  </div>
</template>

<script>

export default {
    components: {
    },
    props: ["file", "action"],
    data(){
        return {
            showTopIconDropDown: false,
        }
    },
    mounted(){
      // HelloSign = import('hellosign-embedded')
    },
    computed: {
        date(){
            return new Date(this.file.created_at)
        },
        classes(){
          let data = {}

          if(this.action == "received"){
            Object.assign(data, {received : true})

            if(this.file.viewed == 0){
              Object.assign(data, {"not-viewed" : true})
            }else{
              if(this.file.signed == 0)
                Object.assign(data, {"viewed-not-signed" : true})
            }
          }
          else if(this.action == "sent"){
            Object.assign(data, {sent : true})

            if(this.file.sent == 0){
              Object.assign(data, {pending : true})
            }
          }

          return data
        },
        formattedDate(){
            return this.date.getDay()  + "/"  + (this.date.getMonth() + 1) + "/" + this.date.getFullYear()
        },
    },
    methods: {
      documentMonitor(e) {
          const path = e.path || (e.composedPath && e.composedPath())
          if(path.some(el => el == this.$refs.currentSingleFile)){
              // console.log("within the file")
          }else{
              // console.log("Outside the file")
              this.showTopIconDropDown = false
          }
      },
      hideTopIcons(){
          this.showTopIconDropDown = false
      },
      signDocument(){
        this.hideTopIcons()
        window.open(this.file.embedded_signing_url, "_blank")
      },
      sendSignatureRequest(){
        window.open(this.file.embedded_signing_url, "_self")
      },
      goToPage(page){
        if(page != this.$nuxt.$route.fullPath)
          this.$nuxt.$router.push(page)
      },
      viewDetails(){
        this.goToPage('/documents/signatures/documents/' + this.file.id)
      },
    },
    mounted() {
        document.addEventListener("click", this.documentMonitor)
    }, //end the mounted function
}
</script>

<style scoped>
    .single-file{
        --scaling-value: 0.8;

        width: calc(11rem * var(--scaling-value));
        height: calc(13rem * var(--scaling-value));

        /* transform: scale(0.9); */

        background: #FFFFFF;
        border: 1px solid #E2E2E2;
        box-sizing: border-box;
        border-radius: 5px;
        margin: 0.5rem;
        /* overflow: hidden; */
        padding: 0.5rem;
    }

    .image{
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center center;
    }

    .middle-section.image.received{
        background-image: url("/png/construction.png");
    }

    .middle-section.image.received.not-viewed{
        background-image: url("/png/not-viewed.png");
    }

    .middle-section.image.received.viewed-not-signed{
      background-image: url("/png/viewed.png");
    }

    .middle-section.image.sent{
        background-image: url("/png/sent-icon.png");
    }

    .middle-section.image.sent.pending{
      background-image: url("/png/pending.png");
    }

    .top-icons{
        height: 9%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .top-icon-dropdown{
        position: absolute;
        top: 1px;
        right: 1px;
        background: #EFEFEF;
        box-shadow: 2px 2px 4px #C4C4C4;
        max-width: 120%;
        overflow: hidden;
        z-index: 2;
    }

    .top-icon-dropdown ul{
        list-style: none;
        padding-inline-start: 0;
        padding: 0;
    }

    .top-icon-dropdown ul li {
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
        overflow: hidden;
        text-overflow:ellipsis;
    }
    .top-icon-dropdown ul li.demacation{
        padding: 0;
        height: 5px;
        background: #9b9b9ba1;
    }

    .top-icon-dropdown ul li:first-child{
        padding-top: 0;
    }

    .top-icon-dropdown ul li:last-child{
        border-bottom: none;
        padding-bottom:0
    }

    .top-icon-dropdown ul li:hover{
        color: black;
    }

    .middle-section{
        height: 50%;
        cursor: pointer;
    }

    .file-name{
        line-height: 1rem;
        font-size: 0.75rem;
        text-align: center;
        -webkit-line-clamp: 2;
        display: -webkit-box;
        -webkit-box-orient: vertical;
        overflow: hidden;
        max-height: 30%;
        min-height: 20%;
        padding-top: 0.2rem;
        cursor: pointer;
    }

    .file-footer{
        height: 10%;
        overflow: hidden;
        display: flex;
        justify-content: flex-end;
        align-items: flex-end;
        font-size: 0.6rem;
    }

    .file-size{
        font-weight: bold;
    }

    .check, .triple-dots{
        height: 100%;
        width: 1.5rem;
        cursor: pointer;
    }

    .check{
        background-position: left center;
    }

    .check.received{
      background-image: url("/png/construction-icon.png");
    }

    .check.sent{
      background-image: url("/png/sent-check-icon.png");
    }

    .triple-dots{
        background-position: right center;
    }

    .name-text {
      color: black;
      white-space: normal !important;
      overflow: hidden;
    }
</style>
