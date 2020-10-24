<template>
  <div class="share-document-modal" ref="modal">
    <div class="modal">
      <div class="modal-header">
        <span>Share File</span>
        <span @click="closeModal()" class="close-button">&times;</span>
      </div>
      <div class="content">
        <div class="search-in-app">
          <form action @submit.prevent="searchForUSer()">
            <div class="search-in-app-input-group">
              <input type="text" placeholder="Search User Here" ref="searchInput" />
              <span class="button-container">
                <button></button>
              </span>
            </div>
          </form>

          <div class="search-result-message" ref="searchResultMessage"></div>

          <div class="search-result" v-show="users.length > 0">
            <div
              @click="getUserYellow(user.id)"
              class="user-strip"
              v-for="user in users"
              :key="user.id"
            >
              <div v-if="user.id == yellowUserId" class="floating-folders">
                <div class="header">
                  <span>Send File To</span>
                  <span class="close-modal" @click="resetYellowUser()">&times;</span>
                </div>
                <share-document-dir
                  :user_id="yellowUserId"
                  :checkObj="shareDocumentHolder"
                  :isParent="true"
                ></share-document-dir>
                <div class="footer">
                  <button
                    class="send-document-button"
                    id="sendDocumentButton"
                    @click.prevent="shareDocument()"
                  >Send</button>
                </div>
              </div>
              <div class="profile-photo-container">
                <div
                  class="profile-photo"
                  :style="{backgroundImage: backgroundPhoto(!!user.thumbnail ? user.thumbnail : (!!user.display_photo ? user.display_photo : 'man-avatar-profile-icon.jpg'))}"
                ></div>
              </div>
              <div class="name-container">
                <div class="name">{{user.name}}</div>
              </div>
              <div class="share-button-container">
                <button>Send File</button>
              </div>
            </div>
          </div>
        </div>

        <div class="copy-section">
          <div>Share this link with friends to access this document</div>
          <div class="copy-link-input-group">
            <span class="copy-text" id="copy-text">Abaaaaaoutcert.assff</span>
            <span class="copy-link-button-container">
              <button @click="copyToClipboard()">Copy Link</button>
            </span>
          </div>
        </div>

        <span class="invite-notification">Your invite link expires in 6 hours or after 20 uses</span>
        <br />
      </div>
      <div class="modal-footer">
        <div class="footer-child">
          <input type="checkbox" name id />
          <span>Set link to never expire</span>
        </div>
        <div class="footer-child">
          <span class="edit-link">Edit Link</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ShareDocumentDir from "~/components/ShareDocumentDir";

export default {
  name: "ShareDocumentModal",
  components: {
    ShareDocumentDir
  },
  props: {
    document_id: String
  },
  data() {
    return {
      searchData: {},
      shareDocumentHolder: { id: undefined },
      yellowUserId: undefined
    };
  },
  computed: {
    users() {
      return this.searchData.data ? this.searchData.data : [];
    }
  },
  methods: {
    backgroundPhoto(val) {
      return `url('${process.env.BACKEND_BASE_URL + val}')`;
    },
    closeModal() {
      this.$refs.modal.style.display = "none";
      this.$emit("closed");
    },
    getUserYellow(id) {
      this.yellowUserId = id;
    },
    resetYellowUser() {
      event.stopPropagation();
      this.yellowUserId = undefined;
    },
    copyToClipboard() {
      const el = document.createElement("textarea");
      el.value = document.getElementById("copy-text").textContent;
      document.body.appendChild(el);
      el.select();
      document.execCommand("copy");
      document.body.removeChild(el);

      alert("Copied!");
    },
    searchForUSer() {
      this.$refs.searchResultMessage.textContent = "";
      const searchString = this.$refs.searchInput.value;

      if (!searchString) return;

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

          this.searchData = temp;

          if (this.searchData.data.length == 0)
            this.$refs.searchResultMessage.textContent = "No Result Found";
        }
      });
    },
    shareDocument() {
      let sendDocumentButton = document.getElementById("sendDocumentButton");

      sendDocumentButton.setAttribute("disabled", "true");
      let data = {
        receiver_id: this.yellowUserId,
        document_id: this.document_id
      };

      if (!!this.shareDocumentHolder.id)
        Object.assign(data, { folder_id: this.shareDocumentHolder.id });

      let attempt = this.$store.dispatch("documents/shareDocument", data);

      attempt
        .then(response => {
          // console.log(response)
          // alert(response.message)
        })
        .finally(() => {
          sendDocumentButton.removeAttribute("diabled");
        });
    } //end function shareDocument
  }
};
</script>

