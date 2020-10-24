<template>
  <div id="authenticated-layout-content">
    <div id="profile-strip-container">
      <ProfileStripOthers />
    </div>
    <div id="profile-other-content">
      <profile-nav-others></profile-nav-others>
      <div id="profile-other-container">
        <h4>Under Construction</h4>
        <div class="under-contruction-container"></div>
      </div>
    </div>
  </div>
</template>

<script>
import ProfileNavOthers from "~/components/ProfileNavOthers";
import ProfileStripOthers from "~/components/ProfileStripOthers.vue";

export default {
  layout: "profile-others",
  components: {
    ProfileStripOthers,
    ProfileNavOthers
  },
  validate({ params }) {
    return !!params.id;
  },
  async fetch({ store, params, redirect, error }) {
    const id = params.id;

    if (!store.state.other_users.list.some(el => el.id == id)) {
      await store.dispatch("other_users/getUser", { id }).catch(err => {
        if (err.response && err.response.status == 404)
          error({ statusCode: 404, message: "User not found" });
      });
    }
  }
};
</script>


<style scoped>
#profile-other-container {
  display: flex;
  flex-direction: column;
}

#profile-other-container h4 {
  text-align: center;
  font-size: 1.5rem;
  font-style: italic;
  color: #646764;
}
.under-contruction-container {
  min-height: 20rem;
  background: url("/png/under_construction.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  flex: 1;
}

@media (max-width: 575.98px) {
  .under-contruction-container{
    min-height: 10rem;
  }
}
</style>