<template>
  <div id="live-event-page">
    <div id="live-event-container">
      <div class="video-section">
        <div class="header">
          <div class="live-now-container">
            <span class="live-now">Live Now</span>
            <div class="live-now-image">
              <div></div>
            </div>
          </div>
          <div class="session-button-and-profile-container">
            <!-- <button @click="shareScreen()">shareScreen</button> -->
            <div v-if="isEventCreator">
              <button
                v-if="liveEvent.is_in_session == 0"
                class="session-button start"
                @click="startSession()"
              >Start Session</button>
              <button v-else class="session-button end" @click="endSession()">End Session</button>
            </div>
            <div v-else>
              <button
                v-if="!joined"
                class="session-button start"
                @click="joinSession()"
              >Join Session</button>
              <button v-else class="session-button end" @click="leaveSession()">Leave Session</button>
            </div>
            <div class="profile-photo" :style="{backgroundImage: 'url(\''+ profilePhoto +'\')'}"></div>
          </div>
        </div>
        <div
          class="body"
          :class="{active: joined}"
          :style="{backgroundImage: 'url(\''+ coverImage +'\')'}"
        >
          <video ref="broadCastedVideo" id="broadcast-video" muted="muted" autoplay></video>
          <!-- <video ref="broadCastedVideo" id="broadcast-video" autoplay></video> -->
          <div class="user-join">
            <div class="user-join-photo-container">
              <div class="user-join-photos">
                <div
                  class="photo"
                  :style="{backgroundImage: 'url(\''+ formatBackendPhoto(u.thumbnail) +'\')'}"
                  v-for="u in justJoined"
                  :key="u.id"
                ></div>
              </div>
            </div>
            <span v-if="occupantsCount > 0">{{occupantsCount}} joined</span>
          </div>
        </div>
      </div>
      <div class="chat-section">
        <div class="chat-header">
          <span>Chat</span>
          <!-- <span>&times;</span> -->
        </div>
        <div class="chat-body">
          <div class="live-chat-message-container">
            <chat-message
              :left="message.sender.id != $auth.user.id"
              :text="message.content"
              v-for="message in messages"
              :thumbnail="message.sender.thumbnail"
              :key="message.id"
            ></chat-message>
          </div>
        </div>
        <form class="chat-input" @submit.prevent="sendMessage()">
          <input
            ref="inputText"
            type="text"
            class="chat-input-field"
            placeholder="Type a message..."
          />
          <div @click="sendMessage()" class="chat-send-image"></div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import ChatMessage from "~/components/ChatMessage";
import Peer from "simple-peer";

var wrtc = require("wrtc");

