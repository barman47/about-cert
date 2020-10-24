import Vue from "vue"
export const state = () => ({
	list: []
})

export const mutations = {
	addUser(state, data) {
		if (!state.list.some(el => el.id == data.id))
			state.list.push(data)
	},
	addEvents(state, {userId, paginationData}){
		const index = state.list.findIndex(el => el.id == userId)

		if (index < 0){
			return
		}

		let user = state.list[index]

		if(user.events_pagination_data){
			paginationData.data = [...user.events_pagination_data.data, ...paginationData.data]
		}

		user.events_pagination_data = paginationData

		Vue.set(state.list, index, user)
	},
	getProfileData(state, {userId, profileData}){
		const index = state.list.findIndex(el => el.id == userId)

		if (index < 0){
			return
		}

		let user = state.list[index]
		user.profile_data = profileData
		Vue.set(state.list, index, user)
	},
	followUser(state, data) {
		const index = state.list.findIndex(el => el.id == data.user_id)

		if (index < 0){
			return
		}

		let user = state.list[index]
		user.is_following_user  = 1
		user.followers_count += 1
		Vue.set(state.list, index, user)
    },
    unfollowUser(state, data) {
        const index = state.list.findIndex(el => el.id == data.user_id)

		if (index < 0){
			return
		}

		let user = state.list[index]
		user.is_following_user  = 0
		user.followers_count -= 1
		Vue.set(state.list, index, user)
    },
}

export const actions = {
	getUser({ commit }, { id }) {
		return new Promise((resolve, reject) => {
			this.$axios
				.get(`/api/users/${id}`)
				.then(response => {
					commit("addUser", response.data.user)
					resolve()
				})
				.catch(err => reject(err));
		})
	},
	getEvents({commit} , {id}){
		return new Promise((resolve, reject) => {
			this.$axios
				.get(`/api/users/${id}/events`)
				.then(response => response.data)
				.then(response => {
					commit("addEvents", {userId: id, paginationData: response.data})

					for(let item of response.data.data.reverse()){
						commit("updates/add", item, {root: true})
					}
					resolve()
				})
				.catch(err => reject(err));
		})
	},
	getProfileData({commit}, {id}){
		return new Promise((resolve, reject) => {
			this.$axios
				.get(`/api/users/${id}/profile`)
				.then(response => {
					commit("getProfileData", {userId: id, profileData: response.data})
					resolve()
				})
				.catch(err => reject(err));
		})
	},
}