<template>
  <div id="default-layout" >
    <land-navbar />
    <div id="default-layout-main-content">
      <nuxt />
      <land-footer />
    </div>
  </div>
</template>

<script>
import LandNavbar from "~/components/LandNavbar.vue";
import LandFooter from "~/components/LandFooter.vue";

export default {
  components: {
    LandNavbar,
    LandFooter
  },
  middleware: "guest",
};
</script>

<style scoped>
    #default-layout{
      display: grid;
      grid-template-rows: auto 1fr;
      overflow: hidden;
      height: 100vh;
      height: var(--viewport-height, 100vh);
      width: 100vw;
    }

    #default-layout-main-content{
      height: 100%;
      overflow: hidden;
      overflow-y: auto;
      display: grid;
      grid-template-rows: 1fr auto;
    }
</style>
