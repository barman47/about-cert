export const state = () => ({
})

// ===================================================================================
// =                           MUTATIONS
// ===================================================================================

export const mutations = {
}

export const actions = {
	payForPriviledge({}, data){
		return new Promise((resolve, reject) => {
			this.$axios.post("/api/documents/signatures/pay-for-priviledge", data)
				.then(response => {
					resolve(response.data)
				}).catch(err => {
					console.log("An error occured")
					console.log(err.response)
					debugger
				})
		})
	},
}