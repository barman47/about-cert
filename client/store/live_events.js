import Vue from "vue"

export const state = () => ({
	list: [],
	paginationData: {},
	peerConnectionConfiguration: {
		iceServers: [
			// { urls: "stun:stun.l.google.com:19302" },
			{
				urls: "stun:numb.viagenie.ca",
				username: "dev.davexoyinbo@gmail.com",
				credential: "Password1"
			},
			{
				urls: "turn:numb.viagenie.ca",
				username: "dev.davexoyinbo@gmail.com",
				credential: "Password1"
			},
			// { urls: "stun:stun4.l.google.com:19302" },
			// { urls: "stun:stun01.sipphone.com" },
			// { urls: "stun:stun.ekiga.net" },
			// { urls: "stun:stun.fwdnet.net" },
			// { urls: "stun:stun.iptel.org" },
			// { urls: "stun:stun.rixtelecom.se" },
			// { urls: "stun:stun.schlund.de"},
			// { urls: "stun:stunserver.org" },
			// { urls: "stun:stun.softjoys.com" },
		],
		mandatory: {
			OfferToReceiveAudio: true,
			OfferToReceiveVideo: true,
		}
	},
	localStream: undefined,
	remoteStream: undefined,
	broadCastedVideo: undefined,
	channel: undefined
})


export const mutations = {
	fetchSingleLiveEvent(state, { liveEvent }) {
		let index = state.list.findIndex(el => el.id == liveEvent.id)

		if (index < 0) {
			state.list.unshift(liveEvent)
			return
		}

		Vue.set(state.list, index, liveEvent)
	},
	fetchAllLiveEvents(state, { paginationData }) {
		state.paginationData = paginationData

		for (let liveEvent of paginationData.data) {
			let index = state.list.findIndex(el => el.id == liveEvent.id)

			if (index < 0) {
				state.list.push(liveEvent)
				continue
			}

			Vue.set(state.list, index, liveEvent)
		}
	},
	updateEchoChannel(state, { channel }) {
		state.channel = channel
	},
	updateLocalStream(state, { stream }) {
		state.localStream = stream
	},
	shareScreen(state, {stream, on}){
		let liveEvent = state.list.find(el => el.id == on)

		state.broadCastedVideo.srcObject = stream

		console.log(state.broadCastedVideo)

		stream.getTracks().forEach(track => {
			(liveEvent.peerConnections || []).forEach(peerConnectionInstance => {
				peerConnectionInstance.peerConnection.addTrack(track, stream)
			})
		})
	},
	updateRemoteStream(state, { stream }) {
		state.remoteStream = stream
	},
	broadCastedVideo(state, { video }) {
		state.broadCastedVideo = video
	},
	startSession(state, { id }) {
		let index = state.list.findIndex(el => el.id == id)
		if (index < 0)
			return

		let liveEvent = state.list[index]
		liveEvent.is_in_session = 1

		Vue.set(state.list, index, liveEvent)
	},

	endSession(state, { id }) {
		let index = state.list.findIndex(el => el.id == id)
		if (index < 0)
			return

		let liveEvent = state.list[index]
		liveEvent.is_in_session = 0

		Vue.set(state.list, index, liveEvent)
	},
	presenceChannelHere(state, { id, users }) {
		let index = state.list.findIndex(el => el.id == id)
		if (index < 0)
			return

		let liveEvent = state.list[index]
		liveEvent.occupants = users

		Vue.set(state.list, index, liveEvent)
	},
	presenceChannelJoining(state, { id, user }) {
		let index = state.list.findIndex(el => el.id == id)
		if (index < 0)
			return

		let liveEvent = state.list[index]

		if (!liveEvent.occupants.some(el => el.id == user.id))
			liveEvent.occupants.unshift(user)

		Vue.set(state.list, index, liveEvent)
	},

	presenceChannelLeaving(state, { id, user }) {
		let index = state.list.findIndex(el => el.id == id)
		if (index < 0)
			return

		let liveEvent = state.list[index]

		let occupantIndex = liveEvent.occupants.findIndex(el => el.id == user.id)
		if (occupantIndex >= 0) {
			liveEvent.occupants.splice(index, 1)
		}

		if (!liveEvent.peerConnections)
			liveEvent.peerConnections = []

		if (user.id == liveEvent.user_id)
			state.remoteStream = new MediaStream()

		let peerConnectionIndex = liveEvent.peerConnections.findIndex(el => el.user_id == user.id)

		if (peerConnectionIndex < 0)
			return

		let peerConnectionInstance = liveEvent.peerConnections.splice(peerConnectionIndex, 1)[0]

		try {
			peerConnectionInstance.peerConnection.close()
			peerConnectionInstance.dataChannel.close()
		} catch (e) { }
		try {
			peerConnectionInstance.peerConnection = null
			peerConnectionInstance.dataChannel = null
		} catch (e) { }

		Vue.set(state.list, index, liveEvent)
	},

	fetchMessages(state, { paginationData, id }) {
		let index = state.list.findIndex(el => el.id == id)
		if (index < 0)
			return

		let liveEvent = state.list[index]

		liveEvent.messagesPagination = paginationData

		Vue.set(state.list, index, liveEvent)
	},
	sendMessage(state, { message, on }) {
		let index = state.list.findIndex(el => el.id == on)
		if (index < 0)
			return

		let liveEvent = state.list[index]

		if (!liveEvent.messagesPagination.data.some(el => el.id == message.id))
			liveEvent.messagesPagination.data.unshift(message)

		Vue.set(state.list, index, liveEvent)
	},

	addToPeerConnections(state, { user_id, peerConnection, on, is_creator }) {
		let index = state.list.findIndex(el => el.id == on)
		if (index < 0)
			return

		let liveEvent = state.list[index]

		if (!liveEvent.peerConnections)
			liveEvent.peerConnections = []

		liveEvent.peerConnections.push({ user_id, peerConnection, is_creator })

		Vue.set(state.list, index, liveEvent)
	},
	addDataChannelToPeerConnections(state, { user_id, on, dataChannel }) {

		let index = state.list.findIndex(el => el.id == on)
		if (index < 0)
			return

		let liveEvent = state.list[index]

		let peerConnectionIndex = liveEvent.peerConnections.findIndex(el => el.user_id == user_id)

		if (peerConnectionIndex < 0)
			return

		liveEvent.peerConnections[peerConnectionIndex].dataChannel = dataChannel

		console.log("Added data channel")

		dataChannel.onopen = event => {
			console.log("Data channel is open")
			dataChannel.send("Hi back")
		}

		dataChannel.onmessage = event => {
			console.log("receives message")
			console.log(event.data)
		}

		dataChannel.onclose = event => {
			console.log("Closing the data channel")
		}

		dataChannel.error = error => {
			console.log("An error occured :Data channel")
			console.log(error)
		}

		Vue.set(state.list, index, liveEvent)
	},

	addICECandidate(state, { on, from, icecandidate }) {
		let index = state.list.findIndex(el => el.id == on)
		if (index < 0)
			return

		let liveEvent = state.list[index]

		// debugger

		let peerConnectionIndex = (liveEvent.peerConnections || []).findIndex(el => el.user_id == from)

		if (peerConnectionIndex < 0)
			return

		let peerConnection = liveEvent.peerConnections[peerConnectionIndex].peerConnection

		peerConnection.addIceCandidate(JSON.parse(icecandidate)).catch(e => {
			console.log("Failure during addIceCandidate(): " + e.name);
		})
	},
}

