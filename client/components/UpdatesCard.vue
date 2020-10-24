<template>
  <div class="updates-card">
    <div class="layer1">
      <span data-profile-photo :style="{'background-image': 'url(\''+profilePhoto+'\')'}"></span>
      <div>
        <h4 @click="goToPage('/users/' + update.user.id)">{{update.user.name}}</h4>
        <small>{{update.formatted_created_at}}</small>
      </div>
    </div>
    <div
      class="layer2"
      @click="goToUpdate(update.id)"
      :style="{'background-image' :  'url(\''+updatePhoto+'\')'}"
    >
      <div class="actions-container">
        <div class="like-container" @click.prevent="">
          <div
            class="like"
            :style="{'background-image': 'url(\'/png/comments-icon.png\')'}"
          >
            <span class="like-count">{{update.comments_count ? update.comments_count : ''}}</span>
          </div>
        </div>
        <div class="share-container" @click.prevent="share()">
          <div class="like" style="background-image: url('/png/share.png')">
            <span class="like-count">{{update.shares_count ? update.shares_count : ''}}</span>
          </div>
        </div>
        <div class="like-container" @click.prevent="like()">
          <div
            class="like"
            :style="{'background-image': 'url(\'/png/like '+ ( update.liked == 1 ? '2' : '1' ) +'.png\')'}"
          >
            <span class="like-count">{{update.likes_count ? update.likes_count : ''}}</span>
          </div>
        </div>
        
      </div>
    </div>
    <div class="layer3">
      <div class="date">{{update.formatted_time}}</div>
      <div @click="goToUpdate(update.id)" class="title">{{update.title}}</div>
      <!-- <div class="comment c1" v-if="(update.comments || []).length > 0">
        <span
          :style="{'background-image' : 'url(\''+formatProfileImage(update.comments[0].user.thumbnail)+'\')'}"
        ></span>
        <div class="content">
          <span>
            <span
              class="name"
              @click="goToPage('/users/' + update.comments[0].user.id)"
            >{{update.comments[0].user.name}}</span>
            &nbsp; {{update.comments[0].content}}
          </span>
        </div>
      </div>

      <div
        class="comment c2"
        v-if="(update.comments || []).length > 0 && update.comments[0].comments.length > 0"
      >
        <span
          :style="{'background-image' : 'url(\''+formatProfileImage(update.comments[0].comments[0].user.thumbnail)+'\')'}"
        ></span>
        <div class="content">
          <span>
            <span
              class="name"
              @click="goToPage('/users/' + update.comments[0].comments[0].user.id)"
            >{{update.comments[0].comments[0].user.name}}</span>
            &nbsp; {{update.comments[0].comments[0].content}}
          </span>
        </div>
      </div>
      <input
        placeholder="Add a comment"
        class="update-card-input"
        type="text"
        @keyup.enter="postComment()"
      /> -->
    </div>
    <share-post-modal
      :post="update"
      @closed="renderSharePostModal = false"
      v-if="renderSharePostModal"
    ></share-post-modal>
  </div>
</template>

<script>
import SharePostModal from "~/components/SharePostModal.vue";

