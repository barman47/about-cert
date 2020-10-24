<template>
  <div id="create-cvs">
    <div class="template-groups" v-if="templateGroups && templateGroups.length > 0">
      <template-group
        ref="templateGroups"
        groupIntent="saved"
        @inView="templateGroupInViewEvent(templateGroup.id)"
        v-for="templateGroup in templateGroups"
        :templateGroup="templateGroup"
        :key="templateGroup.id"
      ></template-group>
    </div>
    <div v-else class="no-cv-template-saved">You have not saved any CV Template</div>
  </div>
</template>

<script>
import ProfileNav from "~/components/ProfileNav";
import TemplateGroup from "~/components/TemplateGroup";

export default {
  components: {
    ProfileNav,
    TemplateGroup
  },
  layout: "profile",
  async fetch({ store, $axios }) {
    if (!store.state.profile.fetched)
      await $axios.get("/api/profile").then(response => {
        store.commit("profile/fetch", response.data);
      });

    if (!store.state.cv.templatesFetched)
      await $axios.get("/api/cv/get-saved-templates").then(response => {
        store.commit("cv/addSavedTemplateGroups", response.data);
      });
  },
  data() {
    return {};
  },
  computed: {
    templateGroups() {
      return this.$store.state.cv.savedTemplateGroups;
    }
  },
  methods: {
    templateGroupInViewEvent(inViewId) {
      for (let templateGroup of this.$refs.templateGroups)
        templateGroup.resetView(inViewId);
    }
  }
};
</script>

<style scoped>
#create-cvs {
  padding: 1rem;
}

.cv-button-group {
  margin-top: 1rem;
  margin-bottom: 1rem;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: flex-end;
}

.cv-button {
  background: #263238;
  color: white;
  height: 27px;
  font-size: 0.75rem;
  border: none;
  margin-left: 0.5rem;
}

.cv-button.generate-cv {
  background: #0084ff;
}

.cv-button .icon {
  height: 0.8rem;
  width: 0.8rem;
  background-position: center center;
  background-repeat: no-repeat;
  background-size: contain;
  display: inline-block;
}

.template-groups {
  display: grid;
  grid-template-columns: 1fr;
  row-gap: 1.5rem;
}

.no-cv-template-saved {
  color: #9b9b9b;
  font-style: italic;
}

/* border: 3px dashed #FFFFFF;
        border-radius: 5px;
        padding: 0.2rem;
        background-origin: content-box;
    } */

@media (max-width: 575.98px) {
  #create-cvs {
    padding: 0;
  }
}
</style>
