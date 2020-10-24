<template>
    <div class="share-document-modal" ref="modal">
        <div class="modal">
            <div class="modal-header">
                <span>Share File</span>
                <span @click="closeModal()" class="close-button">&times;</span>
            </div>
            <div class="content">
                <div class="search-in-app">
                    <form action="" @submit.prevent="searchForUSer()">
                        <div class="search-in-app-input-group">
                            <input type="text" placeholder="Search User Here" ref="searchInput">
                            <span class="button-container">
                                <button></button>
                            </span>
                        </div>
                    </form>

                    <div class="search-result-message" ref="searchResultMessage"></div>

                    <div class="search-result" v-show="users.length > 0">
                        <div class="user-strip" v-for="user in users" :key="user.id" @click="includeOrRemoveUser(user.id)">
                            <div class="profile-photo-container">
                                <div class="profile-photo" :style="{backgroundImage: backgroundPhoto(!!user.thumbnail ? user.thumbnail : (!!user.display_photo ? user.display_photo : 'man-avatar-profile-icon.jpg'))}"></div>
                            </div>
                            <div class="name-container">
                                <div class="name">{{user.name}}</div>
                            </div>
                            <div class="user-marked" v-if="receiverIds.some(el => el == user.id)">
                                <div></div>
                            </div>
                        </div>

                        <div class="display-flex justify-content-end align-items-center share-button-container">
                            <button @click="sharePost()">Share</button>
                        </div>
                    </div>
                </div>

                <div class='copy-section'>
                    <div>Share this link with friends to access this event</div>
                    <div class="copy-link-input-group">
                        <span class="copy-text" id="copy-text">{{linkToShare}}</span>
                        <span class="copy-link-button-container">
                            <button @click="copyToClipboard()">Copy Link</button>
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button class="share-to-social-media-button" @click="sharePost('social')" v-if="canShareToSocialMedia">
                Share to social media!!!
              </button>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "ShareDocumentModal",
    components: {
    },
    props: ["post"],
    data(){
        return {
            searchData: {},
            receiverIds: []
        }
    },
    computed: {
        post_id(){
          return this.post.id
        },
        users() {
            return this.searchData.data ? this.searchData.data : []
        },
        linkToShare(){
            return location.origin + "/updates/"+this.post_id
        },
        canShareToSocialMedia(){
          if(navigator.share)
            return true
          return false
        }
    },
    methods: {
        backgroundPhoto(val){
            return `url('${process.env.BACKEND_BASE_URL + val}')`
        },
        closeModal(){
            this.$refs.modal.style.display = "none"
            this.$emit("closed")
        },
        copyToClipboard(){
            const el = document.createElement('textarea')
            el.value = document.getElementById("copy-text").textContent
            document.body.appendChild(el)
            el.select()
            document.execCommand('copy')
            document.body.removeChild(el)

            this.sharePost("notify")
            alert("Copied!")
        },
        searchForUSer(){
            this.$refs.searchResultMessage.textContent = ""
            const searchString = this.$refs.searchInput.value

            this.receiverIds = []

            if(!searchString)
                return

            let attempt = this.$axios.get(`api/users/search?query=${searchString}&per_page=8&0=1`)

            attempt.then(response => response.data)
                    .then(data => {
                        if(data.data.length <= 0){
                            this.$refs.searchResultMessage.textContent = "No Result Found"
                            this.searchData = {}
                        }else{
                            let temp = data

                            temp.data = temp.data.filter(el => el.id != this.$auth.user.id)

                            this.searchData = temp

                            if(this.searchData.data.length == 0 )
                                this.$refs.searchResultMessage.textContent = "No Result Found"
                        }
                    })
        },
        includeOrRemoveUser(id){
            let index = this.receiverIds.findIndex(el => el == id)
            if(index < 0)
                this.receiverIds.push(id)
            else
                this.receiverIds.splice(index, 1)
        },
        sharePost(val = ""){
            if( !(val === "notify" || val === 'social') && this.receiverIds.length <= 0)
                return

            if(val === 'social'){
              let error_occ = false
              navigator.share({
                // title: "Something",
                // text: "Dummy text",
                // url: window.location.href
                  title: this.post.title,
                  text: this.post.content,
                  url: this.linkToShare
              }).then(() => {})
              .catch(() => error_occ = true);

              if(!error_occ) return
            }

            let data = {
                receiver_ids: val == "notify" || val == "social" ? JSON.stringify([]) : JSON.stringify(this.receiverIds),
                post_id: this.post_id
            }

            let attempt = this.$store.dispatch("updates/sharePost", data)

            attempt.then((response) => {
                // console.log(response)
                // alert(response.message)
            }).finally(() => {
                sendDocumentButton.removeAttribute("diabled")
            })
        }, //end function shareDocument
    }
}
</script>

