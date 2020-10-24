<template>
  <div id="updates-specific">
    <div>
      <div class="main-content">
        <div class="top">
          <div class="image" :style="{backgroundImage: 'url(\''+updatePhoto+'\')'}">
            <div class="like-container" @click.prevent="like()">
              <div
                class="like"
                :style="{'background-image': 'url(\'/png/like '+ ( update.liked == 1 ? '2' : '1' ) +'.png\')'}"
              >
                <span class="like-count">{{update.likes_count ? update.likes_count : ''}}</span>
              </div>
            </div>
            <div class="share-container" @click.prevent="share()">
              <div class="like" style="background-image: url('/png/share.png')">
                <span class="like-count">{{update.shares_count ? update.shares_count : ''}}</span>
              </div>
            </div>
          </div>
          <div class="more">
            <h4 class="title">{{update.title}}</h4>
            <div class="desctiption-for-mobile">
              <div v-show="isDisplayingContent">
                <h4>Description</h4>
                <p>{{update.content}}</p>
              </div>
            </div>
            <div class="poster-details">
              <span class="display_photo" :style="{backgroundImage: 'url(\''+profilePhoto+'\')'}"></span>
              <div class="name-container">
                <h4 class="name" @click="goToPage('/users/' + update.user.id)">{{update.user.name}}</h4>
                <a
                  href="#"
                  @click.prevent="followUser()"
                  class="follow-button"
                  v-if="update.is_following_user === 0"
                >Follow</a>
                <a
                  href="#"
                  @click.prevent="unfollowUser()"
                  class="follow-button unfollow"
                  v-if="update.is_following_user === 1"
                >Unfollow</a>
              </div>
            </div>

            <div class="date-and-time">
              <h5>Date and Time</h5>
              <span class="date">Sat, November 30</span>
              <span class="time">10:00am prompt</span>
            </div>

            <div class="location">
              <h4>Location</h4>
              <p v-if="location">{{location.unformatted_address}}</p>
              <!-- <a href="#" class="view-map-button">View Map</a> -->
              <div>
              <button @click="viewMap()" class="view-map-button">View Map</button>
              <button @click="viewComments()" class="view-comments-button">View Comments</button>
              </div>
            </div>
            <div>
              
            </div>
          </div>
        </div>
        <div class="bottom" v-show="isDisplayingContent">
          <h4>Description</h4>
          <div>
            <p>{{update.content}}</p>
          </div>
        </div>
        <div v-show="isDisplayingMap">
          <div class="map-view" v-if="location && location.lat && location.lng">
            <div class="map-view-header">
              <span>Maps</span>
              <span @click="closeMaps()" class="close-button">&times;</span>
            </div>
            <div class="map-view-text-address">{{location.formatted_address}}</div>
            <GmapMap :center="locationCenter" :zoom="9" :map-type-id="mapTypeId">
              <GmapMarker
                :key="index"
                v-for="(m, index) in locationMarkers"
                :position="m.position"
                :clickable="true"
                :draggable="true"
                @click="center=m.position"
              />
            </GmapMap>
          </div>
        </div>
        <!-- comments.show -->
        <div v-show="isDisplayingComments" class="comments" ref="comments">
          <div class="comments-header">
            <span>Comments</span>
            <span @click="closeComments()" class="close-button">&times;</span>
          </div>
          <div class="comments-body">
            <comment v-for="comment in comments" :key="comment.id" :comment="comment" :post_id="update.id" />
          </div>
          <div class="comments-input-container">
            <input
              type="text"
              ref="postComment"
              @keyup.enter="postComment()"
              class="comments-input"
              placeholder="Add a comment"
            />
            <button @click="postComment()" class="send-comment-button">Send</button>
          </div>
        </div>
      </div>
    </div>
    <div class="similar-updates-view" v-if="location && location.lat && location.lng">
      similar updates view
    </div>

    <share-post-modal
      :post="update"
      @closed="renderSharePostModal = false"
      v-if="renderSharePostModal"
    ></share-post-modal>
  </div>
