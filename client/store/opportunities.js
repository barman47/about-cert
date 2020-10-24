export const state = () => ({
    list: [],
    paginationData: {
        total: undefined,
        per_page: undefined,
        current_page: undefined,
        first_page_url: undefined,
        last_page_url: undefined,
        prev_page_url: undefined,
        path: undefined,
        from: undefined,
        to: undefined,
    },
})

export const mutations = {
    add(state, data){
        if(!state.list.some(el => el.id == data.id))
            state.list.unshift(data)
    },
    addAll(state, collection){
        collection.forEach(data => {
            if(!state.list.some(el => el.id == data.id))
                state.list.push(data)
        });
    },
    like(state, data){
        let index = state.list.findIndex(el => el.id == data.id)

        if(state.list[index].liked)
            state.list[index].likes_count -= 1
        else
            state.list[index].likes_count += 1
        state.list[index].liked = (state.list[index].liked + 1) % 2

        state.list.splice(index, 1, state.list[index])
    },
}

export const actions = {
    create({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/opportunities/create", data)
                .then(response => {
                    commit("add", response.data)
                    resolve()
                }).catch(err => reject(err.response))
        })
    }, //end function create
    like(context, data){
        context.commit("like", data)
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/like", data)
                .then((response) => {
                    resolve()
                })
                .catch(err => reject(err.response))
        })
    },    
}//end actions object

export const getters  = {
    ownOpportunities(state, getters, rootState){
        return state.list.filter(el => el.user_id == rootState.auth.user.id)
    }
}