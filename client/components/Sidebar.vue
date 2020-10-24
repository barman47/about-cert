<template>
  <div ref="sidebar" id="sidebar" :class="{'sidebar-expanded' : expanded}">
    <div id="ham-button" @click="expanded = !expanded">
      <img src="~/assets/png/ham button.png" alt />
    </div>
    <div id="settings-button" v-show="expanded">
      <img src="~/assets/png/settings.png" alt />
    </div>

    <ul id="sidebar-nav">
      <li
        :class="{active: $nuxt.$route.name && $nuxt.$route.name == 'updates'}"
        @click="goToLink('/updates')"
      >
        <img
          :src="'/png/dashboard'+ ($nuxt.$route.name && $nuxt.$route.name == 'updates' ? '-active':'')+'.png'"
          alt
        />
        <span v-show="expanded">Dashboard</span>
      </li>
      <li>
        <img
          :src="'/png/profile'+ ($nuxt.$route.name && $nuxt.$route.name.startsWith('profile') ? '-active':'')+'.png'"
          :class="{active: $nuxt.$route.name && $nuxt.$route.name.startsWith('profile')}"
          @click="goToLink('/profile/edit')"
        />
        <span v-show="expanded">Profile</span>
      </li>
      <li
        @click="goToLink('/documents')"
        :class="{active: $nuxt.$route.name && $nuxt.$route.name.startsWith('documents')}"
      >
        <img
          :src="'/png/doc'+($nuxt.$route.name && $nuxt.$route.name.startsWith('documents') ? '-active' : '')+'.png'"
          alt
        />
        <span v-show="expanded">Documents</span>
      </li>
      <li
        :class="{active: $nuxt.$route.name && $nuxt.$route.path == '/opportunities/manage'}"
        @click="goToLink('/opportunities/manage')"
      >
        <img
          :src="'/png/manage-opportunities'+ ( $nuxt.$route.name && $nuxt.$route.path == '/opportunities/manage' ? '-active': '') +'.png'"
          alt
        />
        <span v-show="expanded">Manage Opportunities</span>
      </li>
      <li @click.prevent="logout()">
        <img class="image" src="~/assets/png/logout 1.png" alt />
        <span v-show="expanded">Logout</span>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      expanded: false
    };
  },
  methods: {
    logout() {
      this.$store.dispatch("logout");
    },
    goToLink(path) {
      this.$nuxt.$router.push(path);
    }
  },
  computed: {}
};
</script>

<style scoped>
#sidebar {
  grid-area: sidebar;
  background: white;
  z-index: 100;

  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dark-theme #sidebar {
  background: #3f3d56;
}

#sidebar.sidebar-expanded {
  width: 200px;
  box-shadow: 2px 4px 4px rgba(0, 0, 0, 0.25);
}

#sidebar-nav {
  list-style: none;
  padding: 5px;
  max-height: 80%;
  overflow-y: auto;
}

#sidebar-nav li {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 1.7rem;
  color: #9b9b9b;
  cursor: pointer;
}

#sidebar-nav li.active {
  color: #0084ff;
}

#sidebar-nav li span {
  margin-left: 0.4rem;
  font-size: 0.8rem;
}

#sidebar.sidebar-expanded #sidebar-nav li {
  justify-content: flex-start;
}

#sidebar-nav li:first-child {
  margin-top: 0;
}

#sidebar-nav li img {
  transform: scale(0.8);
}

#ham-button {
  position: absolute;
  top: 0;
  right: 0;
  width: 50px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

#ham-button img {
  transform: scale(0.7);
}

#settings-button {
  position: absolute;
  top: 0;
  left: 0;
  width: 50px;
  height: 50px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

#settings-button img {
  transform: scale(0.7);
}

@media (max-width: 575.98px) {
  #sidebar {
    box-shadow: 1px -1px 2px 0px rgba(189, 188, 188, 0.43);
  }

  #ham-button {
    display: none;
  }

  #sidebar-nav {
    display: flex;
    width: 100%;
    justify-content: space-around;
    margin: 0;
    padding: 0;
  }

  #sidebar-nav li{
      margin: 0;
      /* border-right: 1px solid #a5a2a9; */
      flex: 1;
  }

  #sidebar-nav li:last-child{
      border-right: none;
  }

  #sidebar-nav li img{
      transform: scale(0.7);
  }
}
</style>