<style scoped>
.share-document-modal {
  position: fixed;
  top: 0;
  bottom: 0;

  /* min-height: 100%; */
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 999;
  background: rgba(0, 0, 0, 0.5);
  padding: 1rem;
  overflow-y: auto;
}

.share-document-modal .modal {
  --modal-content-padding: 1rem;
  --modal-border-radius: 10px;
  --modal-width: 40vw;
  border-radius: var(--modal-border-radius);
  width: var(--modal-width);
  /* max-height: calc(100vh - 100px); */
  background: #f0f0f0;
  position: relative;
}

.share-document-modal .modal .modal-header {
  background: white;
  padding: var(--modal-content-padding);
  border-top-right-radius: var(--modal-border-radius);
  border-top-left-radius: var(--modal-border-radius);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  position: relative;
  height: 50px;
  padding-top: 0;
  padding-bottom: 0;
  display: flex;
  align-items: center;
}

.share-document-modal .modal .modal-header::before {
  content: "";
  position: absolute;
  --size: 4rem;
  height: var(--size);
  width: var(--size);
  background: url("/png/network-and-background.png");

  top: calc(calc(var(--size) / 2) * -1);
  left: calc(50% - calc(var(--size) / 2));
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
}

.share-document-modal .modal .modal-header .close-button {
  height: 100%;
  width: 2rem;
  cursor: pointer;

  display: flex;
  justify-content: flex-end;

  font-weight: bold;
  font-size: 1.5rem;

  padding: 0.5rem;
  transform: translate(0.5rem, 0);
}

.share-document-modal .modal .content {
  padding: var(--modal-content-padding);
}

