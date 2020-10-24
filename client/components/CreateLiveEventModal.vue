<template>
  <div class="create-live-event-modal" ref="modal">
    <div class="create-live-event-option-modal">
      <div class="header">
        <span>Go Live</span>
        &nbsp;
        <span class="close" @click="closeModal()">&times;</span>
      </div>
      <div class="body">
        <div>
          <div class="title-input-container">
            <input
              class="title-input"
              placeholder="Give your live event a title"
              type="text"
              ref="titleInput"
            />
          </div>
          <div class="cover-image-input-container">
            <span>Upload a cover image</span>
            <button class="select-cover-image-button" @click="selectCoverImage()">Upload</button>
            <input
              @change="updatePreviewCoverImage()"
              hidden
              type="file"
              accept="image/*"
              ref="coverImage"
            />
          </div>
          <div id="cover-image-preview-container">
            <img id="cover-image-preview" src alt ref="coverImagePreview" />
          </div>
        </div>
        <div>Select who you want to share your live session with</div>
        <div class="option-button-container">
          <div
            class="option-button"
            @click="selectOption('all')"
            :class="{active: options.some(el => el == 'all')}"
          >Public</div>
          <div
            class="option-button"
            @click="selectOption('followers')"
            :class="{active: options.some(el => el == 'followers')}"
          >All Followers</div>
          <div
            class="option-button"
            @click="selectOption('specific-users')"
            :class="{active: selectingSpecificUsers, muted: !canSelectSpecificUsers}"
          >Specific Followers</div>
        </div>
      </div>
      <div class="footer">
        <button class="send-button" ref="createButton" @click="create()">Create</button>
      </div>
      <div class="other-users" v-show="selectingSpecificUsers">
        <div class="search-other-users">
          <input
            @keyup="searchUsers()"
            ref="searchInput"
            type="text"
            class="search-input"
            placeholder="Search Users..."
          />
          <img class="search-icon" src="/png/other-users-search.png" />
        </div>
        <div class="other-users-container">
          <div
            class="user-strip"
            @click="toggleSelectUser(user)"
            :class="{selected : options.some(el => el == user.id)}"
            v-for="user in users"
            :key="user.id"
          >
            <div class="profile-photo-container">
              <div class="profile-photo"></div>
            </div>
            <div class="user-info-container">
              <span class="name">{{user.name}}</span>
            </div>
            <div class="triple-dots-container">
              <div class="triple-dots"></div>
            </div>
          </div>
        </div>
        <div class="footer">
          <button class="go-live-button" @click="create()">Go live</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      options: ["all"],
      selectingSpecificUsers: false,
      users: [],
      selectedUsers: []
    };
  },
  computed: {
    canSelectSpecificUsers() {
      return !this.options.some(el => el == "all");
    }
  },
  methods: {
    selectOption(val) {
      if (val == "specific-users") {
        this.selectingSpecificUsers = true;
        if (this.options.some(el => el == "all"))
          this.options.splice(
            this.options.findIndex(el => el == "all"),
            1
          );
        return;
      }

      if (val == "followers" && this.options.some(el => el == "all")) {
        this.options.splice(
          this.options.findIndex(el => el == "all"),
          1
        );
      }

      if (val == "all") {
        this.options = [];
        this.selectingSpecificUsers = false;
      }

      if (!this.options.some(el => el == val)) {
        this.options.push(val);
      } else {
        this.options.splice(
          this.options.findIndex(el => el == val),
          1
        );
      }
    },
    selectCoverImage() {
      this.$refs.coverImage.click();
    },
    create() {
      if (
        this.options.length == 0 ||
        this.$refs.coverImage.files.length == 0 ||
        this.$refs.titleInput.value == ""
      )
        return;

      let formData = new FormData();
      formData.append("cover_image", this.$refs.coverImage.files[0]);
      formData.append("title", this.$refs.titleInput.value);
      formData.append("can_join", JSON.stringify(this.options));

      let button = this.$refs.createButton;

      button.setAttribute("disabled", "true");

      let attempt = this.$store.dispatch("live_events/createLiveEvent", {
        data: formData
      });
      attempt
        .then(id => {
          this.$nuxt.$router.push("/events/live/" + id);
        })
        .catch(() => {
          button.removeAttribute("disabled");
        });
    },
    closeModal() {
      this.options = ["all"];
      this.selectingSpecificUsers = false;
      this.$refs.titleInput.value = "";
      this.$refs.coverImage.value = "";
      this.$refs.modal.style.display = "none";
    },
    openModal() {
      this.$refs.modal.style.display = "flex";
    },
    searchUsers() {
      const val = this.$refs.searchInput.value;
      if (!val) return;

      this.$axios
        .get("/api/users/search?query=" + val)
        .then(response => response.data)
        .then(response => {
          this.users = response.data;
        })
        .catch(err => console.log(err));
    },
    toggleSelectUser(user) {
      let index = this.options.findIndex(el => el == user.id);
      if (index >= 0) {
        this.selectOption(user.id);
        this.selectedUsers.splice(
          this.selectedUsers.findIndex(el => el.id == user.id),
          1
        );
      } else {
        this.selectOption(user.id);
        this.selectedUsers.push(user);
      }
    },
    updatePreviewCoverImage() {
      const src = URL.createObjectURL(this.$refs.coverImage.files[0]);
      this.$refs.coverImagePreview.src = src;
    }
  },
  mounted() {}
};
</script>

<style scoped>
.create-live-event-modal {
  position: fixed;
  top: 0;
  right: 0;
  left: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.25);
  z-index: 999;
  display: flex;
  align-items: center;
  justify-content: center;

  overflow-y:auto;
  display: none;
}

