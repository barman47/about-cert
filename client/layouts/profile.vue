<template>
  <div id="authenticated-layout" :class="{'dark-theme': theme == 'dark'}">
    <navbar></navbar>
    <div class="authenticated-home-navigation">
      <home-navigation>
        <li @click="goToLink('/profile/edit')" :class="{active: $nuxt.$route.path == '/profile/edit'}">Edit Profile</li>
        <li @click="goToLink('/profile/cv/create')" :class="{active: $nuxt.$route.path == '/profile/cv/create'}">Create CV</li>
        <li @click="goToLink('/profile/cv/saved')" :class="{active: $nuxt.$route.path == '/profile/cv/saved'}">Saved CVs</li>
        <li @click="goToLink('/profile/cv/job-kit')" :class="{active: $nuxt.$route.path == '/profile/cv/job-kit'}">Job Kit</li>
      </home-navigation>
    </div>
    <sidebar></sidebar>
    <div id="authenticated-layout-content">
      <div id="profile-strip-container" v-if="$nuxt.$route.path == '/profile/edit' || innerWidth > 767.98">
        <profile-strip></profile-strip>
      </div>
      <div id="profile-other-content">
        <div id="profile-profile-nav">
          <profile-nav></profile-nav>
        </div>
        <div id="nuxt-container">
          <nuxt />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProfileNav from "~/components/ProfileNav";
import Navbar from "~/components/Navbar.vue";
import Sidebar from "~/components/Sidebar.vue";
import ProfileStrip from "~/components/ProfileStrip.vue";
import HomeNavigation from "~/components/HomeNavigation";

export default {
  components: {
    Navbar,
    Sidebar,
    ProfileStrip,
    ProfileNav,
    HomeNavigation
  },
  middleware: "auth",
  data(){
    return {
      innerWidth: 0
    }
  },
  computed: {
    theme() {
      return this.$store.state.theme;
    },
  },
  methods: {
    goToLink(path) {
      if (path == this.$nuxt.$route.path) return;
      this.$nuxt.$router.push(path);
    },
    updateWindowWidth(){
      this.innerWidth = window.innerWidth
    }
  },
  mounted() {
    this.updateWindowWidth()
    window.addEventListener("resize", () => {
      this.updateWindowWidth()
    })
  }
};
</script>

<style scoped>
#authenticated-layout {
  display: grid;
  grid-template:
    "sidebar navbar" 50px
    "sidebar authenticated-layout-content" 1fr
    / 50px 1fr;
  height: 100vh;
  height: var(--viewport-height, 100vh);
  position: relative;
}

#authenticated-layout.dark-theme {
  background: #3f3d56;
}

#authenticated-layout-content {
  grid-area: authenticated-layout-content;
  border-top-left-radius: 10px;
  /* background:#e5e5e5; */
  /* background: #eeeeff; */
  background: #f0f0f0;
  padding: 1.5rem;
  padding-left: 2rem;
  padding-right: 2rem;
  height: 100%;
  overflow: hidden;
  display: grid;
  grid-template-columns: 1fr 2fr;
  grid-gap: 2rem;
}

#authenticated-layout-content::-webkit-scrollbar {
  display: none;
}

#authenticated-layout-content::-webkit-scrollbar-track {
  display: none;
}

#authenticated-layout-content::-webkit-scrollbar-thumb {
  display: none;
}

.dark-theme #authenticated-layout-content {
  background: #3a2c51;
}

#profile-strip-container {
  height: 100%;
  overflow-y: auto;
}

#profile-other-content {
  height: 100%;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}
#nuxt-container {
  margin-top: 1rem;
  flex: 1;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 10px;
  /* padding: 1rem; */
  overflow: hidden;
  overflow-y: auto;
}

.authenticated-home-navigation {
  grid-area: home-nav;
  /* box-shadow: -1px 1px 2px 0px rgba(189, 188, 188, 0.43); */
  display: none;
}

@media (max-width: 991.98px) {
  #authenticated-layout-content {
    padding: 1rem;
    grid-gap: 1rem;
    grid-template-columns: 2fr 3fr;
  }
}

@media (max-width: 767.98px) {
  #authenticated-layout {
    grid-template:
      "sidebar navbar" 50px
      "sidebar home-nav" 40px
      "sidebar authenticated-layout-content" 1fr
      / 50px 1fr;
  }
  #authenticated-layout-content {
    display: flex;
    height: unset;
    flex-direction: column;
    /* overflow: unset; */
    height: 100%;
    overflow-y: auto;
  }
  #profile-strip-container {
    height: auto;
    overflow: unset;
    padding-bottom: 3rem;
  }
  #profile-other-content {
    height: auto;
    overflow: unset;
  }

  #profile-profile-nav {
    display: none;
  }

  .authenticated-home-navigation {
    display: block;
  }
}

@media (max-width: 575.98px) {
  #nuxt-container {
    background: transparent;
  }
  #authenticated-layout {
    grid-template:
      "navbar navbar" 50px
      "home-nav home-nav" 40px
      "authenticated-layout-content authenticated-layout-content" 1fr
      "sidebar sidebar" 50px;
    overflow: hidden;
  }

  #authenticated-layout-content {
    border-top-left-radius: 0;
  }
}
</style>
