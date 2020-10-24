<template>
  <div class="admin-request-single-request-main-content">
    <div class="profile-photo">
      <img :src="profilePhoto" />
    </div>
    <div class="user-content">
      <div class="specifics">
        <span class="name">{{request.user.name}}</span>&nbsp;
        <span>{{request.data.text}}</span>
      </div>
      <div class="actions">
        <button
          class="request-button post"
          v-if="showActionButton"
          :class="{active: viewed}"
          @click="actionButtonClicked()"
        >{{actionButtonText}}</button>
        <button class="request-button profile" @click="viewProfile()">View Profile</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    request: {}
  },
  data() {
    return {
      actionButtonTextMapper: [
        {
          type: "report-post",
          buttonText: "View Post"
        },
        {
          type: "report-opportunity",
          buttonText: "View Opportunity"
        }
      ]
    };
  },
  computed: {
    profilePhoto() {
      return (
        process.env.BACKEND_BASE_URL.replace(/\/+$/, "") +
        "/" +
        (this.request.user.thumbnail || "/man-avatar-profile-icon.jpg").replace(
          /^\/+/,
          ""
        )
      );
    },
    viewed() {
      if (Math.floor(Math.random() * 10) == 0) return true;
      return false;
    },
    actionButtonText() {
      return this.actionButtonTextMapper.find(el => el.type == this.request.type).buttonText
    },
    showActionButton() {
      return this.actionButtonTextMapper.some(el => el.type == this.request.type);
    }
  },
  methods: {
    viewProfile() {
      this.$emit("singleRequestButtonClicked", {
        type: "user-profile",
        id: this.request.user.id
      });
    },
    actionButtonClicked() {
      const requestType = this.request.type;
      const type = this.actionButtonTextMapper.some(el => el.type == requestType)
        ? requestType
        : undefined;

      this.$emit("singleRequestButtonClicked", {
        type: type,
        id: this.request.user.id
      });
    }
  }
};
</script>

<style scoped>
.admin-request-single-request-main-content {
  display: grid;
  grid-template-columns: 50px 1fr;
  margin-bottom: 5px;
  padding: 0.5rem;
  padding-left: 0.1rem;
}

.admin-request-single-request-main-content .user-content {
  color: #2f2e41;
  font-size: 0.75rem;
}

.admin-request-single-request-main-content .user-content .name {
  font-weight: bold;
}

.admin-request-single-request-main-content .profile-photo {
  display: flex;
  align-items: flex-start;
  justify-content: center;
}

.admin-request-single-request-main-content .profile-photo img {
  object-fit: contain;
  max-height: 95%;
  max-width: 95%;
  clip-path: circle();
}

.admin-request-single-request-main-content .actions {
  margin-top: 0.5rem;
}

.request-button {
  border: none;
  background: #f2f2f2;
  border-radius: 3px;
  color: #9b9b9b;
  padding: 0.8rem;
  padding-top: 0.3rem;
  padding-bottom: 0.3rem;
  margin-right: 0.5rem;
  font-size: 0.7rem;
  font-style: normal;
  font-weight: normal;
}

.request-button:last-child {
  margin-left: 0;
}

.request-button.profile {
  background: #c9e5ff;
  color: #0084ff;
}

.request-button.post.active {
  background: #c4ffba;
  color: #0d5002;
}
</style>