<template>
  <div>
    <div id="profile-strip">
      <div class="header-image">
        <div
          class="cover-image"
          ref="coverImage"
          :style="{backgroundImage: 'url(\''+cover_image+'\')'}"
        >
          <div class="camera" @click="clickCoverImageInput()"></div>
          <input
            type="file"
            accept="image/*"
            hidden
            ref="coverImageInput"
            @change="commitCoverImage()"
          />
        </div>
        <div
          class="profile-photo"
          ref="displayPhoto"
          @click="clickDisplayPhoto()"
          :style="{backgroundImage: 'url(\''+display_photo+'\')'}"
        >
          <div class="camera" @click.stop="clickDisplayPhotoInput()"></div>
          <input
            type="file"
            accept="image/*"
            hidden
            ref="displayPhotoInput"
            @change.stop="commitDisplayPhoto()"
          />
        </div>
      </div>
      <div class="name-followers-container">
        <!-- When editing the class="...+ editing" -->
        <h4
          :class="{'has-errors' : (errors.name || []).length > 0}"
          class="name"
          ref="nameDisplay"
          @keydown.enter="commitNameChange()"
          @dblclick="editName()"
        >
          {{user.name}}
          <div class="edit edit-name" @click="editName()"></div>
        </h4>
        <ul class="username-error" v-if="(errors.name || []).length > 0">
          <li v-for="error in errors.name" :key="error">{{error}}</li>
        </ul>
        <div class="followers-container">
          <div class="followers">
            <div class="person-vector"></div>
            <div class="follow-main">
              <span>{{details.followers_count}}</span>
              <span>Followers</span>
            </div>
          </div>
          <div class="following">
            <div class="follow-main">
              <span>{{details.following_count}}</span>
              <span>Following</span>
            </div>
            <div class="person-vector"></div>
          </div>
        </div>
        <p
          v-if="!($auth.user.email_verified_at)"
          style="color: #FF5B45; text-align:center; font-size: 0.8rem;"
        >Accounts with unverified email will be deleted after 24hrs</p>
      </div>

      <div class="phone-number-contrainer">
        <div>
          <h6>Phone Number</h6>
          <span>{{user.phone_number}}</span>
        </div>
        <div class="edit edit-phone-number"></div>
      </div>

      <div class="social-media-container">
        <div class="social-media">
          <div class="social-name-plus-logo">
            <div class="social-media-logo" style="background-image: url('/png/envelope 1.png')"></div>
            <div class="name">{{user.email}}</div>
          </div>
          <button class="unverified">Verify</button>
        </div>

        <div class="social-media" v-if="false">
          <div class="social-name-plus-logo">
            <div class="social-media-logo" style="background-image: url('/png/instagram.png')"></div>
            <div class="name instagram">shegz.createev</div>
          </div>
          <button class="unverified">Verify</button>
        </div>
        <div class="social-media" v-if="false">
          <div class="social-name-plus-logo">
            <div class="social-media-logo" style="background-image: url('/png/twitter.png')"></div>
            <div class="name twitter">emmy_veek</div>
          </div>
          <button class>Verify</button>
        </div>
        <div class="social-media" v-if="false">
          <div class="social-name-plus-logo">
            <div class="social-media-logo" style="background-image: url('/png/linkedin.png')"></div>
            <div class="name linkedin">Solaru Olusegun</div>
          </div>
          <button class>Verify</button>
        </div>

        <div class="social-media" v-if="false">
          <div class="social-name-plus-logo">
            <div class="social-media-logo" style="background-image: url('/png/github.png')"></div>
            <div class="name github">Emmy_Veek (Shegz)</div>
          </div>
          <button class>Verify</button>
        </div>
      </div>
    </div>

    <div class="description-container">
      <div class="description">
        <p
          ref="descriptionParagraph"
          @dblclick="editDescription()"
        >{{user.about ? user.about : "Description"}}</p>

        <div class="edit" @click="editDescription()"></div>
      </div>

      <div class="save-button-container">
        <button
          ref="descriptionSaveButton"
          style="display:none"
          class="save-button"
          @click.stop="commitDescription()"
        >Save</button>
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
    return {
      errors: {
        name: []
      }
    };
  },
  computed: {
    user() {
      return this.$auth.user;
    },
    details() {
      return this.$store.state.profile.details;
    },
    display_photo() {
      return (
        process.env.BACKEND_BASE_URL +
        (this.user.display_photo ||
          (this.user.gender.toLowerCase() == "female"
            ? "woman-avatar-profile-icon.jpg"
            : "man-avatar-profile-icon.jpg"))
      );
    },
    cover_image() {
      return (
        process.env.BACKEND_BASE_URL.replace(/\/+$/, "") +
        "/" +
        (this.user.cover_image || "/cover-image-icon.jpg").replace(/^\/+/, "")
      );
    }
  },
  methods: {
    editName() {
      this.$refs.nameDisplay.setAttribute("contenteditable", "true");
      if (!this.$refs.nameDisplay.classList.contains("editing"))
        this.$refs.nameDisplay.classList.add("editing");
    },
    commitNameChange() {
      this.errors.name = [];
      this.$refs.nameDisplay.removeAttribute("contenteditable");
      if (this.$refs.nameDisplay.classList.contains("editing"))
        this.$refs.nameDisplay.classList.remove("editing");

      let text = this.$refs.nameDisplay.textContent.trim();

      let formData = new FormData();
      formData.append("name", text);
      let attempt = this.$store.dispatch("updateUser", formData);
      attempt.catch(errors => {
        if (errors.name) {
          this.errors.name = errors.name || [];
        }
      });
    },
    editDescription() {
      this.$refs.descriptionParagraph.setAttribute("contenteditable", "true");
      this.$refs.descriptionParagraph.style["text-decoration"] = "underline";
      this.$refs.descriptionSaveButton.style["display"] = "inherit";
    },
    commitDescription() {
      this.$refs.descriptionParagraph.removeAttribute("contenteditable");
      this.$refs.descriptionParagraph.style["text-decoration"] = "none";
      this.$refs.descriptionSaveButton.style["display"] = "none";

      let text = this.$refs.descriptionParagraph.textContent.trim();

      let formData = new FormData();
      formData.append("about", text);
      let attempt = this.$store.dispatch("updateUser", formData);
    },
    clickDisplayPhotoInput() {
      this.$refs.displayPhotoInput.click();
    },
    commitDisplayPhoto() {
      let formData = new FormData();
      formData.append("display_photo", this.$refs.displayPhotoInput.files[0]);

      this.$store.dispatch("updateUser", formData);
    },
    clickCoverImageInput() {
      event.stopPropagation();
      this.$refs.coverImageInput.click();
    },
    commitCoverImage() {
      let formData = new FormData();
      formData.append("cover_image", this.$refs.coverImageInput.files[0]);
      this.$store.dispatch("updateUser", formData);
    },
    clickDisplayPhoto() {
      let displayPhoto = this.$refs.displayPhoto;
      if (event.target == displayPhoto)
        this.$refs.displayPhotoModal.openModal();
    },
    clickCoverImage() {
      let coverImage = this.$refs.coverImage;
      if (event.target == coverImage) this.$refs.coverImageModal.openModal();
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

.camera {
  background-image: url("/png/camera.png");
  background-size: contain;
  background-position: center center;
  background-repeat: no-repeat;
  height: 2rem;
  width: 2rem;
  cursor: pointer;
}

.cover-image .camera {
  position: absolute;
  right: 0.7rem;
  top: 0.7rem;
  z-index: 1;
}

.profile-photo .camera {
  position: absolute;
  top: 7rem;
  right: 0.5rem;
  z-index: 1;
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

  position: relative;
  padding-left: 2rem;
  padding-right: 2rem;
  word-break: break-word;
}

.name.editing {
  text-decoration: underline;
  display: inherit;
}

.name.has-errors {
  margin-bottom: 0.5rem;
}

.username-error {
  font-size: 0.8rem;
  color: white;
  background: black;
  padding: 1rem;
  border-radius: 5px;
  list-style: arabic-indic;
  padding-inline-start: 2rem;
  margin-bottom: 2rem;
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

.edit.edit-name {
  height: 2rem;
  width: 2rem;
  display: inline-block;
  padding: 0.5rem;
  background-origin: content-box;
  position: absolute;
  right: 0.5rem;
  top: -1rem;
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

/* assssssssssssssssssssssssssssssssssssss */

.save-button-container {
  display: flex;
  justify-content: flex-end;
}
.save-button {
  background: #0084ff;
  color: white;
  padding-right: 0.5rem;
  padding-left: 0.5rem;
  font-size: 0.75rem;
  border: none;
  border-radius: 2px;
  cursor: pointer;
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
</style>
