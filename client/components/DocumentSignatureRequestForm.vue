<template>
  <div class="document-signature-request-form" v-if="isShowing">
    <div class="content">
      <div class="close-button" @click="$emit('hide'); close();"></div>

      <div class="action-section">
        <div class="on-platform-search">
          <form class="input-group" @submit.prevent="searchForUsers()">
            <input
              type="text"
              class="search-input"
              ref="searchInput"
              placeholder="Search for user here..."
            />
            <button class="search-icon"></button>
          </form>
          <!-- <div class="search-result-message" ref="searchResultMessage"></div> -->
          <div class="search-results">
            <div
              v-show="users.length < 1"
              class="no-result-found-text"
              ref="searchResultMessage"
            >Search for users...</div>
            <div v-if="users.length >= 1">
              <div class="search-result-item" v-for="user in users" :key="user.id">
                <div class="profile-photo-container">
                  <div
                    class="profile-photo"
                    :style="{backgroundImage: backgroundPhoto(!!user.thumbnail ? user.thumbnail : (!!user.display_photo ? user.display_photo : 'man-avatar-profile-icon.jpg'))}"
                  ></div>
                </div>
                <span class="name">{{user.name}}</span>
                <div class="search-result-item-button-container">
                  <button
                    class="search-result-item-button"
                    v-if="!receivers.some(el => el.id == user.id)"
                    @click="addOnPlatformUser(user)"
                  ></button>
                  <button class="search-result-item-button checked" v-else></button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="outsider-section">
          <div>
            <h5 class="outsider-form-header">External User</h5>
            <form
              action
              class="outsider-form"
              @keyup.enter="addExternalUser()"
              @submit.prevent="addExternalUser()"
            >
              <input
                type="text"
                class="form-input"
                placeholder="Enter full name"
                ref="outsiderNameInput"
              />
              <div class="form-group">
                <input
                  type="email"
                  class="form-input"
                  placeholder="Enter email address"
                  ref="outsiderEmailInput"
                />
                <label class="add-button" @click="addExternalUser()"></label>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="display-section">
        <div class="color-details">
          <div>
            <div>
              <div style="background: #0084ff"></div>
            </div>
            <span>AboutCert</span>
          </div>
          <div>
            <div>
              <div style="background: #FF0099;"></div>
            </div>
            <span>External User</span>
          </div>
        </div>
        <div class="signatory-list-container">
          <div class="top">
            <h5 class="signatory-list-header">Signatory List</h5>
            <div class="signatory-list">
              <div
                class="signatory-list-item"
                v-for="user in [...receivers, ...outsiders]"
                :key="user.email"
              >
                <div class="display-flex justify-content-center align-items-center">
                  <div
                    :style="{height: '4px', width: '4px', borderRadius: '50%', background: user.id  ? '#0084ff' : '#FF0099'}"
                  ></div>
                </div>
                <span class="name display-flex align-items-center">{{user.name}}</span>
                <button class="signatory-remove-button" @click="removeUser(user)">Remove</button>
              </div>
            </div>
          </div>
          <div class="bottom">
            <div class="current-user-signatory-checkbox-container">
              <input
                ref="currentUserSignatureCheckbox"
                type="checkbox"
                id="current-user-signatory-checkbox"
              />
              <label for="current-user-signatory-checkbox">I will also be signing this document</label>
            </div>

            <div class="display-flex align-items-center justify-content-center">
              <button
                class="request-signature-button"
                @click="requestDocumentSignature()"
              >Request Signature</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      isShowing: false,
      receivers: [],
      outsiders: []
    };
  },
  props: ["file"],
  methods: {
    show() {
      this.isShowing = true;
    },
    close() {
      this.isShowing = false;
    },
    requestDocumentSignature() {
      let checkbox = this.$refs.currentUserSignatureCheckbox;

      let receiverIds = this.receivers.map(
        (currentVal, index) => currentVal.id
      );

      if (checkbox.checked && !receiverIds.some(el => el == this.$auth.user.id))
        receiverIds.push(this.$auth.user.id);

      if (this.outsiders.length < 1 && receiverIds.length < 1) return;

      let data = {
        receiver_ids: JSON.stringify(receiverIds),
        outsiders: JSON.stringify(this.outsiders),
        document_id: this.file.id
      };

      let attempt = this.$store.dispatch(
        "documents/requestDocumentSignature",
        data
      );

      attempt
        .then(response => {
          window.open(response.embeddedSessionURL, "_self");
        })
        .catch(err => {
          if (err.response && err.response.status) {
            if (err.response.status == 322) {
              this.$emit("noPriviledge");
            }
          }
        });
    }, //end function requestDocumentSignature
    searchForUsers() {
      let searchString = this.$refs.searchInput.value;

      if (!searchString) return;

      this.users = [];

      this.$refs.searchResultMessage.textContent = "Searching ...";
      let attempt = this.$store.dispatch("users/searchForUsers", {
        searchString: searchString
      });

      attempt.then(data => {
        if (data.data.length <= 0) {
          this.$refs.searchResultMessage.textContent = "No Result Found";
          this.searchData = {};
        } else {
          let temp = data;

          temp.data = temp.data.filter(el => el.id != this.$auth.user.id);

          this.users = temp.data;

          if (this.users.length == 0)
            this.$refs.searchResultMessage.textContent = "No Result Found";
        }
      });
    },
    backgroundPhoto(val) {
      return `url('${process.env.BACKEND_BASE_URL + val}')`;
    },
    addOnPlatformUser(user) {
      if (!user) return;
      if (!this.receivers.some(el => el.id == user.id))
        this.receivers.push(user);
    },
    addExternalUser() {
      let user = {
        email: this.$refs.outsiderEmailInput.value,
        name: this.$refs.outsiderNameInput.value
      };

      if (!(user.name || user.email)) return;

      this.$refs.outsiderEmailInput.value = "";
      this.$refs.outsiderNameInput.value = "";

      if (!this.outsiders.some(el => el.email == user.email))
        this.outsiders.push(user);
    },
    removeUser(user) {
      if (!user) return;

      if (!(user.id || user.email)) return;

      // console.log(user)

      if (user.id)
        this.receivers.splice(
          this.receivers.findIndex(el => el.id == user.id),
          1
        );
      else
        this.outsiders.splice(
          this.outsiders.findIndex(el => el.email == user.email),
          1
        );
    }
  }
};
</script>

