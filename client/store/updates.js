import Vue from "vue"
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
    eventsPaginationData: {
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
    add(state, update) {
        if (state.list.findIndex(el => el.id == update.id) < 0)
            state.list.unshift(update)
    },
    addOrReplace(state, update) {
        let index = state.list.findIndex(el => el.id == update.id)

        if (index < 0) {
            state.list.unshift(update)
            return
        }

        state.list[index] = update
    },
    remove(state, { todo }) {
        state.list.splice(state.list.indexOf(todo), 1)
    },
    addAll(state, data) {
        Object.assign(state.paginationData, {
            total: data.total,
            per_page: data.per_page,
            current_page: data.current_page,
            first_page_url: data.first_page_url,
            last_page_url: data.last_page_url,
            prev_page_url: data.prev_page_url,
            path: data.path,
            from: data.from,
            to: data.to,
        })

        data.data.forEach(update => {
            if (state.list.findIndex(el => el.id == update.id) < 0) {
                state.list.push(update)
            }
        });
    },
    addAllEvents(state, data) {
        Object.assign(state.eventsPaginationData, {
            total: data.total,
            per_page: data.per_page,
            current_page: data.current_page,
            first_page_url: data.first_page_url,
            last_page_url: data.last_page_url,
            prev_page_url: data.prev_page_url,
            path: data.path,
            from: data.from,
            to: data.to,
        })

        data.data.forEach(update => {
            if (state.list.findIndex(el => el.id == update.id) < 0)
                state.list.push(update)
        });
    },
    comment(state, { data, comment }) {
        comment = Object.assign({ comments: [] }, comment)
        if (data.type == "post") {
            let index = state.list.findIndex(el => el.id == data.id)
            let post = state.list[index]
            let comments = post.comments

            if (comments.some(el => el.id == comment.id))
                return

            comments.unshift(comment)
            post.comments = comments

            Vue.set(state.list, index, post)
        } else if (data.type == "comment") {
            let index = state.list.findIndex(el => el.id == data.post_id)

            state.list[index].comments.find(el => el.id == data.id).comments.push(comment)
            state.list.splice(index, 1, state.list[index])
        }
    },

    like(state, data) {
        if (data.type == "post") {
            let index = state.list.findIndex(el => el.id == data.id)

            if (state.list[index].liked)
                state.list[index].likes_count -= 1
            else
                state.list[index].likes_count += 1
            state.list[index].liked = (state.list[index].liked + 1) % 2

            state.list.splice(index, 1, state.list[index])
        } else if (data.type == "comment") {
            let index = state.list.findIndex(el => el.id == data.post_id)
            let comment = state.list[index].comments.find(el => el.id == data.id)

            comment.liked = comment.liked == 1 ? 0 : 1
            comment.likes_count += comment.liked == 1 ? 1 : -1
            state.list.splice(index, 1, state.list[index])
        }
    },

    sharePost(state, data) {
        let x = state.list.find(el => el.id == data.post_id)
        x.shares_count += 1

        state.list = state.list
    },
    followUser(state, data) {
        let temp = Object.assign(state.list.find(el => el.id == data.post_id), {
            is_following_user: 1
        })

        state.list.splice(state.list.findIndex(el => el.id == data.post_id), 1, temp)
    },
    unfollowUser(state, data) {
        let temp = Object.assign(state.list.find(el => el.id == data.post_id), {
            is_following_user: 0
        })

        state.list.splice(state.list.findIndex(el => el.id == data.post_id), 1, temp)
    },
    updateLikesCount(state, { id, likes_count }) {
        let index = state.list.findIndex(el => el.id == id)

        if (index >= 0) {
            let item = state.list[index]
            item.likes_count = likes_count

            Vue.set(state.list, index, item)
        }
    },
    updateSharesCount(state, { id, shares_count }) {
        let index = state.list.findIndex(el => el.id == id)

        if (index >= 0) {
            let item = state.list[index]
            item.shares_count = shares_count

            Vue.set(state.list, index, item)
        }
    }
}

export const actions = {
    fetch(context, { count, page }) {
        return new Promise((resolve, reject) => {
            this.$axios.get("/api/posts?count=" + count + "&page=" + page)
                .then(response => {
                    context.commit("addAll", response.data)
                    context.dispatch("listenToPostEvents")
                    resolve()
                }).catch(err => reject(err.response))
        })
    },
    fetchSingle(context, id) {
        return new Promise((resolve, reject) => {
            this.$axios.get("/api/posts/" + id)
                .then((response) => {
                    let update = response.data
                    context.commit("addOrReplace", update)
                    context.dispatch("listenToPostEvents")
                    resolve()
                }).catch(err => reject(err.response))
        })
    },
    create(context, data) {
        return new Promise((resolve, reject) => {

            this.$axios.post("/api/posts", data)
                .then(response => {
                    context.dispatch("fetchSingle", response.data)
                        .then(() => resolve())
                        .catch((err) => reject(err))
                })
                .catch(err => reject(err.response))
        })
    },
    comment(context, data) {
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/comments", data)
                .then((response) => {
                    context.commit("comment", { data: data, comment: response.data.comment })
                    resolve()
                })
                .catch(err => reject(err.response))
        })
    },
    like(context, data) {
        context.commit("like", data)
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/like", data)
                .then((response) => {
                    resolve()
                })
                .catch(err => reject(err.response))
        })
    },
    sharePost(context, data) {
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/posts/share", data)
                .then(response => {
                    if (response.status == 201)
                        context.commit("sharePost", data)
                    resolve()
                })
                .catch(err => reject(err.response))
        })
    }, //end function sharePost

    listenToPostEvents({ commit, state }, data) {
        return new Promise((resolve, reject) => {
            for (let item of state.list) {
                window.Echo.private('post.' + item.id)
                    .listen('.post.liked', (e) => {
                        commit("updateLikesCount", e)
                    }).listen(".post.shared", (e) => {
                        console.log(e)
                        commit("updateSharesCount", e)
                    })
            }

            resolve()
        })
    },
    listenToSinglePostEvents({ commit }, { id }) {
        return new Promise((resolve, reject) => {
            window.Echo.private("post." + id)
                .listen(".post.commented", e => {
                    console.log(e)
                    let data = {
                        data: {
                            type: "post",
                            id: e.id,
                        },
                        comment: e.comment
                    }
                    commit("comment", data)
                })

            resolve()
        })
    },
}

export const getters = {

}
