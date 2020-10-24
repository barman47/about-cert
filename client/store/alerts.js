import Vue from "vue"

export const state = () => ({
    list: [],
    paginationData: undefined,
})

export const mutations = {
    getInitAlerts(state, {data}){
        state.paginationData = data

        for(let d of data.data){
            if(!state.list.some(el => el.id == d.id)){
                state.list.push(d)
            }
        }
    },
    receiveAlert(state, data){
        if(!state.list.some(el => el.id == data.id)){
            state.list.unshift(data);
        }
    },
    markAlertAsViewed(state, {id}){
        let alertIndex = state.list.findIndex(el => el.id == id)
        let alert = state.list[alertIndex]
        alert.viewed = 1

        Vue.set(state.list, alertIndex, alert)
    }
}

export const actions = {
    getInitAlerts({commit}){
        return new Promise((resolve, reject) => {
            this.$axios.get("/api/alerts")
                .then(response => {
                    commit("getInitAlerts", response.data)
                    resolve()
                }).catch(err => {
                    reject(err)
                })
        })
    },
    markAlertAsViewed({commit, state}, {id}){
        return new Promise((resolve, reject) => {
            if(state.list.find(el => el.id == id).viewed != 1){
                this.$axios.post("/api/alerts/mark-as-viewed", {alert_id : id})
                    .then(() => {
                        commit("markAlertAsViewed", {id})
                        resolve()
                    }).catch(err => reject(err))
            }
            else{
                resolve()
            }
        })
    },
    notificationsPaneViewed({commit, state, dispatch}){
        return new Promise((resolve, reject) => {
            for(let alert of state.list){
                if(!!alert.should_view_on_open){
                    dispatch("markAlertAsViewed", alert)
                }
            }

            resolve()
        })
    }
}

export const getters = {
    hasUnread(state){
        if(state.list.length == 0)
            return false
        return state.list.some(el => el.viewed == 0)
    }
}