export default {
  scrollToTop: true,
  validate({ params }) {
    return !!params.id;
  },
  head() {
    return {
      script: []
    };
  },
  async fetch({ store, params, error }) {
    await store
      .dispatch("live_events/fetchSingleLiveEvent", { id: params.id })
      .catch(err => console.log(err));
  },
  data() {
    return {
      array: [],
      peerRefs: [],
      localStream: undefined,
      joined: false
    };
  },
  components: {
    ChatMessage
  },
  layout: "authenticated",
  computed: {
    profilePhoto() {
      return (
        process.env.BACKEND_BASE_URL.replace(/\/+$/, "") +
        "/" +
        (this.$auth.user.thumbnail || "man-avatar-profile-icon.jpg").replace(
          /^\/+/,
          ""
        )
      );
    },
    coverImage() {
      if (!this.liveEvent.cover_image) return "/test video.png";
      return (
        process.env.BACKEND_BASE_URL.replace(/\/+$/, "") +
        "/" +
        (this.liveEvent.cover_image || "").replace(/^\/+/, "")
      );
    },
    roomName() {
      return "";
    },
    messages() {
      return (this.liveEvent.messagesPagination || {}).data || [];
    },
    occupants() {
      return this.liveEvent.occupants || [];
    },
    occupantsCount() {
      return this.occupants.length;
    },
    justJoined() {
      return this.occupants.slice(0, 3);
    },
    isEventCreator() {
      return this.liveEvent.user_id == this.$auth.user.id;
    },
    liveEvent() {
      return this.$store.state.live_events.list.find(
        el => el.id == this.$nuxt.$route.params.id
      );
    },
    remoteStream() {
      return this.$store.state.remoteStream;
    }
  },
  methods: {
    formatBackendPhoto(url) {
      return (
        process.env.BACKEND_BASE_URL.replace(/\/+$/, "") +
        "/" +
        (url || "man-avatar-profile-icon.jpg").replace(/^\/+/, "")
      );
    },
    joinSession() {
      // if (!this.isEventCreator) {
      //   const constraints = {
      //     video: true,
      //     audio: true
      //   };
      //   let self = this;
      //   navigator.mediaDevices
      //     .getUserMedia(constraints)
      //     .then(stream => {
      //       console.log("Got MediaStream:", stream);
      //       self.localStream = stream;

      //       self.$store.commit("live_events/updateLocalStream", { stream });
      //     })
      //     .catch(error => {
      //       console.error("Error accessing media devices.", error);
      //     });
      // }
      this.$store.dispatch("live_events/joinSession", {
        id: this.liveEvent.id,
        creator_id: this.liveEvent.user_id
      });
      this.joined = true;
    },
    leaveSession() {
      this.joined = false;
      this.$store.dispatch("live_events/leaveSession", {
        id: this.liveEvent.id,
        creator_id: this.liveEvent.user_id
      });
    },
    startSession() {
      if (!this.isEventCreator) return;

      this.setLocalStreamAndStartSession();
    },
    endSession() {
      if (!this.isEventCreator) return;

      let attempt = this.$store.dispatch("live_events/endSession", {
        id: this.liveEvent.id
      });

      let self = this;
      attempt.then(() => {
        self.stopStream();
        self.$store.dispatch("live_events/leaveSession", {
          id: this.liveEvent.id,
          creator_id: this.liveEvent.user_id
        });
      });
    },
    setLocalStreamAndStartSession() {
      const constraints = {
        video: true,
        audio: true
      };

      let self = this;
      navigator.mediaDevices
        .getUserMedia(constraints)
        .then(stream => {
          console.log("Got MediaStream:", stream);
          self.$refs.broadCastedVideo.srcObject = stream;

          self.$store.commit("live_events/updateLocalStream", { stream });
          let attempt = self.$store.dispatch("live_events/startSession", {
            id: self.liveEvent.id
          });

          attempt.then(() => {
            self.joinSession();
          });
        })
        .catch(error => {
          console.error("Error accessing media devices.", error);
        });
    },
    shareScreen(){
      this.$store.dispatch("live_events/shareScreen", {on: this.liveEvent.id})
    },
    stopStream() {
      this.localStream.getTracks().forEach(track => {
        track.stop();
      });
    },
    sendMessage() {
      let text = this.$refs.inputText.value;

      if (!text) return;
      this.$refs.inputText.value = "";

      let attempt = this.$store.dispatch("live_events/sendMessage", {
        id: this.liveEvent.id,
        content: text
      });
    }
  },

  mounted() {
    if (!window.Echo) {
      this.$store.dispatch("laravel_echo/initializeLaravelEcho");
    }

    this.$store.commit("live_events/broadCastedVideo", {
      video: this.$refs.broadCastedVideo
    });

    this.$store.commit("live_events/updateRemoteStream", {
      stream: new MediaStream()
    });

    this.$store.commit("live_events/updateLocalStream", {
      stream: new MediaStream()
    });

    if (this.isEventCreator && this.liveEvent.is_in_session == 1) {
      this.startSession();
    }
  },
  beforeRouteEnter(to, from, next) {
    next();
  },

  beforeRouteLeave(to, from, next) {
    this.$store.dispatch("live_events/leaveSession", { id: this.liveEvent.id });
    next();
  },

  watch: {
    remoteStream: function(newVal, oldVal) {
      console.log("value changed");
    }
  }
};
</script>

<style scoped>

#live-event-page{
  padding-bottom: 2rem;
}

#live-event-container {
  display: grid;
  grid-template-columns: 7fr 3fr;
  gap: 3rem;
}
#live-event-container > div {
  border-radius: 10px;
}