.search-in-app .header {
  font-weight: bold;
  color: black;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.search-in-app .search-in-app-input-group {
  --search-height: 2rem;
  --search-padding: 0.2rem;
  height: var(--search-height);
  display: flex;
  align-items: center;
}
.search-in-app .search-in-app-input-group input,
.search-in-app .search-in-app-input-group .button-container {
  height: 100%;
  margin: 0;
  border: 1px solid #9b9b9b;
  border-radius: 5px;
}

.search-in-app .search-in-app-input-group input {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-right: 0;
  background: white;
  padding-left: 0.2rem;
  padding-right: 0.2rem;
  flex: 1;
}

.search-in-app .search-in-app-input-group .button-container {
  border-left: 0;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 2px;
}

.search-in-app .search-in-app-input-group button {
  width: calc(var(--search-height) - var(--search-padding) - 2px);
  height: calc(var(--search-height) - var(--search-padding) - 2px);
  border-radius: 3px;
  border: none;
  margin: 0;
  background-color: #0084ff;

  background-image: url("/png/search2.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  padding: 0.3rem;
  background-origin: content-box;
}

.search-result {
  --search-result-arrow-height: 1.5rem;
  margin-top: var(--search-result-arrow-height);
  border-radius: 5px;
  position: relative;
}

.search-result::before {
  content: "";
  border-top: 0;
  border-right: var(--search-result-arrow-height) solid transparent;
  border-left: var(--search-result-arrow-height) solid transparent;
  border-bottom: var(--search-result-arrow-height) solid white;
  position: absolute;
  left: 1rem;
  bottom: 100%;
}

.search-result .user-strip {
  --floating-folders-header-size: 30px;
  display: grid;
  grid-template-columns: 50px 1fr 80px;
  height: 50px;
  position: relative;
  margin-bottom: 0.5rem;
}

.search-result .user-strip .floating-folders {
  position: absolute;
  top: 0;
  left: calc(100% + var(--modal-content-padding));
  z-index: 2;
  background: #555286;
  width: calc((100vw - var(--modal-width)) / 2 - 2rem);
}

.search-result .user-strip .floating-folders::before {
  content: "";
  top: 0;
  right: 100%;

  position: absolute;

  border-top: solid transparent calc(var(--floating-folders-header-size) / 2);
  border-bottom: solid transparent calc(var(--floating-folders-header-size) / 2);
  border-right: solid #555286 var(--modal-content-padding);
  border-left: 0;
}

.search-result .user-strip .floating-folders .header {
  height: var(--floating-folders-header-size);
  background: white;
  color: #3a2c51;
  font-size: 0.7rem;
  display: flex;
  align-items: center;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
  white-space: nowrap;
}

.search-result .user-strip .floating-folders .header .close-modal {
  min-width: 1rem;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  font-size: 1.5rem;
  cursor: pointer;
}

.search-result .user-strip .floating-folders .footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 0.5rem;
}

.search-result .user-strip .floating-folders .footer .send-document-button {
  border: none;
  background: #27debf;
  border-radius: 3px;
  color: white;
}

.profile-photo-container {
  padding: 0.4rem;
}

.profile-photo-container .profile-photo {
  height: 100%;
  width: 100%;
  clip-path: circle();
  background-size: cover;
  background-repeat: no-repeat;
}

.search-result {
  background: white;
}

.name-container {
  display: flex;
  align-items: center;
  padding-left: 0.2rem;
  padding-left: 0.2rem;
  overflow: hidden;
}

.name-container .name {
  width: 100%;
  overflow: hidden;
  color: black;
  white-space: nowrap;
  text-overflow: ellipsis;
  font-size: 0.7rem;
  font-weight: bold;
}

.share-button-container {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding-right: 0.5rem;
}

.share-button-container button {
  font-size: 0.7rem;
  border: 1px solid #0084ff;
  color: #0084ff;
  background: white;
}

.copy-section {
  font-size: 0.8rem;
  margin-top: 1rem;
  margin-bottom: 1rem;
}

.copy-link-input-group {
  margin-top: 0.5rem;
  background: #f6f6f6;
  border: 1px solid #c4c4c4;
  border-radius: 3px;
  height: 30px;

  display: flex;
  align-items: center;
}

.copy-text {
  flex: 1;
  overflow: hidden;
  overflow-x: auto;
  color: black;
  padding-left: 0.5rem;
  padding-right: 0.5rem;

  -webkit-user-select: all; /* Chrome all / Safari all */
  -moz-user-select: all; /* Firefox all */
  -ms-user-select: all; /* IE 10+ */
  user-select: all; /* Likely future */
}

.copy-link-button-container {
  height: 100%;
  padding: 0.2rem;
}

.copy-link-button-container button {
  background: #0084ff;
  border-radius: 3px;
  border: none;
  color: white;
  height: 100%;
  font-size: 0.63rem;
}

.invite-notification {
  color: #7b018e;
  font-size: 0.63rem;
}

.modal-footer {
  height: 50px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: var(--modal-content-padding);
  font-size: 0.8rem;

  border-bottom-left-radius: var(--modal-border-radius);
  border-bottom-right-radius: var(--modal-border-radius);
}

.footer-child {
  display: flex;
  align-items: center;
}

.edit-link {
  color: #0084ff;
  cursor: pointer;
}

.search-result-message {
  margin-top: 1rem;
  font-size: 1.5rem;
  font-style: italic;
  color: #463737;
}

/* // Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
  .share-document-modal .modal {
    --modal-width: 50vw;
  }
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
  .share-document-modal .modal {
    --modal-width: 70vw;
  }

  .search-result .user-strip .floating-folders {
    top: calc(var(--modal-content-padding) + 100%);
    left: 5%;
    width: 60%;
  }

  .search-result .user-strip .floating-folders::before {
    top: calc(var(--modal-content-padding) * -1);
    right: calc(100% - 5rem);
    border-right: solid transparent
      calc(var(--floating-folders-header-size) / 2);
    border-left: solid transparent calc(var(--floating-folders-header-size) / 2);
    border-bottom: solid #555286 var(--modal-content-padding);
    border-top: 0;
  }
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
  .share-document-modal .modal {
    --modal-width: 75vw;
  }

  .search-result .user-strip .floating-folders {
    width: 90%;
  }
}

/* Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
  .share-document-modal .modal {
    --modal-width: 85vw;
  }
}
</style>
