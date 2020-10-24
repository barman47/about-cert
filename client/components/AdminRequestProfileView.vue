<template>
  <div class="admin-request-profile-view">
    <div class="images" v-if="loading == false && !!user">
      <div
        class="cover-image"
        :style="{backgroundImage: 'url(\''+imageUrl(user.cover_image)+'\')'}"
      ></div>
      <div class="display-photo-container">
        <div
          class="display-photo"
          :style="{backgroundImage: 'url(\''+imageUrl(user.thumbnail)+'\')'}"
        ></div>
      </div>
    </div>
    <div class="content" v-if="loading == false && !!user">
      <div>
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
        </div>
        <div class="contact-details">
          <div class="contact-detail">
            <h5 class="header">Phone number</h5>
            <span class="actual">{{user.phone_number}}</span>
          </div>
          <div class="contact-detail email">
            <h5 class="header">Email Address</h5>
            <span class="actual">{{user.email}}</span>
          </div>
        </div>
      </div>
      <div>
        <div>
          <p class="description" v-if="user.about">{{user.about}}</p>
          <p class="description" style="font-style:italic" v-else>There is no description</p>
        </div>

        <div class="verify-button-container">
          <button>
            <span class="icon"></span>
            <span>Verify User</span>
          </button>
        </div>
      </div>
    </div>
    <div v-if="loading == true" class="loading">
      <div class="loader">
        <div class="layer-2">
          <div class="layer-3">
              <div class="layer-4"></div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="loading == false && !!!user">An errror occured</div>
  </div>
</template>

<script >
export default {
  props: {
    targetId: {
      required: true
    }
  },
  data() {
    return {
      loading: false,
      user: undefined
    };
  },
  computed: {},
  methods: {
    imageUrl(val) {
      return (
        process.env.BACKEND_BASE_URL.replace(/\/+$/, "") +
        "/" +
        (val || "man-avatar-profile-icon.jpg").replace(/^\/+/, "")
      );
    },
    fetchNewUserData(id) {
      this.loading = true;
      this.$axios
        .get("/api/admin/user-profile-data?user_id=" + id)
        .then(response => response.data)
        .then(response => {
          this.user = response.user;
        })
        .catch(err => console.log(err))
        .finally(() => {
          this.loading =  false
        });
    }
  },
  watch: {
    targetId: function(newId, oldId) {
      this.fetchNewUserData(newId);
    }
  },
  mounted() {
    this.fetchNewUserData(this.targetId);
  }
};
</script>

<style scoped>
.admin-request-profile-view {
  --border-raius: 10px;
  --images-container-height: 5rem;
  --cover-image-height: 100%;
  --profile-photo-size: calc(var(--images-container-height) * 1.3);
  height: 100%;
  width: 100%;

  background: white;
  border-radius: var(--border-raius);
  overflow: hidden;
  padding: 0;
  display: flex;
  flex-direction: column;
  color: black;
}

.images {
  height: var(--images-container-height);
  position: relative;
}

.cover-image {
  height: var(--cover-image-height);
  width: 100%;

  background-repeat: no-repeat;
  background-size: cover;
  background-position: center center;
  border-bottom-left-radius: var(--border-raius);
  border-bottom-right-radius: var(--border-raius);
}

.display-photo-container {
  position: absolute;
  max-height: calc(var(--cover-image-height) * 1.3);
  height: var(--profile-photo-size);
  width: var(--profile-photo-size);
  display: flex;
  align-items: center;
  justify-content: center;
  background-size: cover;
  bottom: calc(var(--profile-photo-size) / 2 * -1);
  left: calc(25% - var(--profile-photo-size) / 2);
  clip-path: circle();
  background: white;
  z-index: 1;
}

.display-photo {
  background-repeat: no-repeat;
  background-position: center center;
  background-size: cover;
  height: calc(100% - 7px);
  width: calc(100% - 7px);
  clip-path: circle();
}

.content {
  flex: 1;
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-gap: 0.7rem;
}

.content > div:first-child {
  padding: 1rem;
  padding-top: calc(var(--profile-photo-size) / 2 + 0.5rem);
  display: flex;
  flex-direction: column;
}

.contact-details {
  margin-top: auto;
  border-top: 0.8px solid #c6c5e2;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.contact-detail {
  margin-bottom: 0.5rem;
}

.contact-detail .header {
  margin-top: 0;
  margin-bottom: 0.2rem;
  color: #263238;
  font-size: 0.8rem;
}

.contact-detail:last-child {
  margin-bottom: 0;
}

.contact-detail .actual {
  color: #9b9b9b;
  font-size: 0.7rem;
}

.contact-detail.email .header {
  color: #263238;
}

.contact-detail.email .actual {
  color: #0084ff;
}

.name-followers-container {
  padding-left: 1rem;
  padding-right: 1rem;
  padding-bottom: 1rem;
}

.name {
  margin-top: 0;
  margin-bottom: 0;
  text-align: center;
  color: #263238;
  font-size: 1.1rem;
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
  font-size: 0.9rem;
  color: #0084ff;
}

.follow-main > span:last-child {
  font-size: 0.8rem;
  color: #9b9b9b;
}

.content > div:nth-child(2) {
  display: flex;
  flex-direction: column;
  padding: 1rem;
}

.description {
  color: #757575;
  font-size: 0.8rem;
}

.verify-button-container {
  margin-top: auto;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.verify-button-container button {
  color: white;
  background: #0084ff;
  border: none;
  padding: 0.5rem;
  padding-left: 0.8rem;
  padding-right: 0.8rem;
  display: inline-flex;
  align-items: center;
}

.verify-button-container button .icon {
  background: url("/png/verify-user-icon.png");
  background-position: center center;
  background-size: contain;
  background-repeat: no-repeat;

  width: 1rem;
  height: 1rem;
  margin-right: 0.5rem;

  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.loading {
  display: flex;
  height: 100%;
  width: 100%;
  justify-content: center;
  align-items: center;
}

.loading .loader {
  border: 0.8rem solid #f3f3f3;
  border-top-color: #3498db;
  border-right-color: #3498db;
  border-radius: 50%;
  width: 120px;
  height: 120px;
  animation: spin 2s linear infinite;

  padding: 5px;
}

.loading .loader * {
  height: 100%;
  width: 100%;
  border-radius: 50%;
}

.loading .loader .layer-2 {
  border: 0.5rem solid #f3f3f3;
  border-right-color: red;
  border-top-color: red;
  border-radius: 50%;
  animation: spin-opposite 1s linear infinite;

  padding: 5px;
}

.loading .loader .layer-3 {
  border: 0.3rem solid #f3f3f3;
  border-top-color: green;
  border-bottom-color: green;
  border-radius: 50%;
  animation: spin 600ms linear infinite;

  padding: 3px;
}

.loading .loader .layer-4 {
  border: 0.3rem solid #f3f3f3;
  border-top-color: rgba(85, 82, 134, 0.9);
  border-right-color: rgba(85, 82, 134, 0.9);
  
  border-radius: 50%;
  animation: spin-opposite 400ms linear infinite;

  padding: 3px;
}

@keyframes spin-opposite {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(-360deg);
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>