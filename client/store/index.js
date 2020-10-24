export const state = () => ({
    env: {},
    possibleThemes: ["dark", "light"],
    theme: "", // default | light | dark
    default: {
        theme: ""
    }
})

export const mutations = {
    updateUser(state, data) {
        Object.assign(state.auth.user, data)
    },
    setEnv(state, data) {
        state.env = data
    },
    setDefaultTheme(state, data) {
        state.default.theme = data
        state.theme = data
    },
    setTheme(state, theme) {
        if (!state.possibleThemes.some(el => el == theme))
            return
        state.theme = theme
    },
}

export const actions = {
    nuxtServerInit({ commit }) {
        if (process.server) {
            commit('setEnv', {
                LARAVEL_ECHO_URL: process.env.LARAVEL_ECHO_URL || ""
            })
            commit('setDefaultTheme', process.env.THEME_DEFAULT || 'light')
        }
    },
    login({ state, dispatch }, { email, password }) {
        return new Promise((resolve, reject) => {
            this.$auth.loginWith("password_grant", {
                data: {
                    username: email,
                    password: password,
                }
            }).then(response => {
                dispatch("laravel_echo/initializeLaravelEcho", {}, {root: true})
                resolve()
            })
                .catch(err => {
                    reject(err.response)
                })
        })
    }, //end function login
    logout({ }) {
        this.$auth.logout();
    }, //end function logout

    register({ }, data) {
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/register", data)
                .then(() => resolve())
                .catch(err => {
                    reject(err.response)
                })
        })
    }, //end function register

    updateUser({ commit }, formData) {
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/update-user", formData)
                .then((response) => {
                    commit("updateUser", response.data)
                    resolve()
                }).catch(err => {
                    // debugger
                    if(err.response && err.response.status == 406){
                        reject(err.response.data.errors)
                    }else{
                        reject(err)
                    }
                })
        })
    }, //end function updateUser
}