.video-section {
  background: white;
  height: calc(var(--viewport-height, 100vh) - 100px);
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.header {
  background: white;
  height: 50px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
}

.body {
  background: url("/test video.png");
  flex: 1;
  width: 100%;
  background-position: center center;
  background-size: cover;
  background-repeat: no-repeat;
  background-color: rgba(0, 0, 0, 0.7);
  position: relative;
  overflow: hidden;
}

.body.active {
  background-blend-mode: darken;
}

.body .user-join {
  position: absolute;
  right: 0.5rem;
  bottom: 0.5rem;
  z-index: 2;
  color: white;

  display: inline-flex;
  align-items: center;
  flex-direction: row;
}

.body .user-join .user-join-photo-container {
  --photo-size: 3rem;
  width: calc(var(--photo-size) + 1rem);
  height: calc(var(--photo-size) + 1rem);
  display: flex;
  align-items: center;
  justify-content: center;
}

.body .user-join .user-join-photos {
  position: relative;
  height: 100%;
  width: 100%;
}

.body .user-join .user-join-photos .photo {
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;

  height: var(--photo-size);
  width: var(--photo-size);
  border-radius: 50%;
  border: 1.5px solid white;

  top: calc(50% - calc(var(--photo-size) / 2));

  position: absolute;
}

.body .user-join .user-join-photos .photo:nth-child(1) {
  right: 16px;
  z-index: 12;
}

.body .user-join .user-join-photos .photo:nth-child(2) {
  right: 9px;
  z-index: 11;
}

.body .user-join .user-join-photos .photo:nth-child(3) {
  right: 2px;
  z-index: 10;
}

#broadcast-video {
  height: 100%;
  width: 100%;

  /* background: green; */
}

.profile-photo {
  height: 35px;
  width: 35px;
  border-radius: 50%;
  background-position: center center;
  background-repeat: no-repeat;
  background-size: cover;
  position: relative;
}

.profile-photo::before {
  content: "";
  position: absolute;
  top: -3px;
  bottom: -3px;
  right: -3px;
  left: -3px;
  border-radius: 50%;
  border: 1.2px solid #0084ff;
}

.live-now-container {
  display: flex;
  align-items: center;
}

.live-now {
  font-size: 0.8rem;
  color: #0084ff;
  margin-right: 1rem;
}

.live-now-image {
  height: 40px;
  width: 40px;
  border-radius: 50%;
  position: relative;
  background: #bedefd;
  display: flex;
  justify-content: center;
  align-items: center;
}

.live-now-image::before {
  content: "";
  background: #82c3ff;
  top: 3px;
  right: 3px;
  left: 3px;
  bottom: 3px;
  position: absolute;
  z-index: 0;
  border-radius: 50%;
}

.live-now-image::after {
  content: "";
  background: #0084ff;
  top: 6px;
  right: 6px;
  left: 6px;
  bottom: 6px;
  position: absolute;
  z-index: 0;
  border-radius: 50%;
}

.live-now-image > div {
  height: 10px;
  width: 10px;
  background-image: url("/png/Stream.png");
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  z-index: 2;
}

.session-button-and-profile-container {
  display: inline-flex;
  align-items: center;
  height: 100%;
}

.session-button {
  margin-right: 2rem;
  height: 30px;
  background: rgba(206, 231, 255, 0.25);
  color: #0084ff;
  border: #0084ff 1px solid;
  border-radius: 5px;
}

.session-button.end {
  background: #ff2103;
  color: white;
  border: none;
}

/* Chat section */

.chat-body {
  height: 100%;
  overflow: hidden;
  overflow-y: auto;
}

.chat-section {
  max-height: calc(var(--viewport-height, 100vh) - 100px);
  background: white;
  display: grid;
  grid-template-rows: 60px 1fr 50px;
  overflow: hidden;
}

.chat-header {
  background: #ecf6ff;
  color: #0084ff;
  font-style: normal;
  font-weight: normal;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-left: 1rem;
  padding-right: 1rem;
}

.chat-input {
  display: flex;
  align-items: center;
  padding: 1rem;
}

.chat-input-field {
  flex: 1;
  margin-right: 0.8rem;
  height: 30px;
  border-radius: 15px;
  border: none;
  background: rgba(206, 231, 255, 0.25);

  padding-left: 10px;
  padding-right: 10px;
  text-indent: 10px;
  font-size: 0.8rem;
  color: #9b9b9b;
}

.chat-input-field:focus {
  outline: none;
}

.chat-send-image {
  height: 30px;
  width: 30px;
  border-radius: 50%;
  background-image: url("/png/send.png");
  background-size: contain;
  background-position: center center;
  background-repeat: no-repeat;
}

/* Body */
.live-chat-message-container {
  padding: 1rem;
}

@media (max-width: 1199.98px) {
  #live-event-container {
    gap: 2rem;
  }
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
  #live-event-container {
    display: flex;
    flex-direction: column;
  }

  .video-section {
    height: unset;
    max-height: calc(var(--viewport-height, 100vh) - 8rem);
  }

  .chat-section {
    min-height: calc(var(--viewport-height, 100vh) / 2);
    margin-top: 1rem;
  }
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
}
</style>