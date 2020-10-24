<template>
  <div class="admin-request-view-main-content">
      <div class="view-container">
        <AdminRequestProfileView :targetId="currentlyInview.id" v-if="currentlyInview.type == 'user-profile'" />
        <AdminRequestOpportunityView :targetId="currentlyInview.id" v-if="currentlyInview.type == 'report-opportunity'" />
      </div>
      <div class="events-container">

      </div>
  </div>
</template>

<script>
  import AdminRequestProfileView from "~/components/AdminRequestProfileView.vue"
  import AdminRequestOpportunityView from "~/components/AdminRequestOpportunityView.vue"

  export default {
    props: {
      currentlyInview: {}
    },
    components: {
      AdminRequestProfileView,
      AdminRequestOpportunityView
    }
  }
</script>

<style scoped>
  .admin-request-view-main-content{
    display: grid;
    grid-template-rows: 3fr 2fr;
    height: 100%;
    max-height: 100%;
    grid-gap: 1rem;
  }

  .events-container{
    background: #0083F9;
    border-radius: 10px;
    overflow: hidden;
  }
</style>