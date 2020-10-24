export const state = () => ({
    details: undefined,
    fetched: false,
    allInterests: [],
    allLanguages: [],
    allJobtitles: [],
})

export const mutations = {
    fetch(state, data){
        state.details = data
        state.fetched = true
    },
    editJobtitle(state, data){
        state.details.job_title = data.job_title
    },
    createEducationDetail(state, data){
        state.details.academic_details.push(data)
    },
    createProject(state, data){
        state.details.projects.push(data)
    },
    createCertificateDetail(state, data){
        state.details.certificates.push(data)
    },
    createWorkExperience(state, data){
        state.details.work_experiences.push(data)
    },
    getAllInterests(state, data){
        state.allInterests = data
    },
    createInterest(state, data){
        state.details.interests.push(data)
    },
    createSkill(state, data){
        state.details.skills.push(data)
    },
    getAllLanguages(state, data){
        state.allLanguages = data
    },
    getAllJobTitles(state, data){
        state.allJobtitles = data
    },
    createLanguage(state, data){
        state.details.languages.push(data)
    },
    followUser(state){
      if(state.details)
        Object.assign(state.details, {
          following_count : state.details.following_count + 1
        })
    },
    unfollowUser(state){
      if(state.details && state.details.following_count >= 1)
        Object.assign(state.details, {
          following_count : state.details.following_count - 1
        })
    }
}

export const actions = {
    editJobtitle({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/update-user", data)
                .then(response => {
                    commit("editJobtitle", data)
                    resolve()
                })
                .catch(err => reject(err.response))
        })
    },
    createEducationDetail({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/academic-details/create", data)
                    .then(response => {
                        Object.assign(data, {id: response.data})
                        commit("createEducationDetail", data)
                        resolve()
                    })
                    .catch(err => reject(err.response))
        })
    },
    createProject({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/projects/create", data)
                    .then(response => {
                        Object.assign(data, {id: response.data})
                        commit("createProject", data)
                        resolve()
                    })
                    .catch(err => reject(err.response))
        })
    },
    createCertificateDetail({commit}, formData){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/certificates/create", formData)
                    .then(response => {
                        commit("createCertificateDetail", response.data)
                        resolve()
                    })
                    .catch(err => reject(err.response))
        })
    },

    fetchCertificateDocument(context, data){
        return new Promise((resolve, reject) => {
            this.$axios.get("/api/certificates/get-document?id="+ data.id, {responseType: "blob"})
                    .then(response => {
                        resolve(response.data)
                    })
                    .catch(err => {
                        reject(err.response)
                    })
        })
    },

    createWorkExperience({commit}, data){
        return new Promise((resolve, reject) => {
            this.$axios.post("/api/work-experiences/create", data)
                    .then(response => {
                        commit("createWorkExperience", response.data)
                        resolve()
                    })
                    .catch(err => reject(err.response))
        })
    },

    createInterest({commit,state}, data){
        return new Promise((resolve, reject) => {
            if(!state.details.interests.some(el => el.name.toLowerCase() == data.name.toLowerCase()))
                this.$axios.post("/api/interests/create", data)
                        .then(response => {
                            commit("createInterest", data)
                            resolve()
                        })
                        .catch(err => reject(err.response))
            else{
                reject({message: "Interest already exists"})
            }
        })
    },

    createSkill({commit,state}, data){
        return new Promise((resolve, reject) => {
            if(!state.details.skills.some(el => el.name.toLowerCase() == data.name.toLowerCase()))
                this.$axios.post("/api/skills/create", data)
                        .then(response => {
                            commit("createSkill", data)
                            resolve()
                        })
                        .catch(err => reject(err.response))
            else{
                reject({message: "Skill already exists"})
            }
        })
    },
    createLanguage({commit,state}, data){
        return new Promise((resolve, reject) => {
            if(!state.details.languages.some(el => el.name.toLowerCase() == data.name.toLowerCase()))
                this.$axios.post("/api/languages/create", data)
                        .then(response => {
                            Object.assign(data, {pivot: {proficiency: data.proficiency}})
                            commit("createLanguage", data)
                            resolve()
                        })
                        .catch(err => reject(err.response))
            else{
                reject({message: "Skill already exists"})
            }
        })
    },
}
