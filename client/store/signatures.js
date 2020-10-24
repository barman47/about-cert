export const state = () => ({
    list: [],
})
  
export const mutations = {
    uploadSignature(state, data){
        if(!state.list.some(el => el.id == data.id))
            state.list.push(data)
    },
    fetchAllSignatures(state, data){
        for(let val of data){
            if(!state.list.some(el => el.id == val.id))
                state.list.push(val)
        }
    },
}

export const actions = {
    uploadSignature({commit}, formData){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/signatures/create", formData)
                .then((response) => {
                    commit("uploadSignature", response.data)
                    resolve()
                }).catch((err) => reject(err.response))
        })
    },
    fetchAllSignatures({commit}){
        return new Promise((resolve, reject) => {
            this.$axios.get("/api/signatures/get-all")
                        .then(response => {
                            commit("fetchAllSignatures", response.data)
                            resolve()
                        }).catch((err) => reject(err.response))
        })
    },
}