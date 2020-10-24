<template>
    <div class="dir-content" :class="{'content-child': !isParent}">
        <div class="single-dir" :class="{'dir-parent': !!isParent, 'dir-child' : !isParent}" @click="getSubFolders()">
            <div class="dir-image"></div>
            <div class="dir-name">{{dir ? dir.name : "Received"}}</div>
            <div class="checked-indicator" v-show="!!isParent ? !checkObj.id : checkObj.id == dir.id"></div>
        </div>
        <share-document-dir :user_id="user_id" :checkObj="checkObj" v-for="dir in subDirs" :key="dir.id" :dir="dir"></share-document-dir>
        <!-- <pre>{{subDirs}}</pre> -->
    </div>
</template>

<script>
import ShareDocumentDir from "~/components/ShareDocumentDir"
export default {
    name: "ShareDocumentDir",
    components: {
        ShareDocumentDir
    },
    props: ["isParent", "dir", "checkObj", "user_id"],
    data(){
        return {
            subDirs: [],
            fetched: false,
            checked: false,
        }
    },
    methods:{
        getSubFolders(){

            this.checkObj.id = !!this.isParent ? undefined : this.dir.id

            if(!this.fetched){
                const folderId = this.dir ? this.dir.id : 'received'
                let attempt = this.$axios
                                .get(`/api/documents/received-sub-folders?user_id=${this.user_id}&folder_id=${folderId}`)
    
                attempt.then(response => {
                    this.subDirs = response.data.sub_folders
                    this.fetched = true
                })
            }else{
                
            }
        },
    }
}
</script>

<style scoped>
    .dir-content{
        padding: 0.3rem;
        font-size: 0.65rem;
    }

    .dir-content.content-child{
        margin-left: 1rem;
        padding: 0;
    }

    .single-dir{
        display: flex;
        align-items: center;
        height: 30px;
        border-bottom: 0.2px solid #808080;
        cursor:pointer;
    }

    .dir-parent.single-dir{
        /* grid-template-columns: 30px 1fr; */
        font-size: 0.7rem;
        border-bottom: 0.2px solid #c4c4c4;
    }
/* 
    .dir-child.single-dir{
        margin-left: 1rem;
    } */

    .dir-image{
        height:100%;
        width:30px;
        background-image: url("/png/sm-dir-image.png");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: left center;
        padding: 0.2rem;
        background-origin: content-box;
    }

    .dir-child.single-dir .dir-image{
        height: 80%;
    }

    .dir-name{
        display: flex;
        align-items: center;
        overflow: hidden;
        width: 100%;
        text-overflow: ellipsis;
        color: white;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        
    }

    .checked-indicator{
        height: 100%;
        width: 30px;
        background-image: url("/png/check-indicator.png");
        background-position: center center;
        background-size: contain;
        background-repeat: no-repeat;
        padding: 0.5rem;
        background-origin: content-box;
    }

</style>