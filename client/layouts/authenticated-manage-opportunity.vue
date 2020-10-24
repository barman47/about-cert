<template>
  <div id="authenticated-layout" :class="{'dark-theme': theme == 'dark'}">
    <navbar></navbar>
    <div class="authenticated-home-navigation">
      <home-navigation>
        <li :class="{active: $nuxt.$route.path == '/opportunities/manage'}">Manage Opportunities</li>
      </home-navigation>
    </div>
    <sidebar></sidebar>
    <div id="authenticated-layout-content">
      <nuxt />
    </div>
  </div>
</template>

<script>
import Navbar from "~/components/Navbar.vue";
import Sidebar from "~/components/Sidebar.vue";
import HomeNavigation from "~/components/HomeNavigation";

export default {
  components: {
    Navbar,
    Sidebar,
    HomeNavigation
  },
  middleware: "auth",
  computed: {
    theme() {
      return this.$store.state.theme;
    }
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
  /* top: 0;
  right: 0;
  left: 0;
  bottom: 0; */
}

#authenticated-layout.dark-theme {
  background: #3f3d56;
}

#authenticated-layout-content {
  grid-area: authenticated-layout-content;
  border-top-left-radius: 10px;
  /* background:#e5e5e5; */
  background: #eeeeff;
  padding: 1.5rem;
  padding-left: 2rem;
  padding-right: 2rem;
  height: 100%;
  display: grid;
  grid-template-columns: 1fr;
  overflow-y: auto;
  overflow-x: hidden;
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

.authenticated-home-navigation {
  grid-area: home-nav;
  display: none;
}

@media (max-width: 991.98px) {
  #authenticated-layout-content {
    padding: 1rem;
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

  .authenticated-home-navigation {
    display: block;
  }

  * >>> .home-navigation-desktop {
    display: none;
  }
}

@media (max-width: 575.98px) {
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