<style scoped>
.document-signature-request-form {
  margin: 0;
  padding: 0;
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background: rgba(0, 0, 0, 0.25);
  z-index: 999;

  display: flex;
  align-items: center;
  justify-content: center;
  --search-input-height: 25px;
  --search-result-item-height: 30px;
}

input {
  font-size: 0.7rem;
}

input:focus {
  outline: none;
}

.document-signature-request-form .content {
  max-width: 70%;
  width: 100%;
  background: white;
  display: grid;
  grid-template-columns: 3fr 2fr;
  grid-gap: 1rem;
  padding: 1rem;
  border-radius: 10px;
  position: relative;
}

.close-button {
  position: absolute;
  --close-button-size: 50px;
  width: var(--close-button-size);
  height: var(--close-button-size);
  top: calc(-1 * (var(--close-button-size) / 2));
  right: calc(-1 * (var(--close-button-size) / 2));
  border-radius: 50%;
  background-color: rgba(0, 0, 0, 0.4);
  cursor: pointer;

  background-image: url("/png/x.png");
  background-size: 25%;
  background-repeat: no-repeat;
  background-position: center center;
}

.document-signature-request-form .content .action-section > div {
  background: #f2f2f2;
  border-radius: 5px;
  padding: 0.5rem;
}

.document-signature-request-form .content .display-section {
  display: flex;
  flex-direction: column;
}

.on-platform-search {
  margin-bottom: 1rem;
}

.on-platform-search .input-group {
  height: var(--search-input-height);
  display: grid;
  grid-template-columns: 1fr var(--search-input-height);
  background: white;
}

.on-platform-search .input-group input.search-input {
  background: white;
  border: none;
  border-radius: 3px;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  padding-left: 0.5rem;
}

.on-platform-search .input-group input.search-input:focus {
  outline: none;
}

.on-platform-search .input-group .search-icon {
  background-color: white;
  background-image: url("/png/search.png");
  border: none;
  outline: none;
  padding: 0.2rem;
  border-radius: 3px;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;

  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  background-origin: content-box;
  cursor: pointer;
}

