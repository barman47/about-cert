<template>
    <div class="documents-page-content">
        <div class="search-sort">
            <div class="search-container">
                <input class="input" type="text" placeholder="search trash...">
                <div class="icon"></div>
            </div>
            <div class="other-actions">
                <div class="button" @click="restoreAll()"><span class="icon" style="background-image: url('/png/restore-all-icon.png')"></span><span>Restore All</span></div>
                <div class="button" @click="emptyTrash()"><span class="icon" style="background-image: url('/png/empty-trash.png')"></span><span>Empty Trash</span></div>
                <div class="button" @click="isList = !isList">
                    <span v-show="!isList" class="icon" style="background-image: url('/png/trash-change-view-to-list.png')" ></span>
                    <span v-show="!!isList" class="icon" style="background-image: url('/png/trash-change-view-to-grid.png')" ></span>
                </div>
            </div>
        </div>

        <div v-if="searchResults.length > 0">
            <h3 style="margin-top: 1.5rem; margin-bottom: 0.5rem;">Search Results &nbsp; <span class="document-search-result-close-button" @click="resetSearchResults()">&times;</span></h3>

            <files-display intent="trash" :isList="isList" :files="searchResults"></files-display>
        </div>
        <files-display intent="trash" :isList="isList" :files="[...folders, ...files]"></files-display>
    </div>
</template>

<script>
    import {mapGetters} from "vuex"

    import FilesDisplay from "~/components/FilesDisplay";
    import Modal from "~/components/Modal";
    import InputLabel from "~/components/InputLabel";

    export default {
        components: {
            FilesDisplay,
            Modal,
            InputLabel,
        },
        layout: "document",
        async fetch({store, $axios, params, redirect}){
            if(!params.id && !store.state.documents.visitedIds.some(el => el == "root")){
                await $axios.get("/api/documents/root")
                        .then(response => {
                            store.commit("documents/getAllDocuments", {response: response.data})
                        }).catch(() => {
                            return redirect("/updates")
                        })
            }else if(params.id && !store.state.documents.visitedIds.some(el => el == params.id)){
                await $axios.get("/api/documents/folder/" + params.id)
                        .then(response => {
                            store.commit("documents/getAllDocuments", {response: response.data, path: params.id})
                        }).catch(() => {
                            return redirect("/documents")
                        })
            }
        },
        data(){
            return {
                searchResults: [],
                isList: false,
            }
        },
        computed: {
            ...mapGetters({
                rawDocuments: "documents/deletedDocuments",
            }),
            documents(){
                let rawDocuments = this.rawDocuments
                
                if(!this.$nuxt.$route.params.id)
                    return rawDocuments.filter(el => el.documentable_type.toLowerCase() == "root")
                else{
                    return rawDocuments.filter(el => el.documentable_id.toLowerCase() == this.$nuxt.$route.params.id)
                }
            },
            files(){
                return this.documents.filter(el => el.type.toLowerCase() != 'folder')
            },
            folders(){
                return this.documents.filter(el => el.type.toLowerCase() == "folder")
            },
        },
        methods: {
            resortDocuments(){
                const value = this.$refs.sortInputField.value
                if(value)
                    this.$store.dispatch("documents/sortDocuments", {sortBy: value})
            },
            toggleDocumentOrder(){
                this.$store.dispatch("documents/sortDocuments", {toggleOrder: true})
            },
            openCreateFolderModal(){
                this.$refs.createFolderModal.openModal()
            },
            createFolder(){
                this.$refs.createFolderInput.$el.setAttribute("disabled", "true")
                this.$refs.createFolderButton.setAttribute("disabled", "true")
                this.$refs.createFolderButton.classList.add("loading")
                const data = {
                    name: this.$refs.createFolderInput.$el.value,
                    folder_id: this.$nuxt.$route.params.id, 
                    type: "folder",
                }//end the data Object

                if(!data.name){
                    this.$refs.createFolderButton.classList.add("error")
                    setTimeout(() => {
                        this.$refs.createFolderButton.classList.remove("error")
                    }, 2000);
                    return
                }

                let attempt = this.$store.dispatch("documents/makeDocument", data)

                attempt.then(() => {
                    this.$refs.createFolderInput.$el.value = ""
                    this.$refs.createFolderModal.closeModal()
                }).catch(() => {
                    this.$refs.createFolderButton.classList.add("error")
                    setTimeout(() => {
                        this.$refs.createFolderButton.classList.remove("error")
                    }, 2000);
                }).finally(() => {
                    this.$refs.createFolderInput.$el.removeAttribute("disabled")
                    this.$refs.createFolderButton.removeAttribute("disabled", "true")
                    this.$refs.createFolderButton.classList.remove("loading")
                })
            },
            searchDocuments(){
                this.searchResults = []

                const searchString = this.$refs.documentSearchInput.value

                let attempt = this.$axios.get(`/api/documents/search?query=${searchString}&per_page=20&0=1`)

                attempt.then((response) => {
                    this.searchResults = response.data.data
                })
            },
            resetSearchResults(){
                this.searchResults = []
            },
            restoreAll(){
                let attempt = this.$store.dispatch("documents/restoreAll")
            },
            emptyTrash(){
                let attempt = this.$store.dispatch("documents/emptyTrash")
            }
        }
    }