</template>
<script>
import Comment from "~/components/Comment.vue";
import SharePostModal from "~/components/SharePostModal.vue";

export default {
  scrollToTop: true,
  layout: "authenticated",
  plugins: ["~/plugins/google_maps"],
  components: {
    Comment,
    SharePostModal
  },
  async fetch({ $axios, store, params }) {
    let index = store.state.updates.list.findIndex(el => el.id == params.id);

    if (index < 0) {
      await $axios.get("/api/posts/" + params.id).then(response => {
        store.commit("updates/add", response.data);
      });
    } else if (!store.state.updates.list[index].position) {
      await $axios.get("/api/posts/" + params.id).then(response => {
        store.commit("updates/addOrReplace", response.data);
      });
    }
  },
  data() {
    return {
      renderSharePostModal: false,
      isDisplayingMap: false,
      isDisplayingContent: true,
      isDisplayingComments: true
    };
  },
  mounted() {
    this.$store.dispatch("updates/listenToSinglePostEvents", {
      id: this.update.id
    });
  },
  computed: {
    update() {
      return this.$store.state.updates.list.find(
        el => el.id == this.$route.params.id
      );
    },
    comments() {
      return this.update.comments;
    },
    location() {
      return this.update.location;
    },
    locationCenter() {
      return { lat: this.location.lat, lng: this.location.lng };
    },
    locationMarkers() {
      return [{ position: this.locationCenter }];
    },
    mapTypeId() {
      return "terrain";
    },
    profilePhoto() {
      return (
        process.env.BACKEND_BASE_URL +
        (this.update.user.thumbnail
          ? this.update.user.thumbnail
          : "man-avatar-profile-icon.jpg")
      );
    },
    updatePhoto() {
      return (
        process.env.BACKEND_BASE_URL +
        (this.update.img ? this.update.img : "test.jpg")
      );
    }
  },
  methods: {
    goToPage(path) {
      if (this.$nuxt.$router.fullPath == path) return;
      this.$nuxt.$router.push(path);
    },
    postComment() {
      const val = this.$refs.postComment.value;
      this.$refs.postComment.value = "";

      if (!val) return;

      let data = {
        type: "post",
        id: this.update.id,
        content: val,
        post_id: this.update.id
      };

      let attempt = this.$store.dispatch("updates/comment", data);
    },
    like() {
      event.stopPropagation();
      let data = {
        type: "post",
        id: this.update.id,
        post_id: this.update.id
      };

      let attempt = this.$store.dispatch("updates/like", data);
    },
    initMap() {
      // The location of Uluru
      var uluru = { lat: -25.344, lng: 131.036 };
      // The map, centered at Uluru
      var map = new google.maps.Map(document.getElementById("map"), {
        zoom: 4,
        center: uluru
      });
      // The marker, positioned at Uluru
      var marker = new google.maps.Marker({ position: uluru, map: map });
    },
    viewMap() {
      this.isDisplayingContent = false;
      this.isDisplayingMap = true;
      // this.isDisplayingComments = false
    },
    closeMaps() {
      this.isDisplayingContent = true;
      this.isDisplayingMap = false;
      // this.isDisplayingComments = true;
    },
    viewComments() {
      this.isDisplayingContent = false;
      this.isDisplayingMap = false;
      this.isDisplayingComments = true;
    },
    closeComments() {
      this.isDisplayingContent = true;
      this.isDisplayingMap = false;
      this.isDisplayingComments = true;
    },

    share() {
      this.renderSharePostModal = true;
    },
    toggleCommentsVisibility() {
      let comments = this.$refs.comments;

      if (comments.classList.contains("show"))
        comments.classList.remove("show");
      else comments.classList.add("show");
    },
    followUser() {
      const data = {
        user_id: this.update.user.id,
        post_id: this.update.id
      };

      this.$store.dispatch("users/follow", data);
    },
    unfollowUser() {
      const data = {
        user_id: this.update.user.id,
        post_id: this.update.id
      };

      this.$store.dispatch("users/unfollow", data);
    }
  }
};
</script>