export default {
  components: {
    SharePostModal
  },
  props: ["update"],
  data() {
    return {
      renderSharePostModal: false
    };
  },
  methods: {
    goToUpdate(id) {
      this.goToPage("/updates/" + id);
    },
    goToPage(path) {
      if (this.$nuxt.$router.fullPath == path) return;
      this.$nuxt.$router.push(path);
    },
    share() {
      event.stopPropagation();
      this.renderSharePostModal = true;
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
    postComment() {
      const val = event.target.value;
      event.target.value = "";

      if (!val) return;

      let data = {
        type: "post",
        id: this.update.id,
        content: val,
        post_id: this.update.id
      };

      let attempt = this.$store.dispatch("updates/comment", data);
    },
    formatProfileImage(img) {
      return (
        process.env.BACKEND_BASE_URL +
        (img ? img : "man-avatar-profile-icon.jpg")
      );
    }
  },
  computed: {
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
  }
};
</script>

<style scoped>
.updates-card {
  display: grid;
  /* grid-template-rows: 3.5rem 10rem 12rem; */
  grid-template-rows: 3.5rem 10rem 1fr;
  border-radius: 10px;
  overflow: hidden;
  background: white;
  color: black;
}

.dark-theme .updates-card {
  background: #140034;
  color: white;
}

/* Layer One */

.updates-card .layer1 {
  padding: 0.5rem;
  display: flex;
  align-items: center;
  padding-left: 1rem;
  padding-right: 1rem;
}

.updates-card .layer1 div {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.updates-card .layer1 h4 {
  font-size: 0.9rem;
  margin: 0;
  margin-bottom: 0.2rem;
  cursor: pointer;
}

.updates-card .layer1 small {
  color: #9b9b9b;
  font-size: 0.8rem;
}

.updates-card .layer1 span[data-profile-photo] {
  background-size: cover;
  background-repeat: no-repeat;
  height: 2.5rem;
  width: 2.5rem;
  border-radius: 50%;
}

/* Layer Two */

.updates-card .layer2 {
  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
  position: relative;
  cursor: pointer;
  background-color: rgba(0, 0, 0, 0.1);
  background-blend-mode: darken;

  --action-container-sizes: 2rem;
  --action-content-scale: 0.6;
  --action-content-padding: 0.5rem;
  --actions-container-height: 2.5rem;

  margin-bottom: calc(calc(var(--actions-container-height) / 2) + 0.8rem);
}

.updates-card .layer2:hover {
  background-color: rgba(0, 0, 0, 0.4);
}

.actions-container {
  position: absolute;
  display:flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-around;

  left: 0;
  right: 0;
  height: var(--actions-container-height);
  bottom: calc(-1 * var(--actions-container-height) / 2);
  /* overflow: hidden; */
  /* background: rgba(0, 0, 0, 0.5); */
}

.updates-card .layer2 .like-container {
  height: var(--action-container-sizes);
  width: var(--action-container-sizes);
  background: transparent;
  /* position: absolute; */
  /* right: 1rem;
  bottom: -0.5rem; */
  position: relative;
  background-position: center;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
}

.updates-card .layer2 .share-container {
  height: var(--action-container-sizes);
  width: var(--action-container-sizes);
  background: transparent;
  /* position: absolute; */
  /* right: 5rem;
  bottom: -0.5rem; */
  position: relative;
  background-position: center;
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
}

.updates-card .layer2 .like {
  width: calc(var(--action-content-scale) * var(--action-container-sizes));
  height: calc(var(--action-content-scale) * var(--action-container-sizes));
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  position: relative;
}

.updates-card .layer2 .like::before {
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

.dark-theme .updates-card .layer2 .like::before {
  background: #3f3d56;
}

.updates-card .layer2 .like .like-count {
  content: attr(data-like-count);
  position: absolute;
  bottom: calc(-100% - 0.5rem);
  right: 0;
  left: 0;
  color: #9b9b9b;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.7rem;
}

/* Layer Three */
.updates-card .layer3 {
  display: flex;
  flex-direction: column;
  padding: 0.5rem;
  position: relative;
  padding-bottom: 1rem;
}

.updates-card .layer3 .date {
  font-size: 0.8rem;
  color: #0084ff;
}

.updates-card .layer3 .title {
  font-size: 1.1rem;
  margin: 0.5rem;
  cursor: pointer;
}

.updates-card .layer3 .comment {
  display: flex;
  flex-direction: row;
  padding: 0.5rem;
}

.updates-card .layer3 .comment > span {
  height: 2rem;
  width: 2rem;
  border-radius: 50%;
  background-size: cover;
  background-repeat: no-repeat;
}

.updates-card .layer3 .comment .content {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: center;
  padding: 0.5rem;
  width: 100%;
}
.updates-card .layer3 .comment.c1 {
  z-index: 2;
}
.updates-card .layer3 .comment.c2 {
  padding-left: 3rem;
  position: relative;
}

.updates-card .layer3 .comment.c2 > span {
  position: relative;
}

.updates-card .layer3 .comment.c2 > span::after {
  content: " ";
  position: absolute;
  height: 100%;
  width: 2px;
  background: #959595;
  bottom: 102%;
  left: calc(50% - 2px / 2);
  z-index: 1;
}

.updates-card .layer3 .comment .content span {
  font-size: 0.8rem;
  color: #9b9b9b;
}

.updates-card .layer3 .comment .content > span {
  -webkit-line-clamp: 2;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.updates-card .layer3 .comment .content span.name {
  color: black;
  cursor: pointer;
}

.dark-theme .updates-card .layer3 .comment .content span.name {
  color: white;
}

.updates-card .layer3 .update-card-input {
  position: absolute;
  bottom: 0;
  right: 0;
  left: 0;
  width: 100%;
  border: 0;
  border-top: 0.5px solid #ddd;
  height: 3rem;
  text-indent: 10px;
  font-size: 0.8rem;
  color: rgba(0, 0, 0, 0.75);
  padding: 0.3rem;
  padding-right: 3rem;
  padding-left: 1rem;
  background: url("~assets/png/smiling-emoticon-square-face-2.png") no-repeat
    right 16px center;
  background-size: 1.5rem 1.5rem;
}

.dark-theme .updates-card .layer3 .update-card-input {
  color: rgba(255, 255, 255, 0.75);
}
.updates-card .layer3 .update-card-input:focus {
  outline: none;
}

@media (max-width: 767.98px) {
  .updates-card {
    grid-template-rows: 3.5rem 15rem 1fr;
  }
  /* .updates-card .layer3 {
    padding-bottom: 3rem;
  } */
}

@media (max-width: 575.98px) {
  .updates-card .layer2 {
    --action-container-sizes: 2.5rem;
    --action-content-scale: 0.5;
    --action-content-padding: 0.7rem;
  }
}
</style>
