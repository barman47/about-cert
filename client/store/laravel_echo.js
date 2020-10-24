import Echo from "laravel-echo"

let io = require('socket.io-client')

export const state = () => ({
})


export const mutations = {
}

export const actions = {
	initializeLaravelEcho({ rootState, commit, dispatch }) {
		return new Promise((resolve, reject) => {
			try {
				window.io = io

				window.Echo = new Echo({
					broadcaster: 'socket.io',
					host: rootState.env.LARAVEL_ECHO_URL,
					client: io,
					auth: {
						headers: {
							Authorization: localStorage.getItem("auth._token.password_grant")
						}
					}
				})

				if (rootState.auth.user) {
					window.Echo.private("user." + rootState.auth.user.id)
						//Live event
						.listen(".live.event.created", e => {
							dispatch("live_events/fetchSingleLiveEvent", { id: e.id }, { root: true })
						})

					window.Echo.private("authenticated")
						//live event
						.listen(".live.event.created", e => {
							console.log(e)
							dispatch("live_events/fetchSingleLiveEvent", { id: e.id }, { root: true })
						})

					// Notification Alerts
					window.Echo.private('alert.' + rootState.auth.user.id)
						.listen('.user.alert', (e) => {
							commit("alerts/receiveAlert", e.data, { root: true })
						});

					// Created Posts
					window.Echo.private('posts')
						.listen('.post.created', (e) => {
							dispatch("updates/fetchSingle", e.id, { root: true })
						});

					// Updates
					dispatch("updates/listenToPostEvents", {}, { root: true })
				}
			} catch (e) {
				console.log(e)
			}

			resolve()
		})
	},
}