.create-live-event-option-modal {
  width: 500px;
  display: flex;
  flex-direction: column;
  background: white;
  border-radius: 12px;
  position: relative;
  --modal-padding: 1.5rem;
}

.create-live-event-option-modal .header {
  height: 40px;
  background: #ecf6ff;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-left: var(--modal-padding);
  padding-right: var(--modal-padding);
  color: #3a2c51;
  font-size: 1rem;
}

.create-live-event-option-modal .header .close {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.5rem;
  cursor: pointer;
  padding-left: 1rem;
  padding-right: 1rem;
  transform: translateX(1rem);
}

.create-live-event-option-modal .body {
  padding: var(--modal-padding);
  padding-bottom: 0.5rem;
  font-size: 0.9rem;
}

.title-input-container {
  margin-bottom: 1rem;
}

.title-input {
  width: 100%;
  border-radius: 3px;
  border: 1px solid #c4c4c4;
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.cover-image-input-container {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.cover-image-input-container span {
  color: black;
  font-weight: 500;
  margin-right: 1rem;
}

.select-cover-image-button {
  color: white;
  background: #0084ff;
  border-radius: 3px;
  border: none;
  padding: 0.4rem 0.8rem 0.4rem 0.8rem;
}

#cover-image-preview-container {
  margin-bottom: 2rem;
}

#cover-image-preview {
  max-height: 60%;
  max-width: 200px;
}

.option-button-container {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  margin-top: 1rem;
}

.option-button-container .option-button {
  --height: 25px;
  height: var(--height);
  font-size: 0.8rem;
  color: #555555;
  background: #f1f1f1;
  display: flex;
  align-items: center;
  padding-left: 0.8rem;
  padding-right: 0.8rem;
  border-radius: calc(var(--height) / 2);
  cursor: pointer;

  margin-right: 0.5rem;
  margin-bottom: 0.5rem;
}

.option-button-container .option-button:last-child {
  margin-right: 0;
}

.option-button-container .option-button.active {
  background: #e3f2ff;
  color: #0084ff;
}

.option-button-container .option-button.muted {
  /* cursor: not-allowed; */
  color: rgba(165, 165, 165, 0.79);
}

.footer {
  margin-top: 0.5rem;
  padding: var(--modal-padding);
  padding-top: 0.5rem;
  display: flex;
  justify-content: flex-end;
}

.footer .send-button {
  border: none;
  background: #0084ff;
  color: white;
  border-radius: 5px;
  padding: 0.5rem;
  padding-left: 0.8rem;
  padding-right: 0.8rem;
}

/* For the other users section */
.other-users {
  display: flex;
  flex-direction: column;
  position: absolute;
  width: calc(calc(100vw - 100% - 5rem) / 2);
  max-height: calc(
    -2rem + var(--viewport-height, 100vh) - calc(calc(
            var(--viewport-height, 100vh) - 100%
          ) / 2)
  );
  left: calc(100% + 0.8rem);
  top: 0;
  overflow: hidden;
  border-radius: 10px;
}

.search-other-users {
  border-radius: 10px;
  background: #ecf6ff;
  display: flex;
  flex-direction: row;
  align-items: center;
  /* overflow: hidden; */
  height: 40px;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

.search-input {
  flex: 1;
  height: 100%;
  outline: none;
  background: transparent;
  border: none;
  padding-right: 1rem;
  padding-left: 0.5rem;
}

input.search-input:focus,
input.search-input:active {
  border: none;
  outline: none;
}

.search-input::placeholder {
  color: #999999;
}

.search-icon {
  height: 60%;
  object-fit: contain;
  cursor: pointer;
}

.other-users-container {
  margin-top: 1rem;
  padding: 0.5rem;
  background: white;
  flex: 1;
  overflow: hidden;
  overflow-y: auto;
  border-radius: 10px;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}

.user-strip {
  display: grid;
  grid-template-columns: 40px 1fr 30px;
  gap: 0.8rem;
  background: #ecf6ff;
  border-radius: 5px;
  padding: 0.5rem;
  margin-bottom: 0.5rem;
  height: 65px;
}

.user-strip.selected {
  color: white;
  background: #0084ff;
}

.profile-photo-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.profile-photo-container .profile-photo {
  background-image: linear-gradient(to right, green, blue);
  height: 35px;
  width: 35px;
  border-radius: 50%;
  position: relative;
}

.profile-photo-container .profile-photo::before {
  content: "";
  position: absolute;
  top: -2px;
  bottom: -2px;
  right: -2px;
  left: -2px;
  border: #0084ff solid 1px;
  border-radius: 50%;
}

.user-strip.selected .profile-photo-container .profile-photo::before {
  border: #fff solid 1px;
}

.user-info-container .name {
  font-size: 0.8rem;
}

.triple-dots {
  height: 100%;
  width: 100%;
  padding: 0.7rem;
  padding-right: 0.1rem;
  background: url(/png/triple-dots.png);
  background-size: contain;
  background-repeat: no-repeat;
  background-position: right center;
  background-origin: content-box;
}

.other-users .footer {
  background: white;
  margin-top: 0;
  padding-bottom: 0.5rem;
}

.go-live-button {
  padding: 0.4rem 0.7rem;
  border: none;
  color: white;
  background: #0084ff;
  border-radius: 3px;
}

/* // Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
  .create-live-event-option-modal {
    width: 50%;
  }
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
  .create-live-event-option-modal {
    width: 70%;
  }

  .other-users {
    width: 100%;
    max-height: unset;
    left: 0;
    top: calc(100% + 0.8rem);
    border-radius: 10px;
    margin-bottom: 6rem;
  }
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
  .create-live-event-option-modal {
    width: 80%;
  }
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
  .create-live-event-option-modal {
    width: 90%;
  }
}
</style>