</script>


<style scoped>
    input:focus,
    select:focus {
        outline: none;
    }
    .documents-page-content{
        color: #7574A0;
    }

    .search-sort{
        display: flex;
        align-items: center;
        justify-content: space-between;

        --button-height: 1.7rem;

        margin-bottom: 2rem;
    }

    .search-container{
        display: flex;
        align-items: center;
        height: var(--button-height);
    }

    .search-container .input{
        height: 100%;
        width: 12rem;

        background: transparent;
        border: none;
        border-radius: 3px;

        border: 1.5px solid white;

        margin: 0;
        font-size: 0.7rem;
        padding-right: 5px;
        padding-left: 5px;

        color: #000;
    }

    .search-container .input::placeholder{
        color: #9b9b9b;
    }

    .search-container .icon{
        height: 100%;
        width: 2rem;
        border: none;
        border-radius: 3px;

        background-color: #27debf;
        background-image: url("/png/search2.png");
        background-position: center center;
        background-size: contain;
        background-origin: content-box;
        background-repeat: no-repeat;
        
        padding: 0.45rem;
        margin: 0;

        cursor: pointer;
    }

    .other-actions{
        display: flex;
        align-items: center;
    }

    .other-actions .button{
        background: white;
        border-radius: 3px;
        height: var(--button-height);
        padding: 0.3rem;
        padding-right: 0.5rem;
        padding-left: 0.5rem;
        display: flex;
        align-items: center;
        color: #797979;
        font-size: 0.7rem;

        cursor: pointer;
        margin-right: 10px;

        display: flex;
        align-items: center;
    }

    .other-actions .button:hover{
        box-shadow: 1px 1px 2px 0px #a7a4a4;
    }

    .other-actions .button:last-child{
        margin-right: 0;
    }

    .other-actions .button  .icon{
        background-position: center center;
        background-size: contain;
        background-repeat: no-repeat;
        height: 100%;
        width: 1.5rem;
    }

    .other-actions .button span{
        margin-right: 5px;
    }

    .other-actions .button span:last-child{
        margin-right: 0;
    }

    .folder-heading{
        margin-top: 1.5rem; 
        margin-bottom: 0.5rem; 
        font-size: 1rem;
        display: flex;
        align-items: center;
    }

    .dark-theme .folder-heading{
        color: white;
    }

    .add-folder-icon{
        height: 1.2rem;
        width: 1.2rem;
        margin-left: 0.5rem;
        display: inline-block;
        background-image: url("/png/add.png");
        background-size: center center;
        background-size: contain;
        background-repeat: no-repeat;
        cursor: pointer;
    }

    .submit-create-folder-button{
        border: none;
        background: #0084ff;
        color: white;
        border-radius: 3px;
        height: 2rem;
    }

    .submit-create-folder-button:active,
    .submit-create-folder-button:focus{
        border: none;
        outline: none;
    }

    .submit-create-folder-button.error{
        background: #e86262;
    }


    /* /Inclisions */
    .document-search-result-close-button{
        height: 100%;
        width: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: white;
        background: #C4C4C4;
        clip-path: circle();
    }

    @media (max-width: 1199.98px) {

    }

    @media (max-width: 991.98px) {

    }

    @media (max-width: 767.98px) {

    }

    @media(max-width: 649.98px){
        .search-sort{
            flex-direction: column;
            align-items: flex-start;
        }

        .search-box-container{
            order: 1;
        }

        .search-input-container{
            order: 2;
        }
    }

    @media (max-width: 575.98px) {

    }
</style>