<style scoped>
#updates-specific {
  display: grid;
  grid-template-columns: 5fr 2fr;
  grid-gap: 2rem;
  padding-bottom: 1rem;
  /* min-height: 100%;
        overflow-y: auto; */
}

#updates-specific .main-content {
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 10px;
  overflow: hidden;
  overflow-y: auto;
  padding: 0;
  max-height: calc(var(--viewport-height, 100vh) - 50px - 3rem);
}

.dark-theme #updates-specific .main-content {
  background: #1b083a;
  color: #c4c4c4;
}

#updates-specific .main-content .top {
  display: grid;
  /* min-height: 300px; */
  background: #f9f9f9;
  grid-template-columns: 4fr 3fr;
}

.dark-theme #updates-specific .main-content .top {
  background: #140034;
}

#updates-specific .main-content .top .image {
  background-size: cover;
  background-repeat: no-repeat;
  height: 100%;
  width: 100%;
  position: relative;
  background-color: rgba(0, 0, 0, 0.1);
  background-blend-mode: darken;

  --action-container-sizes: 2rem;
  --action-content-scale: 0.6;
  --action-content-padding: 0.5rem;
}

#updates-specific .main-content .top .image:hover {
  background-color: rgba(0, 0, 0, 0.4);
}

#updates-specific .main-content .top .more {
  /* height: 100%; */
  padding: 2rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  display: flex;
  flex-direction: column;
}

#updates-specific .main-content .top .more .title {
  font-size: 1.4rem;
  margin: 0;
  margin-top: 0.5rem;
}

.dark-theme #updates-specific .main-content .top .more .title {
  color: white;
}

#updates-specific .main-content .top .more .desctiption-for-mobile {
  display: none;
}

#updates-specific .main-content .top .more .poster-details {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  display: flex;
  align-items: flex-start;
  padding: 0.5rem;
  padding-right: 0;
  padding-left: 0;
  margin-top: 0.5rem;
  margin-bottom: 1rem;
}
#updates-specific .main-content .top .more .poster-details .display_photo {
  background-size: cover;
  background-repeat: no-repeat;
  height: 2.5rem;
  width: 2.5rem;
  border-radius: 50%;
}

#updates-specific .main-content .top .more .poster-details .name-container {
  padding: 0.5em;
  padding-top: 0;
  margin-right: 0.5em;
}

#updates-specific .main-content .top .more .poster-details .name {
  font-size: 1rem;
  margin-top: 0.1rem;
  margin-bottom: 0.4rem;
  cursor: pointer;
}

.dark-theme #updates-specific .main-content .top .more .poster-details .name {
  color: #eeeeff;
}

#updates-specific .main-content .top .more .poster-details .follow-button {
  background: #dbeeff;
  font-size: 0.8rem;
  color: #0084ff;
  padding: 0.2rem;
  padding-left: 0.4rem;
  padding-right: 0.4rem;
  border-radius: 3px;
}

.dark-theme
  #updates-specific
  .main-content
  .top
  .more
  .poster-details
  .follow-button {
  background: #c4c4c4;
  color: #141b5b;
}

#updates-specific
  .main-content
  .top
  .more
  .poster-details
  .follow-button.unfollow {
  background: #c0c4c7;
  font-size: 0.8rem;
  color: #687179;
}

.dark-theme
  #updates-specific
  .main-content
  .top
  .more
  .poster-details
  .follow-button.unfollow {
  background: #757575;
  color: #9b9b9b;
}

#updates-specific .date-and-time {
  color: #0084ff;
  margin-top: 0.5rem;
  margin-bottom: 1rem;
}

#updates-specific .date-and-time h5 {
  font-size: 1rem;
  color: #3a2c51;
  margin: 0.1rem 0 0.5rem 0;
}
.dark-theme #updates-specific .date-and-time h5 {
  color: #c4c4c4;
}

#updates-specific .date-and-time .date,
#updates-specific .date-and-time .time {
  display: block;
  margin-top: 0.2rem;
  margin-bottom: 0.2rem;
}

#updates-specific .date-and-time .date {
  font-size: 0.8rem;
}

#updates-specific .date-and-time .time {
  font-size: 0.75rem;
}

