<template>
  <div id="authenticated-layout-content">
    <div id="profile-strip-container">
      <ProfileStripOthers />
    </div>
    <div id="profile-other-content">
      <profile-nav-others></profile-nav-others>
      <div id="profile-other-container">
        <div class="events-cards-container">
          <updates-card v-for="event in events" :key="event.id" :update="event" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProfileNavOthers from "~/components/ProfileNavOthers";
import ProfileStripOthers from "~/components/ProfileStripOthers.vue";
import UpdatesCard from "~/components/UpdatesCard";

export default {
  layout: "profile-others",
  components: {
    ProfileStripOthers,
    ProfileNavOthers,
    UpdatesCard
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

    if (
      !store.state.other_users.list.find(el => el.id == id)
        .events_pagination_data
    ) {
      await store
        .dispatch("other_users/getEvents", { id })
        .catch(err => console.log(error));
    }
  },
  computed: {
    events() {
      return this.$store.state.other_users.list.find(
        el => el.id == this.$nuxt.$route.params.id
      ).events_pagination_data.data;
    }
  }
};
</script>

<style scoped>
#profile-other-container{
  background:#e3e3e3;
}

.events-cards-container{
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 2rem;
}


/* // Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
  .events-cards-container {
    grid-template-columns: 1fr;
  }
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
}
</style>