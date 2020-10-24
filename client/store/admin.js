
export const state = () => ({
    requests: {
        list: [],
        paginationData: undefined,
    }
})

export const mutations = {
    fetchAllRequests(state, data){
        for(let datum of data.data){
            if(!state.requests.list.some(el => el.id == datum.id)){
                state.requests.list.push(datum)
            }
        }

        state.requests.paginationData = data
    },
}//end mutations

export const actions = {
    fetchAllRequests({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.get("/api/admin/requests")
                    .then(response => response.data)
                    .then((response) => {
                        commit("fetchAllRequests", response.data)
                        resolve()
                    }).catch(err => reject(err))
        })
    },
}//end actions

export const getters = {
    requests(state){
        return state.requests.list
    }
}