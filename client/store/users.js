export const state = () => ({

})

export const actions = {
  follow({ commit }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post("/api/users/follow", data)
        .then(() => {
          const updateStore = [
            "updates/followUser",
            "other_users/followUser"
          ]

          for(let item of updateStore){
            try {
              commit(item, data, { root: true })
            } catch (e) {}
          }

          commit("profile/followUser", null, { root: true })
          resolve()
        })
        .catch(err => reject(err))
    })
  },
  unfollow({ commit }, data) {
    return new Promise((resolve, reject) => {
      this.$axios.post("/api/users/unfollow", data)
        .then(() => {
          const updateStore = [
            "updates/unfollowUser",
            "other_users/unfollowUser"
          ]
          
          for(let item of updateStore){
            try {
              commit(item, data, { root: true })
            } catch (e) {}
          }
          
          commit("profile/unfollowUser", null, { root: true })
          resolve()
        })
        .catch(err => reject(err))
    })
  },
  searchForUsers({ }, data) {
    return new Promise((resolve, reject) => {
      let searchString = data.searchString
      let perPage = data.perPage ? data.perPage : 20
      let page = data.page ? data.page : 1
      this.$axios.get(`api/users/search?query=${searchString}&per_page=${perPage}&0=${page}`)
        .then(response => {
          resolve(response.data)
        })
        .catch(err => {
          console.log("An error occurred while fetching for users")
        })
    })
  },
}
