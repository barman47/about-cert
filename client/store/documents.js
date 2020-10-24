// const FileDownload = require("js-file-download")
import Vue from 'vue'
export const state = () => ({
    list: {
        deleted : [],
        notDeleted: []
    },
    sortBy: "name",
    sortOrder: "ASC",
    breadCrumb: [],
    visitedIds: [],
})

// ===================================================================================
// =                           MUTATIONS
// ===================================================================================

export const mutations = {
    getAllDocuments(state, { response, path }) {
        if (!state.visitedIds.some(el => el == (path || "root")))
            state.visitedIds.push(path || "root")

        let data = response.documents

        for (let val of data) {
            if (![...state.list.deleted, ...state.list.deleted].some(el => el.id == val.id)){
                if(val.deleted ==  0){
                    state.list.notDeleted.push(val)
                }else{
                    state.list.deleted.push(val)
                }
            }
        }//end for loop

        //order the list
        let order = state.sortOrder == "ASC" ? 1 : -1
        state.list.notDeleted.sort((a, b) => a[state.sortBy].toLowerCase() > b[state.sortBy].toLowerCase() ? 1 * order : -1 * order)
        state.list.deleted.sort((a, b) => a[state.sortBy].toLowerCase() > b[state.sortBy].toLowerCase() ? 1 * order : -1 * order)
    },// end method getAllDocuments
    makeDocument(state, data) {
        if (![...state.list.deleted, ...state.list.deleted].some(el => el.id == data.id)){
            if(data.deleted ==  0){
                state.list.notDeleted.push(data)
            }else{
                state.list.deleted.push(data)
            }
        }
    },
    deleteDocument(state, data){
        let deleted = state.list.notDeleted.splice(state.list.notDeleted.findIndex(el => el.id == data.document_id), 1)[0]

        if(!state.list.deleted.some(el => el.id == deleted.id))
            state.list.deleted.push(deleted)
    },
    permanentlyDeleteDocument(state, data){
        state.list.notDeleted.splice(state.list.notDeleted.findIndex(el => el.id == data.document_id), 1)
        state.list.deleted.splice(state.list.notDeleted.findIndex(el => el.id == data.document_id), 1)
    },
    emptyTrash(state){
        state.list.deleted = []
    },
    restoreDocument(state, data){
        let restored = state.list.deleted.splice(state.list.deleted.findIndex(el => el.id == data.document_id), 1)[0]

        if(!state.list.notDeleted.some(el => el.id == restored.id))
            state.list.notDeleted.push(restored)
    },
    restoreAll(state){
        let deleted = state.list.deleted.slice()
        for(let restored of deleted){
            if(!state.list.notDeleted.some(el => el.id == restored.id))
                state.list.notDeleted.push(restored)
        }

        state.list.deleted = []
    },
    resortDocuments(state) {
        //order the list
        let order = state.sortOrder == "ASC" ? 1 : -1
        state.list.notDeleted.sort((a, b) => a[state.sortBy].toLowerCase() > b[state.sortBy].toLowerCase() ? 1 * order : -1 * order)
        state.list.deleted.sort((a, b) => a[state.sortBy].toLowerCase() > b[state.sortBy].toLowerCase() ? 1 * order : -1 * order)
    },
    getBreadCrumb(state, data) {
        state.breadCrumb = data
    },
    sortBy(state, value){
        state.sortBy =  value
    },
    toggleSortOrder(state, value){
        if(value == true){
            state.sortOrder = state.sortOrder == "ASC" ? "DESC" : "ASC"
        }
    },
    copyDocumentToFolder(state, data){
        let raw = data.raw
        let newDocument = data.newDocument
        if (!state.list.notDeleted.some(el => el.id == newDocument.id))
            state.list.notDeleted.push(newDocument)
    },
    moveDocumentToFolder(state, data){
        let raw = data.raw
        let newDocument = data.newDocument

        let index = state.list.notDeleted.findIndex(el => el.id == newDocument.id)

        if(index < 0)
            state.list.notDeleted.push(newDocument)
        else{
            state.list.notDeleted.splice(index, 1, newDocument)
        }
    },
    
    
}//end mutations object