export const actions = {
	createLiveEvent({ }, { data }) {
		return new Promise((resolve, reject) => {
			this.$axios.post("/api/live-events/create", data)
				.then(response => resolve(response.data.id))
				.catch(err => reject(err))
		})
	},
	fetchSingleLiveEvent({ commit }, { id }) {
		return new Promise((resolve, reject) => {
			this.$axios.get("/api/live-events/" + id)
				.then(response => response.data)
				.then(response => {
					commit("fetchSingleLiveEvent", { liveEvent: response.data })
					resolve()
				}).catch(err => reject(err))
		})
	},
	fetchAllLiveEvents({ commit }) {
		return new Promise((resolve, reject) => {
			this.$axios.get("/api/live-events")
				.then(response => response.data)
				.then(response => {
					commit("fetchAllLiveEvents", { paginationData: response.data })
					resolve()
				}).catch(err => console.log(err))
		})
	},
	startSession({ commit }, { id }) {
		return new Promise((resolve, reject) => {
			this.$axios.post("/api/live-events/start-session", { live_event_id: id })
				.then(() => {
					commit("startSession", { id })
					resolve()
				}).catch(err => reject(err))
		})
	},
	endSession({ commit }, { id }) {
		return new Promise((resolve, reject) => {
			this.$axios.post("/api/live-events/end-session", { live_event_id: id })
				.then(() => {
					commit("endSession", { id })
					resolve()
				}).catch(err => reject(err))
		})
	},
	joinSession({ commit, dispatch, rootState }, { id, creator_id }) {
		let channel = window.Echo.join(`live.event.${id}`)
			.here(users => {
				commit("presenceChannelHere", { id, users: users })
				dispatch("fetchMessages", { id })

				for (let user of users) {
					console.log(user.name + ": " + !(user.id == creator_id || user.id == rootState.auth.user.id))
					if (user.id == creator_id || user.id == rootState.auth.user.id) { continue; }
					else {
						console.log("Making call in here")
						dispatch("makeCall", { to: user.id, on: id })
					}
				}
			})
			.joining(user => {
				commit("presenceChannelJoining", { id, user })
				console.log(user.name + " Joining")
				if (user.id != creator_id && rootState.auth.user.id == creator_id) {
					console.log("Making call in Joining")
					dispatch("makeCall", { to: user.id, on: id })
				}
			})
			.leaving(user => {
				commit("presenceChannelLeaving", { id, user })
				console.log(user.name + " Leaving")
			})
			.listen(".receive.offer", e => {
				if (e.to == rootState.auth.user.id) {
					dispatch("receiveOffer", e)
				}
			})
			.listen(".receive.answer", e => {
				if (e.to == rootState.auth.user.id) {
					dispatch("receiveAnswer", e)
				}
			})
			.listen(".receive.icecandidate", e => {
				if (e.to == rootState.auth.user.id) {
					commit("addICECandidate", e)
				}
			}).listenForWhisper("send-message", ({ message, on }) => {
				console.log("Receive message")
				commit("sendMessage", { message, on })
			})

		commit("updateEchoChannel", { channel })
	},
	leaveSession({ commit }, { id }) {
		window.Echo.leave(`live.event.${id}`);
	},
	fetchMessages({ commit, rootState }, { id }) {
		return new Promise((resolve, reject) => {
			this.$axios.get("/api/live-events/get-messages/" + id)
				.then(response => response.data)
				.then(response => {
					commit("fetchMessages", { paginationData: response.data, id })
					resolve()
				}).catch(err => reject(err))
		})
	},
	sendMessage({ commit, state, rootState }, { id, content }) {
		return new Promise((resolve, reject) => {
			this.$axios.post("/api/live-events/send-message", { content, live_event_id: id })
				.then((response) => {
					let index = state.list.findIndex(el => el.id == id)
					if (index < 0)
						return

					let liveEvent = state.list[index]

					if (!liveEvent.messagesPagination)
						dispatch("fetchMessages", { id })
					else {
						let message = {
							id: response.data.id,
							content: content,
							live_event_id: id,
							sender: {
								id: rootState.auth.user.id,
								name: rootState.auth.user.name,
								thumbnail: rootState.auth.user.thumbnail,
							}
						}
						commit("sendMessage", { message, on: id })
						state.channel.whisper("send-message", { message, on: id })
					}

					resolve()
				})
				.catch(err => reject(err))
		})
	},

	//========================================================================
	//=========================== FOR WEBRTC
	//========================================================================
	makeCall({ state, dispatch }, { to, on }) {
		return new Promise(async (resolve, reject) => {
			let peerConnection = new RTCPeerConnection(state.peerConnectionConfiguration)
			state.localStream.getTracks().forEach(track => {
				peerConnection.addTrack(track, state.localStream)
			})

			dispatch("addICECandidateListeners", { to, on, peerConnection })
			dispatch("addConnectionStateChangeEvent", { to, on, peerConnection })

			let offer = await peerConnection.createOffer()
			await peerConnection.setLocalDescription(offer)
			await dispatch("sendOffer", {
				to,
				on,
				offer: JSON.stringify(offer),
				peerConnection
			})
			resolve()
		})
	},
	shareScreen({ state, commit }, { on }) {
		return new Promise((resolve, reject) => {
			let constraints = {
				video: {
					cursor: 'always',  //| 'motion' | 'never',
					displaySurface: 'application' //| 'browser' | 'monitor' | 'window'
				}
			}
			navigator.mediaDevices
				.getDisplayMedia(constraints)
				.then(stream => {
					console.log("Got MediaStream:", stream);
					// self.$refs.broadCastedVideo.srcObject = stream;
					commit("updateLocalStream", { stream });
					commit("shareScreen", {stream, on})
				})
				.catch(error => {
					console.error("Error accessing media devices.", error);
				});
		})
	},
	sendOffer({ commit, dispatch, state }, { to, on, offer, peerConnection }) {
		return new Promise(async (resolve, reject) => {
			// let dataChannel = peerConnection.createDataChannel("datachannel")
			let liveEvent = state.list.find(el => el.id == on)
			await dispatch("addOtherPeerConnectionListener", { peerConnection, is_creator: to == liveEvent.user_id, is_receiver: false })

			this.$axios.post("/api/live-events/send-offer", { to, on, offer })
				.then(() => {
					commit("addToPeerConnections", { user_id: to, peerConnection, on })
					// commit("addDataChannelToPeerConnections", { user_id: to, on, dataChannel })
					resolve()
				})
				.catch(err => reject(err))
		})
	},
	receiveOffer({ commit, state, dispatch }, { from, on, offer, is_creator }) {
		console.log({ from, on, offer, is_creator })
		return new Promise(async (resolve, reject) => {
			let peerConnection = new RTCPeerConnection(state.peerConnectionConfiguration)

			state.localStream.getTracks().forEach(track => {
				peerConnection.addTrack(track, state.localStream)
			})

			await commit("addToPeerConnections", { user_id: from, peerConnection, on, is_creator })
			dispatch("addOtherPeerConnectionListener", { peerConnection, is_creator, is_receiver: true })
			dispatch("addICECandidateListeners", { to: from, on, peerConnection })
			dispatch("addConnectionStateChangeEvent", { to: from, on, peerConnection })

			peerConnection.setRemoteDescription(new RTCSessionDescription(JSON.parse(offer)))
			const answer = await peerConnection.createAnswer()
			await peerConnection.setLocalDescription(answer)

			dispatch("sendAnswer", {
				to: from,
				on: on,
				answer: JSON.stringify(answer),
				peerConnection,
				is_creator
			})

			resolve()
		})
	},
	sendAnswer({ commit, dispatch }, { to, on, answer, peerConnection, is_creator }) {
		return new Promise((resolve, reject) => {
			this.$axios.post("/api/live-events/send-answer", { to, on, answer })
				.then(() => {

					resolve()
				})
				.catch(err => reject(err))
		})
	},
	receiveAnswer({ state }, { from, on, answer, is_creator }) {
		console.log({ from, on, answer, is_creator })

		return new Promise(async (resolve, reject) => {
			let index = state.list.findIndex(el => el.id == on)
			if (index < 0)
				return

			let liveEvent = state.list[index]

			let peerConnectionIndex = liveEvent.peerConnections.findIndex(el => el.user_id == from)

			if (peerConnectionIndex < 0)
				return

			let peerConnection = liveEvent.peerConnections[peerConnectionIndex].peerConnection

			await peerConnection.setRemoteDescription(new RTCSessionDescription(JSON.parse(answer)))

			console.log("added the answer to remote description")
			resolve()
		})
	},



	addOtherPeerConnectionListener({ state, commit }, { peerConnection, is_creator, is_receiver }) {
		if (is_creator) {
			peerConnection.addEventListener('track', async (event) => {
				console.log("Adding track to remote stream object")
				await state.remoteStream.addTrack(event.track, state.remoteStream)
				state.broadCastedVideo.srcObject = state.remoteStream
			})
		}

		if (is_receiver) {
			// console.log("is peer receiver")
			// peerConnection.ondatachannel = event => {
			// 	// Make sure you listen for the data channel from the incomming
			// 	// offer then create a mutation function for updating the datachannels
			// 	// in the live event datachannel list
			// 	// Test: test the sendMessage action function against the dataa
			// 	console.log("Receiver peer received the data channel")
			// 	let dataChannel = event.channel
			// 	commit("addDataChannelToPeerConnections", { user_id, on, dataChannel })
			// }
		}
	},
	addICECandidateListeners({ dispatch }, { to, on, peerConnection }) {
		console.log("Adding icecandidate event listener")
		peerConnection.addEventListener("icecandidate", event => {
			if (event.candidate) {
				console.log("=>ICE candidate")
				console.log(event.candidate)
				dispatch("sendICECandidate", { to, on, icecandidate: JSON.stringify(event.candidate) })
			}
		})
	},
	addConnectionStateChangeEvent({ commit, rootState, state }, { to, on, peerConnection }) {

		peerConnection.addEventListener("connectionstatechange", event => {
			if (peerConnection.connectionState == "connected") {
				console.log("connected")
			}
		})
	},
	sendICECandidate({ commit }, { to, on, icecandidate }) {
		return new Promise((resolve, reject) => {
			this.$axios.post("/api/live-events/send-icecandidate", { to, on, icecandidate })
				.then(() => resolve())
				.catch(err => reject(err))
		})
	},
}