#updates-specific .location {
  margin-top: 0.75rem;
  margin-bottom: 0.75rem;
  font-size: 0.8rem;
}

#updates-specific .location h4 {
  margin: 0.1rem 0 0.1rem 0;
  font-size: 1rem;
}

#updates-specific .location .view-map-button {
  color: #0084ff;
  border: solid 1.5px #0084ee;
  padding: 0.2rem 0.4rem 0.2rem 0.4rem;
  border-radius: 3px;
  margin-top: 0.5rem;
  font-size: 0.7rem;
}

.view-comments-button {
  border: solid 1.5px #9b9b9b;
  padding: 0.2rem 0.4rem 0.2rem 0.4rem;
  border-radius: 3px;
  margin-top: 0.5rem;
  font-size: 0.7rem;

  background: white;
  color: #9b9b9b;
}

#updates-specific .main-content .bottom {
  padding: 2rem;
  /* overflow: hidden;
  overflow-y: auto; */
}

#updates-specific .main-content .bottom h4 {
  font-size: 1.1rem;
  margin: 0;
  margin-bottom: 0.5rem;
}

.dark-theme #updates-specific .main-content .bottom h4 {
  color: #0084ff;
}

#updates-specific .main-content .bottom p {
  font-size: 0.85rem;
  font-weight: 300;
  font-style: normal;
}

#updates-specific .main-content .bottom .check-comments-button {
  display: none;
}

/* Comments Section */

#updates-specific .comments {
  border-radius: 10px;
  background: transparent;
  display: grid;
  grid-template-rows: 50px 1fr 40px;
  max-height: calc(calc(var(--viewport-height, 100vh) - 50px - 3rem) * 0.9);
  min-height: calc(calc(var(--viewport-height, 100vh) - 50px - 3rem) * 0.6);
}

#updates-specific .comments-header {
  /* background: #ecf6ff; */
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #0084ff;
  color: #3a2c51;
  padding: 0.5rem;
  padding-left: 1rem;
  padding-right: 1rem;
  border-top: solid 1px #c4c4c4;
  border-bottom: solid 1px #c4c4c4;
  /* border-top-left-radius: 10px;
  border-top-right-radius: 10px; */
}

.dark-theme #updates-specific .comments-header {
  background: #3f3d56;
}

#updates-specific .comments-header .close-button {
  width: 2rem;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: flex-end;

  cursor: pointer;
  font-size: 1.5rem;
  padding-right: 1rem;
  padding-left: 1rem;
  transform: translateX(1rem);
}

#updates-specific .comments-body {
  background: white;
  padding: 0.5rem;
  overflow: hidden;
  overflow-y: auto;
  position: relative;
}

.dark-theme #updates-specific .comments-body {
  background: #140034;
}

.comments-input-container {
  display: flex;
  align-items: center;
  flex-direction: row;
  border-top: #9b9b9b 0.5px solid;
  background: white;
  padding-right: 0.8rem;
  padding-left: 0.8rem;
}

.dark-theme .comments-input-container {
  background: #140034;
  color: white;
}

.comments-input {
  flex: 1;
  border: none;
  background: white;
  height: 100%;
  margin-right: 8px;

  color: rgba(0, 0, 0, 0.7);
}

.comments-input::placeholder {
  color: #c4c4c4;
}

.dark-theme .comments-input {
  background: #140034;
  color: white;
}

.comments-input:focus {
  outline: none;
}

.send-comment-button {
  border: none;
  border-radius: 0 16px 16px 16px;
  background: #0084ff;
  color: white;

  padding: 0.8rem;
  padding-top: 0.3rem;
  padding-bottom: 0.3rem;
}
.like-container {
  height: var(--action-container-sizes);
  width: var(--action-container-sizes);
  background: transparent;
  position: absolute;
  right: 2rem;
  bottom: -0.5rem;
  background-position: center;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
}

.share-container {
  height: var(--action-container-sizes);
  width: var(--action-container-sizes);
  background: transparent;
  position: absolute;
  right: 8rem;
  bottom: -0.5rem;
  background-position: center;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
}

