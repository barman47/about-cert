<template>
  <div class>
    <div class="home-navigation-desktop">
      <home-navigation>
        <li :class="{active: $nuxt.$route.path == '/opportunities/manage'}">Manage Opportunities</li>
      </home-navigation>
    </div>

    <div class="opportunities-card-container">
      <manage-opportunities-card
        :opportunity="opportunity"
        v-for="opportunity in opportunities"
        :key="opportunity.id"
      ></manage-opportunities-card>
    </div>
    <div id="floating-buttons">
      <floating-button image="/png/share 2.png">Post Opportunities</floating-button>
    </div>
  </div>
</template>

<script>
import HomeNavigation from "~/components/HomeNavigation";
import ManageOpportunitiesCard from "~/components/ManageOpportunitiesCard";
import FloatingButton from "~/components/FloatingButton";
export default {
  scrollToTop: true,
  components: {
    HomeNavigation,
    ManageOpportunitiesCard,
    FloatingButton
  },
  layout: "authenticated-manage-opportunity",
  computed: {
    opportunities() {
      return this.$store.getters["opportunities/ownOpportunities"];
    }
  }
};
</script>

<style scoped>
#floating-buttons {
  position: fixed;
  right: 3rem;
  bottom: 2rem;
  display: grid;
  grid-template-columns: 1fr;
  row-gap: 10px;
  z-index: 100;
}
.opportunities-card-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-column-gap: 3rem;
  grid-row-gap: 5rem;
  margin-top: 1.5rem;
}

@media (max-width: 991.98px) {
  .opportunities-card-container {
    grid-template-columns: 1fr 1fr;
    grid-row-gap: 3rem;
  }
}
@media (max-width: 767.98px) {
  .opportunities-card-container {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 575.98px) {
  #floating-buttons {
    bottom: calc(2rem + 50px);
    right: 1rem;
  }
}
</style>