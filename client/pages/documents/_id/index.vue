<template>
    <div class="documents-page-content">
        <div class="document-page-heading-container">
            <h2 class="document-page-heading">Files (30)</h2>

            <div class="button" @click="fileDisplayIsList = !fileDisplayIsList">
                <span v-show="!fileDisplayIsList" class="icon" style="background-image: url('/png/trash-change-view-to-list.png')" ></span>
                <span v-show="!!fileDisplayIsList" class="icon" style="background-image: url('/png/trash-change-view-to-grid.png')" ></span>
            </div>
        </div>
        <bread-crumb :items="[...breadCrumbItems, ...breadCrumb]"></bread-crumb>
        <div class="search-sort">
            <div class="search-input-container">
                <select name="" id="" class="sort-input" @change="resortDocuments()" ref="sortInputField">
                    <option value="">Sort By</option>
                    <option value="name">Name</option>
                    <option value="created_at">Date</option>
                </select>
                <button class="toggle-sort-order-button" @click="toggleDocumentOrder()">T</button>
            </div>

            <div class="search-box-container">
                <input @keyup.enter="searchDocuments()" ref="documentSearchInput" type="text" class="search-box" placeholder="Search Documents">
                <div @click="searchDocuments()" class="search-icon-container"><div class="search-icon"></div></div>
            </div>
        </div>

        <div v-if="searchResults.length > 0">
            <h3 style="margin-top: 1.5rem; margin-bottom: 0.5rem;">Search Results &nbsp; <span class="document-search-result-close-button" @click="resetSearchResults()">&times;</span></h3>

            <files-display :files="searchResults"></files-display>
        </div>

        <h3 class="folder-heading">
            Folders <div class="add-folder-icon" @click="openCreateFolderModal()"></div>
            <modal id="create-folder-modal" 
                size="sm" 
                ref="createFolderModal"
                @submitted="createFolder()"
                >
                <template v-slot:header>Create Folder</template>

                <div>
                    <input-label inputId="name">Folder name:</input-label>
                    <input-field ref="createFolderInput" type="text" name="name" id="name"/>
                </div>
                <template v-slot:footer>
                    <div style="font-size: 0.8rem;">
                        <button ref="createFolderButton" type="submit" class="submit-create-folder-button">Submit</button>
                    </div>
                </template>
            </modal>
        </h3>

        <files-display :isList="fileDisplayIsList" :files="folders"></files-display>

        <h3 class="folder-heading">Documents</h3>

        <files-display :isList="fileDisplayIsList" :files="files"></files-display>
    </div>
</template>

<script>
    import {mapGetters} from "vuex"

    import BreadCrumb from "~/components/BreadCrumb";
    import FilesDisplay from "~/components/FilesDisplay";
    import Modal from "~/components/Modal";
    import InputLabel from "~/components/InputLabel";
    import InputField from "~/components/InputField";

    export default {
        // loading: {
        //     color: 'blue',
        //     height: '5px'
        //   },
        components: {
            BreadCrumb,
            FilesDisplay,
            Modal,
            InputLabel,
            InputField
        },
        layout: "document",
        async fetch({store, $axios, params, redirect}){
            // Get bread crumbs everytime there is a url id else commit an empty array
            if(params.id)
                $axios.get("/api/documents/get-bread-crumb/"+params.id)
                            .then(response => {
                                store.commit("documents/getBreadCrumb", response.data)
                            }).catch(() => {
                                store.commit("documents/getBreadCrumb", [])
                            })
            else
                store.commit("documents/getBreadCrumb", [])


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
                breadCrumbItems: [
                    {
                        link: "/documents",
                        text: "Documents"
                    },
                    {
                        link: "/documents",
                        text: "Folders"
                    },
                ],
                searchResults: [],
                fileDisplayIsList: false
            }
        },
        computed: {
            ...mapGetters({
                rawDocuments: "documents/documents",
                breadCrumb: "documents/breadCrumb",
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
                this.$refs.createFolderInput.getInputFieldElement().setAttribute("disabled", "true")
                this.$refs.createFolderButton.setAttribute("disabled", "true")
                this.$refs.createFolderButton.classList.add("loading")
                const data = {
                    name: this.$refs.createFolderInput.value(),
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
                    this.$refs.createFolderInput.getInputFieldElement().value = ""
                    this.$refs.createFolderModal.closeModal()
                }).catch((err) => {
                    this.$refs.createFolderButton.classList.add("error")
                    setTimeout(() => {
                        this.$refs.createFolderButton.classList.remove("error")
                    }, 2000);
                }).finally(() => {
                    this.$refs.createFolderInput.getInputFieldElement().removeAttribute("disabled")
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
            }
        },
        mounted(){
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

    .document-page-heading-container{
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .document-page-heading-container .button{
        --button-height: 1.7rem;

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
        /* margin-right: 10px; */

        display: flex;
        align-items: center;
    }

    .document-page-heading-container .button:hover{
        box-shadow: 1px 1px 2px 0px #a7a4a4;
    }

    .document-page-heading-container .button:last-child{
        margin-right: 0;
    }

    .document-page-heading-container .button  .icon{
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

    .document-page-heading{
        margin-top: 0.5rem; 
        margin-bottom: 0.5rem; 
        font-size: 1.2rem;
    }

    .dark-theme .document-page-heading{
        color: white;
    }

    .search-sort{
        margin-top: 10px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .search-input-container{
        order: 0;
        margin-top: 2.5px;
        margin-bottom: 2.5px;
    }

    .sort-input{
        height: 30px;
        background: white;
        padding-right: 3rem;
        border: 1px solid #E2E2E2;
        border-radius: 5px;

        color: #c4c4c4;
        padding-left: 0.5rem;
    }

    .dark-theme .sort-input{
        background: #555286;
        border-color: #41325a;
    }

    .toggle-sort-order-button{
        height: 30px;
        border: none;
        color: black;
        cursor: pointer;
        border: 1px #c4c4c4 solid;
        border-radius: 3px;
        background: #eee;
    }

    .dark-theme .toggle-sort-order-button{
        background: #555286;
        border-color: #41325a;
    }

    .toggle-sort-order-button:active{
        outline: none
    }

    .search-box-container{
        display: flex;
        align-items: center;
        margin-top: 2.5px;
        margin-bottom: 2.5px;
        order: 0;
        
    }



    .search-box{
        background: #FFFFFF;
        border: 1px solid #E2E2E2;
        box-sizing: border-box;
        border-radius: 5px;
        height: 30px;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: none;
        font-size: 0.8rem;
        color: #c4c4c4;
    }

    .dark-theme .search-box::placeholder{
        color: #c4c4c4;
    }

    .search-box-container .search-icon-container{
        background-color: #FFFFFF;
        border: 1px solid #E2E2E2;
        box-sizing: border-box;
        border-radius: 5px;
        height: 30px;
        width: 30px;
        padding: 0.5rem;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        border-left: none;
    }

    .dark-theme .search-box,
    .dark-theme .search-icon-container{
        background: #555286;
        border-color: #41325a;
        color: white;
    }

    .search-box-container .search-icon{
        height: 100%;
        width: 100%;
        background-image: url("/png/search.png");
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
        
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