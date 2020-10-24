<template>
  <div id="authenticated-layout" :class="{'dark-theme': theme == 'dark'}">
    <navbar></navbar>
    <sidebar></sidebar>
    <nuxt />
  </div>
</template>

<script>
  import ProfileNav from "~/components/ProfileNav";
  import Navbar from "~/components/Navbar.vue";
  import Sidebar from "~/components/Sidebar.vue";
  import ProfileStripOthers from "~/components/ProfileStripOthers.vue";

  export default {
    components: {
      Navbar,
      Sidebar,
      ProfileStripOthers,
      ProfileNav
    },
    middleware: "auth",
    computed: {
      theme(){
        return this.$store.state.theme
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
}

#authenticated-layout.dark-theme{
  background: #3F3D56;
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

.dark-theme #authenticated-layout-content{
  background: #3A2C51;
}

* >>> #profile-strip-container {
  height: 100%;
  overflow: hidden;
  overflow-y: auto;
}

* >>> #profile-other-content {
  height: 100%;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

* >>> #profile-other-container {
  background: white;
  flex: 1;
  margin-top: 0.5rem;
  border-radius: 10px;
  padding: 1rem;
  overflow-y: auto;
}

@media (max-width: 991.98px) {
  #authenticated-layout-content {
    padding: 1rem;
    grid-gap: 1rem;
    grid-template-columns: 2fr 3fr;
  }
}

@media (max-width: 767.98px) {
  #authenticated-layout-content {
    display: flex;
    height: unset;
    flex-direction: column;
    /* overflow: unset; */
    height: 100%;
    overflow-y: auto;
  }
  * >>> #profile-strip-container {
    height: auto;
    overflow: unset;
    padding-bottom: 3rem;
  }
  * >>> #profile-other-content {
    height: auto;
    overflow: unset;
  }
}

@media (max-width: 575.98px) {
  #authenticated-layout {
    grid-template:
      "navbar navbar" 50px
      "authenticated-layout-content authenticated-layout-content" 1fr
      "sidebar sidebar" 50px;
    overflow: hidden;
  }

  #authenticated-layout-content{
    border-top-left-radius: 0;
  }
}
</style>
