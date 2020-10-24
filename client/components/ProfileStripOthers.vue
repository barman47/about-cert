<template>
  <div>
    <div id="profile-strip">
      <div class="header-image">
        <div
          @click="clickCoverImage()"
          class="cover-image"
          :style="{backgroundImage: 'url(\''+cover_image+'\')'}"
        ></div>
        <div
          @click="clickDisplayPhoto()"
          class="profile-photo"
          :style="{backgroundImage: 'url(\''+display_photo+'\')'}"
        ></div>
      </div>
      <div class="name-followers-container">
        <h4 class="name">{{user.name}}</h4>
        <div class="followers-container">
          <div class="followers">
            <div class="person-vector"></div>
            <div class="follow-main">
              <span>{{user.followers_count}}</span>
              <span>Followers</span>
            </div>
          </div>
          <div class="following">
            <div class="follow-main">
              <span>{{user.following_count}}</span>
              <span>Following</span>
            </div>
            <div class="person-vector"></div>
          </div>
        </div>
        <div class="follow-action-container">
          <button
            v-show="isFollowingUser == 0"
            @click="followUser()"
            class="follow-action-button follow"
            ref="followButton"
          >Follow</button>
          <button
            v-show="isFollowingUser == 1"
            @click="unfollowUser()"
            class="follow-action-button unfollow"
            ref="unfollowButton"
          >Unfollow</button>
        </div>
      </div>

      <div class="phone-number-contrainer">
        <div>
          <h6>Phone Number</h6>
          <span>{{user.phone_number}}</span>
        </div>
      </div>

      <div class="social-media-container">
        <div class="social-media">
          <div class="social-name-plus-logo">
            <div class="social-media-logo" style="background-image: url('/png/envelope 1.png')"></div>
            <div class="name">{{user.email}}</div>
          </div>
        </div>
      </div>
    </div>

    <div class="description-container" v-if="user.about && user.about != ''">
      <div class="description">
        <p>{{user.about}}</p>
      </div>
    </div>

    <!-- Display photo modal -->
    <modal :id="'display-photo-modal' + user.id" size ref="displayPhotoModal">
      <template v-slot:header>Profile Photo</template>
      <template v-slot:footer>
        <span></span>
      </template>

      <!-- modal body -->
      <div class="displayed-photo-container">
        <img :src="display_photo" alt="Display Photo" />
      </div>
    </modal>

    <!-- Cover photo modal -->
    <modal :id="'cover-photo-modal' + user.id" size ref="coverImageModal">
      <template v-slot:header>Cover Image</template>
      <template v-slot:footer>
        <span></span>
      </template>

      <!-- modal body -->
      <div class="displayed-photo-container">
        <img :src="cover_image" alt="Cover Image" />
      </div>
    </modal>
  </div>
</template>

<script>
import Modal from "~/components/Modal";
export default {
  components: {
    Modal
  },
  data() {
    return {};
  },
  mounted() {},
  computed: {
    user() {
      return this.$store.state.other_users.list.find(
        el => el.id == this.$nuxt.$route.params.id
      );
    },
    isFollowingUser() {
      return this.user.is_following_user;
    },
    display_photo() {
      // return process.env.BACKEND_BASE_URL +   "woman-avatar-profile-icon.jpg"
      return (
        process.env.BACKEND_BASE_URL +
        (this.user.display_photo ||
          (this.user.gender.toLowerCase() == "female"
            ? "woman-avatar-profile-icon.jpg"
            : "man-avatar-profile-icon.jpg"))
      );
    },
    cover_image() {
      // return process.env.BACKEND_BASE_URL + "cover-image-icon.jpg"
      return (
        process.env.BACKEND_BASE_URL +
        (this.user.cover_image || "cover-image-icon.jpg")
      );
    }
  },
  methods: {
    clickDisplayPhoto() {
      this.$refs.displayPhotoModal.openModal();
    },
    clickCoverImage() {
      this.$refs.coverImageModal.openModal();
    },
    followUser() {
      if (this.isFollowingUser) {
        return;
      }

      this.$refs.followButton.setAttribute("disabled", "true");
      this.$store
        .dispatch("users/follow", { user_id: this.user.id })
        .finally(() => {
          this.$refs.followButton.removeAttribute("disabled");
        });
    },
    unfollowUser() {
      if (!this.isFollowingUser) {
        return;
      }

      this.$refs.unfollowButton.setAttribute("disabled", "true");
      this.$store
        .dispatch("users/unfollow", { user_id: this.user.id })
        .finally(() => {
          this.$refs.unfollowButton.removeAttribute("disabled");
        });
    }
  }
};
</script>

