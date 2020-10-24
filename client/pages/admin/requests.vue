<template>
  <div class="main-content">
    <div class="requests-container">
      <AdminRequests @singleRequestButtonClicked="singleRequestButtonClicked($event)" />
    </div>
    <div class="request-view-container">
      <AdminRequestView :currentlyInview="currentlyInview" />
    </div>
  </div>
</template>

<script>
import AdminRequests from "~/components/AdminRequests.vue";
import AdminRequestView from "~/components/AdminRequestView.vue";

export default {
  layout: "admin",
  components: {
    AdminRequests,
    AdminRequestView
  },
  fetch({ store }) {
    return new Promise((resolve, reject) => {
      store.dispatch("admin/fetchAllRequests").finally(() => resolve());
    });
  },
  data() {
    return {
      currentlyInview: {
        type: undefined,
        id: undefined
      }
    };
  },
  methods: {
    singleRequestButtonClicked(val) {
      this.currentlyInview = val;
    }
  }
};
</script>

<style scoped>
.main-content {
  padding: 1rem;
  display: grid;
  grid-template-columns: minmax(300px, 2fr) 5fr;
  height: 100%;
  width: 100%;
  grid-gap: 1rem;
  max-height: 100%;
  max-width: 100%;
  overflow: hidden;
}

.requests-container {
  height: 100%;
  width: 100%;
  max-height: 100%;
  max-width: 100%;
  overflow: hidden;
}

.request-view-container {
  height: 100%;
  overflow: hidden;
  overflow-y: auto;
}
</style>