<style scoped>
    .share-document-modal{
        position: fixed;
        top: 0;
        bottom: 0;

        /* min-height: 100%; */
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items:center;
        z-index: 999;
        background: rgba(0,0,0, 0.5);
        padding: 1rem;
        overflow-y: auto;
    }

    .share-document-modal .modal{
        --modal-content-padding: 1rem;
        --modal-border-radius: 10px;
        --modal-width: 40vw;
        border-radius: var(--modal-border-radius);
        width: var(--modal-width);
        /* max-height: calc(100vh - 100px); */
        background: #F0F0F0;
        position: relative;
    }

    .share-document-modal .modal .modal-header{
        background: white;
        padding: var(--modal-content-padding);
        border-top-right-radius: var(--modal-border-radius);
        border-top-left-radius: var(--modal-border-radius);

        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .share-document-modal .modal .modal-header .close-button{
        height: 100%;
        width: 2rem;
        cursor: pointer;

        display: flex;
        justify-content: flex-end;
    }

    .share-document-modal .modal .content{
        padding: var(--modal-content-padding);
    }

    .search-in-app .header{
        font-weight: bold;
        color: black;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }



    .search-in-app .search-in-app-input-group{
        --search-height: 2rem;
        --search-padding: 0.2rem;
        height: var(--search-height);
        display: flex;
        align-items: center;
    }
    .search-in-app .search-in-app-input-group input,
    .search-in-app .search-in-app-input-group .button-container{
        height: 100%;
        margin: 0;
        border: 1px solid #9B9B9B;
        border-radius: 5px;
    }

    .search-in-app .search-in-app-input-group input{
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: 0;
        background: white;
        padding-left: 0.2rem;
        padding-right: 0.2rem;
        flex: 1;
    }

    .search-in-app .search-in-app-input-group .button-container{
        border-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px;
    }

    .search-in-app .search-in-app-input-group button{
        width: calc(var(--search-height) - var(--search-padding) - 2px);
        height: calc(var(--search-height) - var(--search-padding) - 2px);
        border-radius: 3px;
        border:none;
        margin: 0;
        background-color: #0084ff;
        background-image: url("/png/search2.png");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
        padding: 0.3rem;
        background-origin: content-box;
    }

    .search-result{
        --search-result-arrow-height: 1.5rem;
        margin-top: var(--search-result-arrow-height);
        border-radius: 5px;
        position: relative;
    }

    .search-result::before{
        content: "";
        border-top: 0;
        border-right: var(--search-result-arrow-height) solid transparent;
        border-left: var(--search-result-arrow-height) solid transparent;
        border-bottom: var(--search-result-arrow-height) solid white;
        position: absolute;
        left: 1rem;
        bottom: 100%;
    }

    .search-result .user-strip{
        --floating-folders-header-size: 30px;
        display: grid;
        grid-template-columns: 50px 1fr 40px;
        height: 50px;
        position: relative;
        margin-bottom: 0.5rem;
        cursor: pointer;
    }

    .search-result .user-strip .floating-folders{
        position: absolute;
        top: 0;
        left: calc(100% + var(--modal-content-padding));
        z-index: 2;
        background: #555286;
        width: calc((100vw - var(--modal-width)) / 2 - 2rem);
    }

    .search-result .user-strip .floating-folders::before{
        content: "";
        top: 0;
        right: 100%;

        position: absolute;

        border-top: solid transparent calc(var(--floating-folders-header-size) / 2);
        border-bottom: solid transparent calc(var(--floating-folders-header-size) / 2);
        border-right: solid #555286 var(--modal-content-padding);
        border-left: 0;
    }

    .search-result .user-strip .floating-folders .header {
        height: var(--floating-folders-header-size);
        background: white;
        color: #3A2C51;
        font-size: 0.7rem;
        display: flex;
        align-items: center;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        white-space: nowrap;
    }

    .search-result .user-strip .floating-folders .header .close-modal{
        min-width: 1rem;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .search-result .user-strip .floating-folders .footer{
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 0.5rem;
    }

    .search-result .user-strip .floating-folders .footer .send-document-button{
        border: none;
        background: #27DEBF;
        border-radius: 3px;
        color: white;
    }

    .user-marked{
        padding: 0.5rem;
        background-image: url("/png/check.png");
        background-size: contain;
        background-position: center center;
        background-repeat: no-repeat;
        background-origin: content-box;
    }

    .profile-photo-container{
        padding: 0.4rem;
    }

    .profile-photo-container .profile-photo{
        height: 100%;
        width: 100%;
        clip-path: circle();
        background-size: cover;
        background-repeat: no-repeat;
    }

    .search-result{
        background: white;
    }

    .name-container{
        display: flex;
        align-items: center;
        padding-left: 0.2rem;
        padding-left: 0.2rem;
        overflow: hidden;
    }

    .name-container .name{
        width: 100%;
        overflow: hidden;
        color: black;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 0.7rem;
        font-weight: bold;
    }

    .share-button-container{
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 0.5rem;
        padding-bottom: 0.5rem;
    }

    .share-button-container button{
        font-size: 0.8rem;
        border: none;
        color: white;
        border-radius: 3px;
        background: #0084ff;
    }

    .copy-section{
        font-size: 0.8rem;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .copy-link-input-group{
        margin-top: 0.5rem;
        background: #F6F6F6;
        border: 1px solid #C4C4C4;
        border-radius: 3px;
        height: 30px;

        display: flex;
        align-items: center;
    }

    .copy-text{
        flex: 1;
        overflow: hidden;
        overflow-x: auto;
        color: black;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        white-space: nowrap;
        overflow: hidden;
        width: 100%;
        text-overflow: ellipsis;

        -webkit-user-select: all;  /* Chrome all / Safari all */
        -moz-user-select: all;     /* Firefox all */
        -ms-user-select: all;      /* IE 10+ */
        user-select: all;          /* Likely future */
    }

    .copy-link-button-container{
        height: 100%;
        padding:0.2rem;
    }

    .copy-link-button-container button{
        background: #0084FF;
        border-radius: 3px;
        border: none;
        color: white;
        height: 100%;
        font-size: 0.63rem;
    }

    .invite-notification{
        color: #7B018E;
        font-size: 0.63rem;
    }

    .modal-footer{
        height: 50px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: var(--modal-content-padding);
        font-size: 0.8rem;

        border-bottom-left-radius: var(--modal-border-radius);
        border-bottom-right-radius: var(--modal-border-radius);
    }

    .footer-child{
        display: flex;
        align-items: center;
    }

    .edit-link{
        color: #0084ff;
        cursor: pointer;
    }

    .search-result-message{
        margin-top: 1rem;
        font-size: 1.5rem;
        font-style: italic;
        color: #463737;
    }

    .share-to-social-media-button{
      border: none;
      background: #0084ff;
      color: white;
      border-radius: 30px;
      padding: 0.5rem;
      padding-left: 1rem;
      padding-right: 1rem;
      font-size: 0.8rem;
    }

    @media (max-width: 991.98px) {
      .share-document-modal .modal{
        --modal-width: 60vw;
      }
    }

    @media (max-width: 768px) {
      .share-document-modal .modal{
        --modal-width: 80vw;
      }
    }

    @media (max-width: 575.98px) {
      .share-document-modal .modal{
        --modal-width: 90vw;
      }
    }
</style>
