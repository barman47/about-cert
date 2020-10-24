import Vue from "vue"

export const state = () => ({
    signatureReceivePaginationData: {},
    signatureReceivedMarkersLoaded: false,
    signatureReceivedMarkers: [],
    signatureFolder: {},

    signatureSendPaginationData: {},
    signatureSendMarkers: [],
    signatureSendMarkersLoaded: false,
})

export const mutations = {
    commitReceivedMarkers(state, data) {
        state.signatureReceivedMarkersLoaded = true
        state.signatureReceivePaginationData = data.data
        state.signatureReceivedMarkers = [...state.signatureReceivePaginationData.data, ...state.signatureReceivedMarkers]
    },
    fetchSignatureSendMarkers(state, data) {
        state.signatureSendMarkersLoaded = true
        state.signatureSendPaginationData = data.data
        state.signatureSendMarkers = [...state.signatureSendPaginationData.data, ...state.signatureSendMarkers]
    },
    commitSignatureFolder(state, data) {
        if (data)
            state.signatureFolder = data
    },
    sendMarkerViewed(state, {on, email}){
        let sendMarkerIndex = state.signatureSendMarkers.findIndex(el => el.id == on)

        let sendMarker = state.signatureSendMarkers[sendMarkerIndex]

        let recipientIndex = sendMarker.recipients.findIndex(el => el.email == email)

        if(recipientIndex < 0){
            return
        }

        sendMarker.recipients[recipientIndex].viewed = 1

        Vue.set(state.signatureSendMarkers, sendMarkerIndex, sendMarker)
    },
    sendMarkerSigned(state, {on, email}){
        let sendMarkerIndex = state.signatureSendMarkers.findIndex(el => el.id == on)

        let sendMarker = state.signatureSendMarkers[sendMarkerIndex]

        let recipientIndex = sendMarker.recipients.findIndex(el => el.email == email)

        if(recipientIndex < 0){
            return
        }

        sendMarker.recipients[recipientIndex].signed = 1

        Vue.set(state.signatureSendMarkers, sendMarkerIndex, sendMarker)
    },

    sendMarkerExecuted(state, {on}){
        let sendMarkerIndex = state.signatureSendMarkers.findIndex(el => el.id == on)

        let sendMarker = state.signatureSendMarkers[sendMarkerIndex]

        sendMarker.executed = 1

        Vue.set(state.signatureSendMarkers, sendMarkerIndex, sendMarker)
    }
}

export const actions = {
    downloadSignedDocumentToFolder({ commit }, data) {
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/documents/signatures/documents/download-signed/to-folder", data)
                .then(response => {
                    let document = response.data.document
                    commit("documents/makeDocument", document, { root: true })
                    commit("documents/resortDocuments", null, { root: true })
                    resolve(document)
                }).catch(err => reject(err.response))
        })
    },
    fetchSignatureSendMarkers({commit, dispatch}) {
        return new Promise((resolve, reject) => {
            this.$axios
                .get("/api/documents/signatures/documents/send-markers")
                .then(response => {
                    commit("fetchSignatureSendMarkers", response.data);
                    resolve()
                })
                .catch(err => reject(err))
        })
    },
    addEventListenerToASendMarker({commit}, {sendMarker}){
        return new Promise((resolve, reject) => {
            window.Echo.private("signature.send.marker." + sendMarker.id)
                .listen(".document.signed", e => {
                    console.log(e)
                    const email = e.signer.email
                    commit("sendMarkerSigned", {on: sendMarker.id, email})
                })
                .listen(".document.viewed", e => {
                    console.log(e)
                    const email = e.signer.email
                    commit("sendMarkerViewed", {on: sendMarker.id, email})
                })
                .listen(".document.executed", e => {
                    commit("sendMarkerExecuted", {on: sendMarker.id})
                })
            resolve();
        })
    },
}

export const getters = {
    signatureSendMarkers(state) {
        return state.signatureSendMarkers
    },
    signatureReceivedMarkers(state) {
        return state.signatureReceivedMarkers
    }
}