<style scoped>
#profile-strip {
  background: white;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  margin-bottom: 2rem;
}

.description-container {
  display: flex;
  flex-direction: column;
  padding: 1rem;
  background: white;
  border-radius: 10px;
  color: #757575;
  font-size: 0.9rem;
  font-weight: normal;
  font-style: normal;
  line-height: 1.2rem;
}

.description {
  display: flex;
}

.description p {
  flex: 10;
}

.description .edit {
  height: 1.2rem;
  width: 1.2rem;
  flex: 1;
}

.header-image {
  height: 8rem;
  position: relative;
  margin-bottom: 6rem;
}

.cover-image {
  background-image: url("/test.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  height: 100%;
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  border-radius: 10px;

  cursor: pointer;
}

.profile-photo {
  background-image: url("/test.jpg");
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  height: 10rem;
  width: 10rem;
  position: absolute;
  bottom: -5rem;
  left: calc(50% - 10rem / 2);
  border-radius: 50%;
  border: 5px solid white;

  cursor: pointer;
}

.name-followers-container {
  padding-left: 1rem;
  padding-right: 1rem;
  padding-bottom: 3rem;
  position: relative;
}
.name-followers-container::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 1rem;
  right: 1rem;
  height: 1px;
  background: #9b9b9b;
}

.name {
  font-size: 1.5rem;
  font-weight: normal;
  font-style: normal;
  margin-top: 0.5rem;
  text-align: center;
}

.followers-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.followers {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 0.5rem;
  border-right: 1px #9b9b9b solid;
}

.following {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0.5rem;
}

.person-vector {
  height: 2rem;
  width: 1rem;
  background-image: url("/png/person.png");
  background-size: contain;
  background-position: center center;
  background-repeat: no-repeat;
  margin-right: 0.5rem;
  margin-left: 0.5rem;
}

.follow-main {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  font-style: normal;
  font-weight: normal;
}

.follow-main > span:first-child {
  font-size: 1.1rem;
  color: #0084ff;
}

.follow-main > span:last-child {
  font-size: 0.9rem;
  color: #9b9b9b;
}

.phone-number-contrainer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  padding-left: 2rem;
  padding-right: 2rem;
  position: relative;
}

.phone-number-contrainer h6 {
  margin: 0;
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
  color: #c6c5e2;
  font-size: 0.8rem;
}

.phone-number-contrainer span {
  color: #9b9b9b;
  font-size: 0.9rem;
}

.phone-number-contrainer::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 1rem;
  right: 1rem;
  height: 1px;
  background: #9b9b9b;
}

.edit {
  background-image: url("/png/edit.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  cursor: pointer;
}

.edit-phone-number {
  height: 1rem;
  width: 1rem;
}

.social-media-container {
  padding: 1rem;
}
.social-media {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.social-name-plus-logo {
  display: flex;
  align-items: center;
}

.social-media-logo {
  height: 1.5rem;
  width: 1.5rem;
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  margin-right: 1rem;
}

.social-name-plus-logo .name {
  font-size: 0.8rem;
  color: #9b9b9b;
}
.social-media {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
}

.social-media button {
  background: #0084ff;
  font-size: 0.9rem;
  border: none;
  padding: 0.5rem;
  padding-left: 1rem;
  padding-right: 1rem;
  color: white;
  font-weight: normal;
  font-size: normal;
  cursor: pointer;
}

.social-media .unverified {
  background: white;
  color: #9b9b9b;
  border: 1.2px solid #9b9b9b;
}

.social-media .name.instagram {
  color: #c74c4d;
}

.social-media .name.twitter {
  color: #76a9ea;
}

.social-media .name.linkedin {
  color: #0077b7;
}

.social-media .name.github {
  color: #293939;
}

/* For display photo and cover photo */
.displayed-photo-container {
  display: flex;

  align-items: center;
  justify-content: center;
  height: 100%;
  width: 100%;
}

.displayed-photo-container img {
  max-height: 100%;
  max-width: 100%;

  object-fit: contain;
}

/* Follow and Unfollow buttons */
#profile-strip .follow-action-container {
  display: flex;
  justify-content: center;
  margin-top: 1rem;
}

#profile-strip .follow-action-container .follow-action-button {
  font-size: 0.8rem;
  padding: 0.2rem;
  padding-left: 0.4rem;
  padding-right: 0.4rem;
  border-radius: 3px;
  border: none;
}

#profile-strip .follow-action-container .follow-action-button.follow {
  color: #0084ff;
  background: #dbeeff;
}

#profile-strip .follow-action-container .follow-action-button.unfollow {
  background: #c0c4c7;
  color: #687179;
}
</style>
