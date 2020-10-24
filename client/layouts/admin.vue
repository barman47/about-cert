<template>
  <div id="admin-layout">
    <div id="admin-navbar-container">
      <admin-navbar />
    </div>
    <div id="admin-layout-content">
        <nuxt />
    </div>
  </div>

</template>

<script>
import AdminNavbar from "~/components/AdminNavbar.vue";
export default {
  middleware: "admin",
  components: {
    AdminNavbar
  }
}
</script>

<style scoped>
  #admin-layout{
    display: grid;
    grid-template-rows: 50px 1fr;
    height: 100vh;
    height: var(--viewport-height, 100vh);
    overflow: hidden;
  }

  #admin-navbar-container{
    margin: 0;
  }

  #admin-layout-content{
    overflow: hidden;
    overflow-y: auto;

    background: #1F0051;
    color: white;
    font-size: 0.8rem;
  }
</style>