// ===================================================================================
// =                           ACTIONS
// ===================================================================================

export const actions = {
    makeDocument({ commit }, formData) {
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/create", formData)
                .then(response => {
                    commit("makeDocument", response.data)
                    commit("resortDocuments")
                    resolve()
                }).catch((err) => reject(err))
        })
    },
    getBreadCrumb({ commit }, data) {
        if (data && data.id)
            return new Promise((resolve, reject) => {
                this.$axios.get("/api/documents/get-bread-crumb/" + id)
                    .then(response => {
                        commit("getBreadCrumb", response.data)
                        resolve()
                    }).catch(() => {
                        commit("getBreadCrumb", [])
                        reject()
                    })
            })
        else
            return new Promise((resolve, reject) => {
                commit("getBreadCrumb", [])
                resolve()
            })
    },
    sortDocuments({commit, state}, {sortBy, toggleOrder}){
        if(sortBy)
            commit("sortBy", sortBy)
        if(toggleOrder == true)
            commit("toggleSortOrder", toggleOrder)

        commit("resortDocuments")
    },
    forgeDocument({commit, state}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/forge", data)
                        .then(response => {
                            if(state.list.length > 0){
                                commit("makeDocument", response.data.document)
                            }
                            if(data.download == "true"){
                                resolve(response.data.download_url)
                                // this.$axios.get(response.data.download_url)
                                //     .then(response2 => {
                                //         // FileDownload(response2.data, response.data.document.name + "." + response.data.document.extension)
                                //         console.log(response2)
                                //         // this.$download(new Blob([response2.data], {type: response.data.mime_type}),"download.docx",response.data.mime_type)
                                //     })
                            }
                            resolve(response.data)
                        })
                        .catch(err => console.log(err.response))
        })
    },
    deleteDocument({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/delete", data)
                .then(response => {
                    commit("deleteDocument", data)
                    commit("resortDocuments")
                    resolve()
                })
        })
    },
    permanentlyDeleteDocument({commit}, {document_id}){
        return new Promise((resolve, reject) => {
            let data = {
                document_id, document_id
            }

            this.$axios.post("/api/documents/permanently-delete", data)
                .then(response => {
                    commit("permanentlyDeleteDocument", data)
                    resolve()
                }).catch(err => reject(err.response))
        })
    },
    emptyTrash({commit}){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/empty-trash")
                .then(response => {
                    commit("emptyTrash")
                    resolve()
                }).catch(err => reject(err))
        })
    },
    shareDocument({}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/share", data)
                .then(response => resolve(response.data))
        })
    },
    copyDocumentToFolder({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/copy-document-to-folder", data)
                    .then(response => {
                        commit("copyDocumentToFolder", {newDocument : response.data.document, raw: data})
                        resolve()
                    })
        })
    },
    moveDocumentToFolder({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/move-document-to-folder", data)
                    .then(response => {
                        commit("moveDocumentToFolder", {newDocument : response.data.document, raw: data})
                        resolve()
                    })
        })
    },
    requestDocumentSignature({}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/signatures/create-signature-request", data)
            .then(response => {
                resolve(response.data)
            })
            .catch(err => {
                reject(err)
            })
        })
    }, //end function requestDocumentSignature
    restoreDocument({commit}, {document_id}){
        return new Promise((resolve, reject) => {
            let data = {
                document_id: document_id
            }
            this.$axios.post("/api/documents/restore-document", data)
                .then(response => {
                    commit("restoreDocument", data)
                    commit("resortDocuments")
                    resolve()
                })
                .catch(err => reject(err.response))
        })
    },
    restoreAll({commit}){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/restore-all-documents")
                .then(response => {
                    commit("restoreAll")
                    commit("resortDocuments")
                    resolve()
                })
                .catch(err => reject(err))
        })
    },
}

// ===================================================================================
// =                           GETTERS
// ===================================================================================

export const getters = {
    documents(state) {
        return state.list.notDeleted
    },
    deletedDocuments(state) {
        return state.list.deleted
    },
    breadCrumb(state) {
        let list = state.breadCrumb
        return list.slice().map(el => {
            return {
                text: el.text,
                link: "/documents/" + el.id
            }
        })
    },
}
