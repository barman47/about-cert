<template>
  <div class="notification-item">
    <div class="profile-photo-container">
      <div
        class="profile-photo"
        :style="{backgroundImage: 'url(\''+ getAlertSenderProfilePhoto +'\')'}"
      ></div>
    </div>
    <div class="content">
      <div class="text" ref="textContent"></div>
      <div class="date">{{alert.created_at_calendar}}</div>
    </div>
    <div class="notification-icon-container">
      <div
        v-if="alert.viewed == 0"
        class="notification-icon"
        style="background-image: url('/png/notification-active.png')"
      ></div>
      <div
        v-else
        class="notification-icon"
        style="background-image: url('/png/notification-inactive.png')"
      ></div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    alert: {
      required: true,
      type: Object
    }
  },
  data() {
    return {
      classMapper: {
        link: {
          root: "alert-link",
          document: {
            eventListeners: {
              click: this.documentClickEvent
            },
            classes: ["document"]
          },
          user: {
            eventListeners: {
              click: this.userClickEvent
            },
            classes: ["user"]
          },
          folder: {
            eventListeners: {
              click: this.folderClickEvent
            },
            classes: ["document"]
          }
        },
        button: {
          eventListeners: {},
          classes: ["alert-button"]
        },
        text: {
          eventListeners: {},
          classes: ["alert-text"]
        }
      }
    };
  },
  computed: {
    getAlertSenderProfilePhoto() {
      let alert = this.alert;
      if (!alert.sender) return "";

      if (!alert.sender.thumbnail) return "";

      return (
        process.env.BACKEND_BASE_URL.replace(/\/+$/, "") +
        "/" +
        alert.sender.thumbnail.replace(/^\/+/, "")
      );
    }
  },
  mounted() {
    this.assembleAlertText();
  },
  methods: {
    assembleAlertText() {
      let parent = this.$refs.textContent;
      const alert = this.alert;
      const classMapper = this.classMapper;

      for (let item of alert.data) {
        let child = document.createElement("span");
        child.appendChild(document.createTextNode(item.text));

        //Add necessary classes from the class mapper type
        if (item.type == "link") {
          if (!child.classList.contains(classMapper.link.root))
            child.classList.add(classMapper.link.root);
        } else {
          for (let typeClass of (classMapper[item.type] || {}).classes || []) {
            if (!child.classList.contains(typeClass))
              child.classList.add(typeClass || "");
          }
        }

        //Add classes for the alert items with link_to property
        if (item.link_to) {
          let linkToMapper = classMapper.link[item.link_to] || {};

          if (linkToMapper.eventListeners) {
            child.setAttribute("target", item.id);

            for (let eventType in linkToMapper.eventListeners) {
              child.addEventListener(
                eventType,
                linkToMapper.eventListeners[eventType]
              );
            }
          }

          for (let typeClass of linkToMapper.classes || []) {
            if (!child.classList.contains(typeClass))
              child.classList.add(typeClass || "");
          }
        }

        parent.appendChild(child);
      }
    },
    userClickEvent() {
      const userId = event.target.getAttribute("target");
      this.$nuxt.$router.push("/users/" + userId);
      this.$emit("hideNotificationsPane")
    },
    documentClickEvent() {
      const documentId = event.target.getAttribute("target");
      this.$nuxt.$router.push("/documents/view/" + documentId);
      this.$emit("hideNotificationsPane")
    },
    folderClickEvent() {
      const folderId = event.target.getAttribute("target");

      this.$emit("hideNotificationsPane")
      if (folderId == "root") {
        this.$nuxt.$router.push("/documents");
      } else {
        this.$nuxt.$router.push("/documents/" + folderId);
      }
    }
  }
};
</script>

<style scoped>
.notification-item {
  border-bottom: 0.5px solid #d8d8d8;
  display: grid;
  grid-template-columns: 3rem 1fr 2rem;
  min-height: 3.4rem;
  overflow: hidden;
  font-size: 0.8rem;

  padding: 0.2rem;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-item .profile-photo-container {
  /* background: red; */
  display: flex;

  align-items: center;
  justify-content: center;
  padding: 0.2rem;
}

.notification-item .profile-photo-container .profile-photo {
  height: 100%;
  width: 100%;

  background-size: cover;
  background-repeat: no-repeat;
  background-position: center center;
  clip-path: circle();

  /* background-image: url("~assets/png/undraw_about_us_page_ee1k1.png"); */
}

.notification-item .notification-icon-container {
  padding: 0.5rem;
}

.notification-item .notification-icon {
  height: 100%;
  width: 100%;
  background-position: center center;
  background-size: contain;
  background-repeat: no-repeat;
  background-origin: content-box;
}

.content {
  display: grid;
  grid-template-columns: 1fr;

  grid-template-rows: 1fr 1rem;

  padding: 0.2rem;
  padding-right: 0.4rem;
  padding-left: 0.4rem;

  font-size: 0.75rem;
  line-height: 1rem;
  color: black;
}

.content .date {
  font-size: 0.6rem;
  color: #9b9b9b;
  margin-top: 0.4rem;
  margin-bottom: 0.2rem;
}

.notification-item .content .text >>> .alert-link {
  cursor: pointer;
  color: #0084ff;
}

.notification-item .content .text >>> .alert-link.user {
  font-weight: 600;
}

.notification-item .content .text >>> .alert-link.document {
  font-weight: 500;
  color: #0084ff;
}

.notification-item .content .text >>> .alert-button {
  color: #0084ff;
  padding: 0.1rem;
  padding-left: 0.4rem;
  padding-right: 0.4rem;
  margin-left: 0.2rem;
  margin-right: 0.2rem;
  background: #d5efff;
  border-radius: 3px;
  cursor: pointer;
  font-size: smaller;
  white-space: nowrap;
}

</style>