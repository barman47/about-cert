<template>
  <div id="navbar">
    <div id="home-button" @click="$nuxt.$router.push('/')">
      <img src="~/assets/png/home.png" alt />
    </div>
    <div class="navbar-action-group">
      <div id="notification-bell" :class="{'notification-shown': showNotifications}">
        <img
          v-if="hasUnreadAlerts"
          src="~assets/png/notification-bell-active.png"
          ref="showcasePaneButton"
          @click="showNotificationsPane()"
        />
        <img
          v-else
          src="~assets/png/notification-inactive.png"
          ref="showcasePaneButton"
          @click="showNotificationsPane()"
        />
        <div
          id="notification-showcase-pane"
          ref="notificationShowcasePane"
          v-if="showNotifications"
        >
          <SingleAlert
            @hideNotificationsPane="hideNotificationsPane()"
            :alert="alert"
            v-for="alert in alerts"
            :key="alert.id"
          ></SingleAlert>
          <div
            v-if="alerts.length == 0"
            class="notification-item-empty"
          >Your notification container is empty</div>
        </div>
      </div>
      <div id="theme-change-button" @click="toggleTheme()">
        <img src="~assets/png/Theme.png" alt v-show="theme == 'light'" />
        <img src="~assets/png/Theme-dark.png" alt v-show="theme == 'dark'" />
      </div>
    </div>
  </div>
</template>

<script>
import SingleAlert from "~/components/SingleAlert.vue";

export default {
  components: {
    SingleAlert,
  },
  data() {
    return {
      showNotifications: false
    };
  },
  computed: {
    theme() {
      return this.$store.state.theme;
    },
    alerts() {
      return this.$store.state.alerts.list;
    },
    hasUnreadAlerts() {
      return this.$store.getters["alerts/hasUnread"];
    }
  },
  methods: {
    asTest() {
      debugger;
    },
    toggleTheme() {
      if (this.theme == "light") this.$store.commit("setTheme", "dark");
      else this.$store.commit("setTheme", "light");
    },
    showNotificationsPane() {
      if (this.alerts.length == 0) this.$store.dispatch("alerts/getInitAlerts");
      this.showNotifications = true;
    },
    hideNotificationsPane() {
      this.showNotifications = false;
      this.$store.dispatch("alerts/notificationsPaneViewed");
    },
    documentMonitor(e) {
      const path = e.path || (e.composedPath ? e.composedPath() : []);

      if (path.some(el => el == this.$refs.showcasePaneButton)) {
        if (this.showNotifications == false) this.showNotifications = true;
        // console.log(1)
      } else if (path.some(el => el == this.$refs.notificationShowcasePane)) {
        // console.log(2)
      } else {
        this.hideNotificationsPane();
      }
    }
  },
  mounted() {
    document.addEventListener("click", this.documentMonitor);
  }
};
</script>

<style scoped>
#navbar {
  grid-area: navbar;
  background: white;
  z-index: 99;
  padding: 3px;
  position: relative;

  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}
.dark-theme #navbar {
  background: #3f3d56;
}

#navbar > * {
  height: 100%;
}

#navbar .navbar-action-group {
  display: inline-flex;
  align-items: center;
  flex-direction: row;
}

#home-button {
  display: flex;
  align-items: center;
}

#home-button img {
  cursor: pointer;
  object-fit: contain;
  transform: scale(0.7);
}

#theme-change-button {
  height: 100%;
  display: flex;
  align-items: center;
}

#theme-change-button img {
  transform: scale(0.7);
  cursor: pointer;
}

#notification-bell {
  cursor: pointer;
  padding: 0.7rem;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  position: relative;
}

#notification-bell img {
  object-fit: contain;
  max-width: 100%;
  max-height: 100%;
}

#notification-bell.notification-shown::before {
  content: "";
  position: absolute;
  height: 0;
  width: 0;

  top: calc(100% + 2px);
  right: calc(50% - 0.5rem);
  border-right: 0.5rem transparent solid;
  border-left: 0.5rem transparent solid;
  border-top: none;
  border-bottom: 1.2rem #c4c4c4 solid;

  cursor: initial;
}

#notification-showcase-pane {
  position: absolute;
  background: white;
  right: -1rem;
  top: calc(100% + 1rem);

  padding: 0.2rem;

  padding-top: 0;
  padding-bottom: 0;
  border-radius: 10px;

  --showcase-pane-width: 25rem;

  width: var(--showcase-pane-width);

  border: 1px #c4c4c4 solid;

  max-height: 25rem;
  overflow-y: auto;
  cursor: initial;
}

#notification-showcase-pane .notification-item-empty {
  padding: 1rem;
  font-style: italic;
  color: #827672;
  font-size: 0.9rem;
}

@media (max-width: 575.98px) {
  /* #navbar {
    box-shadow: -1px 1px 2px 0px rgba(189, 188, 188, 0.43);
  } */

  #notification-showcase-pane {
    position: fixed;
    --showcase-pane-width: 90vw;

    top: calc(50px + 1rem);
    right: unset;
    left: calc(calc(100vw - var(--showcase-pane-width)) / 2);
    bottom: unset;
    width: var(--showcase-pane-width);
    max-height: 70vh;
    box-shadow: 3px 2px 7px 0px #a0a0a087;
  }
}
</style>