.like {
  width: calc(var(--action-content-scale) * var(--action-container-sizes));
  height: calc(var(--action-content-scale) * var(--action-container-sizes));
  background-repeat: no-repeat;
  background-size: contain;
  position: relative;
  cursor: pointer;
}

.like::before {
  content: "";
  position: absolute;
  top: calc(var(--action-content-padding) * -1);
  right: calc(var(--action-content-padding) * -1);
  bottom: calc(var(--action-content-padding) * -1);
  left: calc(var(--action-content-padding) * -1);
  background: white;
  z-index: -1;
  border-radius: 50%;
}
.dark-theme .like::before {
  background: #3f3d56;
}

.like-count {
  content: attr(data-like-count);
  position: absolute;
  bottom: calc(-100% - 0.1rem);
  right: 0;
  left: 0;
  color: #9b9b9b;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
}

.map-view {
  overflow: hidden;
  border-radius: 10px;
  background: white;
  max-height: calc(var(--viewport-height, 100vh) - 50px - 3rem - 15rem);
}

.dark-theme .map-view {
  background: #140034;
}

.map-view-header {
  font-size: 1.1rem;
  color: #0084ff;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 35px;
  padding: 0.5rem;
  padding-left: 1rem;
  padding-right: 1rem;
}

.map-view-header .close-button{
  font-size: 1.7rem;
  padding-left: 1rem;
  padding-right: 1rem;
  transform: translateX(1rem);
  cursor: pointer;
}

.map-view-text-address {
  display: flex;
  padding: 0.5rem;
  font-size: 0.8rem;
  background: rgba(228, 234, 222, 0.5);
  font-style: italic;
  border-bottom: 0.8px solid white;
}

.vue-map-container {
  height: 500px;
  width: 100%;
  background-color: grey;
}

@media (max-width: 991.98px) {
  #updates-specific .main-content {
    /* max-height: unset; */
    overflow-y: scroll;
  }

  #updates-specific .main-content .top {
    grid-template-columns: 1fr;
  }

  #updates-specific .main-content .top .image {
    min-height: 50vh;
  }
  #updates-specific .main-content .bottom {
    overflow: unset;
  }
}

@media (max-width: 767.98px) {
  #updates-specific .main-content {
    max-height: unset;
    overflow-y: unset;
  }
  #updates-specific {
    grid-template-columns: 1fr;
  }

  /* #updates-specific .comments {
    width: 70vw;
    //height: calc(var(--viewport-height, 100vh) - 50px - 3rem);
    position: fixed;
    top: calc(1rem);
    bottom: 56px; // adjustment for chrome 
    right: 1.5rem;
    z-index: 888;
    box-shadow: -4px 4px 20px 6px rgba(130, 121, 121, 0.25);
    display: none;
  }

  #updates-specific .comments.show {
    display: grid;
  } */
  /* #updates-specific .comments::before{
            content: "";
            position:fixed;
            background: rgba(0,0,0,0.1);
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: -2;
        } */

  #updates-specific .main-content .bottom .check-comments-button {
    display: unset;
    /* border: 1.25234px solid #B2B2B2;
            box-sizing: border-box;
            border-radius: 2.50467px;
            background: transparent; */
  }

  #updates-specific .main-content .bottom {
    display: none;
  }

  #updates-specific .main-content .top .more {
    padding-left: 1rem;
    padding-right: 1rem;
  }

  #updates-specific .main-content .top .more .desctiption-for-mobile {
    display: block;
  }

  #updates-specific .main-content .top .more .desctiption-for-mobile p {
    font-size: 0.9rem;
    line-height: 1.4rem;
  }

  .view-comments-button {
    display: none;
  }

  #updates-specific .comments-header .close-button {
    display: none;
  }
}

@media (max-width: 575.98px) {
  #updates-specific .main-content .top .image {
    min-height: 30vh;
  }

  #updates-specific .main-content .top .image {
    --action-container-sizes: 2.5rem;
    --action-content-scale: 0.5;
    --action-content-padding: 0.7rem;
  }
}
</style>
