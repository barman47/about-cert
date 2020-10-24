import Vue from "vue"
export const state = ()  => ({
    templateGroups : [],
    savedTemplateGroups : [],
    templatesFetched: 0,
    paginationData: undefined,
    savedPaginationData: undefined,
})

export const mutations = {
    addTemplateGroups(state, data){
        for(let d of data.data){
            if(!state.templateGroups.some(el => el.id == d.id))
                state.templateGroups.push(d)
        }

        delete data.data

        if(!!data)
            state.paginationData = data
    },

    addSavedTemplateGroups(state, data){
        for(let d of data.data){
            if(!state.savedTemplateGroups.some(el => el.id == d.id))
                state.savedTemplateGroups.push(d)
        }

        delete data.data

        if(!!data)
            state.savedPaginationData = data
    },

    saveCV(state, {data, meta}){
        
        if(!state.savedTemplateGroups.some(el => el.id == meta.templateGroupId)){
            let temp = Object.assign({}, state.templateGroups.find(el => el.id == meta.templateGroupId))
            let tempT = Object.assign({}, temp.templates.find(el => el.id == data.template_id))

            Object.assign(temp, {
                templates: [tempT]
            })

            state.savedTemplateGroups.push(temp)
        }else{
            let index = state.savedTemplateGroups.findIndex(el => el.id == meta.templateGroupId)

            if(state.savedTemplateGroups[index].templates.some(el => el.id == data.template_id)){
                return
            }

            let temp = Object.assign({}, state.savedTemplateGroups[index])
            temp.templates.push(Object.assign({}, state.templateGroups.find(el => el.id == meta.templateGroupId).templates.find(el => el.id == data.template_id)))
            Vue.set(state.savedTemplateGroups, index, temp)
        }
    },
}

export const actions = {
    payForPack({}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/cv/pay-for-template", data)
                .then(response => {
                    resolve(response.data.payment_link)
                }).catch(err => reject(err.response))
        })
    },
    downloadCVInView({}, data){
        return new Promise((resolve, reject) => {
            this.$axios.get(`/api/cv/temp/generate/download-link?intent=${data.intent}&template_id=${data.template_id}`)
                .then(response => {
                    resolve(response.data.download_url)
                }).catch(err => {
                    reject(err)
                })
        })
    },
    jobKitRequest({}, data){
      return new Promise((resolve, reject) => {
        this.$axios.post(`/api/cv/request-for-tailored-cv`, data)
                .then(response => {
                    resolve(response.data.payment_link)
                }).catch(err => {
                    reject(err.response)
                })
      })
    },

    saveCV({commit}, {data, meta}){
      return new Promise((resolve, reject) => {
        let saveIntent = meta.saveIntent || 'save'

        if(saveIntent == "recompile"){
            saveIntent = "compile"
        }

        this.$axios.post(`/api/cv/${saveIntent}`, data)
                .then(response => {
                    if(saveIntent == "save")
                        commit("saveCV", {data, meta})
                    resolve()
                }).catch(err => {
                    console.log(err)
                    reject(err.response)
                })
      })
    },
}

export const getters = {

}
