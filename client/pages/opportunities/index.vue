<template>
  <div class style="margin-bottom: 30px">
    <div class="home-navigation-desktop">
      <home-navigation />
    </div>
    <div class="category-selector-button">
      <span>Category</span> &nbsp; &nbsp;
      <img src="/png/Group 125.png" alt />
    </div>

    <div class="opportunities-card-container">
      <opportunities-card
        :opportunity="opportunity"
        v-for="opportunity in opportunities"
        :key="opportunity.id"
      />
    </div>
    <div id="floating-buttons">
      <floating-button
        image="/png/share 2.png"
        @clicked="$refs.modal.openModal()"
      >Post Opportunities</floating-button>
    </div>

    <modal ref="modal" id="opportunity-modal" size="sm" @submitted="createOpportunity()">
      <template v-slot:header>Post Opportunities</template>
      <div>
        <div class="form-group">
          <label for="title">Title</label>
          <input ref="titleInput" required class="form-input" type="text" id="title" />
        </div>
        <div class="form-group">
          <label for="description">Description</label>
          <textarea
            ref="descriptionInput"
            required
            class="form-input"
            name="description"
            id="description"
            rows="10"
          ></textarea>
        </div>
        <div class="form-group">
          <label for="link">Link</label>
          <input ref="linkInput" required class="form-input" type="text" id="link" />
        </div>
        <!-- Add content -->
      </div>
      <template v-slot:footer>
        <div class="footer-button-container">
          <button type="submit" class="footer-button">Post Opportunity</button>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import HomeNavigation from "~/components/HomeNavigation";
import FloatingButton from "~/components/FloatingButton";
import Modal from "~/components/Modal";
import OpportunitiesCard from "~/components/OpportunitiesCard";
import InputLabel from "~/components/InputLabel";

export default {
  scrollToTop: true,
  components: {
    HomeNavigation,
    FloatingButton,
    Modal,
    OpportunitiesCard,
    InputLabel
  },
  layout: "authenticated",
  async fetch({ store, $axios }) {
    if (store.state.opportunities.list.length <= 0) {
      await $axios.get("/api/opportunities/get-all").then(response => {
        store.commit("opportunities/addAll", response.data);
      });
    }
  },
  computed: {
    opportunities() {
      return this.$store.state.opportunities.list;
    }
  },
  methods: {
    createOpportunity() {
      const title = this.$refs.titleInput.value;
      const description = this.$refs.descriptionInput.value;
      const link = this.$refs.linkInput.value;

      if (!title || !description || !link) return;

      const data = {
        title: title,
        content: description,
        link: link
      };

      let attempt = this.$store.dispatch("opportunities/create", data);

      attempt.then(() => {
        this.$refs.titleInput.value = "";
        this.$refs.descriptionInput.value = "";
        this.$refs.linkInput.value = "";
        this.$refs.modal.closeModal();
      });
    }
  }
};
</script>

<style scoped>
.opportunities-card-container {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  grid-column-gap: 3rem;
  grid-row-gap: 5rem;
  margin-top: 1.5rem;
}

#floating-buttons {
  position: fixed;
  right: 3rem;
  bottom: 2rem;
  display: grid;
  grid-template-columns: 1fr;
  row-gap: 10px;
  z-index: 100;
}

.category-selector-button {
  background: white;
  display: inline-flex;
  align-items: center;
  padding: 0.8rem 1rem 0.8rem 1rem;

  justify-content: space-between;
  border-radius: 5px;
  color: #9b9b9b;
}

.footer {
  height: unset;
}

.footer-button-container {
  width: 100%;
  padding-left: 0;
  padding-right: 0;
}

.footer-button {
  width: 100%;
  height: 35px;
  margin-left: 0;
  margin-right: 0;
  background: #fda57d;
  border-radius: 5px;
  font-size: 0.9rem;
  color: white;
}

@media (max-width: 1199.98px) {
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