.on-platform-search .search-results {
  margin-top: 1rem;
  margin-bottom: 1rem;
}

.search-result-item {
  display: grid;
  grid-template-columns: calc(var(--search-result-item-height) + 5px) 1fr calc(
      var(--search-result-item-height) + 5px
    );
  grid-gap: 5px;
  height: var(--search-result-item-height);
  font-size: 0.8rem;
  color: black;
  margin-bottom: 0.5rem;
}

.search-result-item:last-child {
  margin-bottom: 0;
}

.search-result-item .profile-photo {
  width: 100%;
  height: 100%;
  clip-path: circle();
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
}

.search-result-item .name {
  display: flex;
  align-items: center;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.search-result-item .search-result-item-button-container {
  padding: 0.3rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.search-result-item .search-result-item-button {
  height: 100%;
  width: 100%;
  border: none;
  padding: 0.3rem;
  border-radius: 3px;
  background-color: #0084ff;
  background-image: url("/png/plus.png");
  background-repeat: no-repeat;
  background-size: contain;
  background-position: center center;
  background-origin: content-box;
}

.search-result-item .search-result-item-button.checked {
  background-color: #25ff01;
}

.on-platform-search .no-result-found-text {
  font-style: italic;
  font-size: 0.9rem;
}

.outsider-form-header {
  color: #555286;
  font-weight: 500;
  margin-top: 0.2rem;
  margin-bottom: 0.5rem;
}

.outsider-form .form-input {
  width: 100%;
  background: white;
  border-radius: 3px;
  border: none;
  height: var(--search-input-height);
}

.outsider-form .form-group {
  margin-left: 0;
  margin-bottom: 0;
  padding: 0;
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  align-items: center;
  justify-content: space-between;
}

.outsider-form .form-group .form-input {
  width: calc(100% - var(--search-input-height) - 0.5rem);
}

.outsider-form .add-button {
  height: var(--search-input-height);
  width: var(--search-input-height);
  border-radius: 3px;
  cursor: pointer;
  background-color: #0084ff;
  background-image: url("/png/plus.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  padding: 0.3rem;
  background-origin: content-box;
}

.signatory-list-container {
  background: #f2f2f2;
  border-radius: 5px;
  padding: 0.7rem;
  font-size: 0.8rem;
  color: black;
  /* flex: 1; */
  display: flex;
  flex-direction: column;
}

.signatory-list-container .bottom {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: flex-end;
}

.signatory-list-header {
  color: #555286;
  font-weight: 500;
  margin-top: 0.2rem;
  margin-bottom: 0.8rem;
  font-size: 1rem;
  font-weight: 500;
}

.signatory-list-item {
  display: grid;
  grid-template-columns: 15px 1fr 70px;
  overflow: hidden;
  margin-bottom: 0.7rem;
}

.signatory-list-item:last-child {
  margin-bottom: 0;
}

.signatory-list-item .name {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.signatory-remove-button {
  color: #00826c;
  border: none;
  background: #adfff1;
  border-radius: 3px;
  font-size: 0.6rem;
  height: 100%;
}

.current-user-signatory-checkbox-container {
  margin-top: 1rem;
  margin-bottom: 1rem;

  font-size: 0.7rem;
  color: #797979;
}

.request-signature-button {
  background: #0084ff;
  color: white;
  border: none;
  border-radius: 3px;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  padding-left: 0.8rem;
  padding-right: 0.8rem;
}

.color-details {
  padding-top: 0.5rem;
  margin-bottom: 1rem;
  font-size: 0.8rem;
  font-style: italic;
  display: flex;
  align-items: center;
}

.color-details > div {
  flex: 1;
  display: grid;
  grid-template-columns: 10px 1fr;
}

.color-details > div div:first-child {
  display: flex;
  align-items: center;
  justify-content: center;
}

.color-details > div div:first-child div {
  height: 4px;
  width: 4px;
  border-radius: 50%;
  background: black;
}

/* // Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
  .document-signature-request-form .content {
    max-width: 80%;
  }
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
  .document-signature-request-form .content {
    max-width: 90%;
  }
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
  .document-signature-request-form .content {
    grid-template-columns: 1fr;
  }

  .color-details{
    margin-top: 1rem;
  }
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
  .close-button {
    --close-button-size: 30px;
  }
}
</style>