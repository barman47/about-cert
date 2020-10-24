<template>
  <div id="updates-page">
    <div class="home-navigation-desktop">
      <home-navigation />
    </div>
    <div>
      <h4
        id="live-events-container-header"
        v-if="liveEvents && liveEvents.length > 0"
      >Live events that might interest you</h4>
      <div id="live-events-section">
        <div @click="liveEventsNavigationClicked('left')" class="nav-left"></div>
        <div @click="liveEventsNavigationClicked('right')" class="nav-right"></div>
        <div
          ref="liveEventContainer"
          id="live-events-container"
          v-if="liveEvents && liveEvents.length > 0"
        >
          <div class="single-live-event" v-for="liveEvent in liveEvents" :key="liveEvent.id">
            <LiveEventsCard :liveEvent="liveEvent" />
          </div>
        </div>
      </div>
    </div>
    <div id="updates-container">
      <updates-card v-for="update in updates" :key="update.id" :update="update"></updates-card>
    </div>
  </div>
</template>

<script>
import HomeNavigation from "~/components/HomeNavigation";
import UpdatesCard from "~/components/UpdatesCard";
import LiveEventsCard from "~/components/LiveEventsCard";
// import socket from "~/plugins/socket.io.js"

export default {
  scrollToTop: true,
  components: {
    HomeNavigation,
    UpdatesCard,
    LiveEventsCard
  },
  layout: "authenticated",
  async fetch({ store, $axios }) {
    if (!store.state.live_events.paginationData.path) {
      await store.dispatch("live_events/fetchAllLiveEvents");
    }
    if (!store.state.updates.paginationData.total)
      await $axios.get("/api/posts").then(response => {
        store.commit("updates/addAll", response.data);
      });
  },
  computed: {
    updates() {
      // return this.$store.state.updates.list
      return this.$store.state.updates.list.filter(
        el => el.user.id != this.$auth.user.id
      );
    },
    liveEvents() {
      return this.$store.state.live_events.list || [];
    }
  },
  methods: {
    liveEventsNavigationClicked(direction) {
      console.log(direction);
      const distance =
        window.innerWidth * 0.8 * (direction == "right" ? 1 : -1);
      this.$refs.liveEventContainer.scrollLeft += distance;
    }
  },
  mounted() {
    // socket.on("message", (message) => {console.log(message)})
  }
};
</script>
<style scoped>
#updates-page {
  width: 100%;
  overflow-x: hidden;
  height: max-content;
}

#updates-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
  margin-bottom: 1rem;
}

#live-events-container-header {
  margin-top: 0.2rem;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

#live-events-section {
  position: relative;
  --navigation-height: 2rem;
  --navigation-left: 2rem;
  padding-left: 3rem;
  padding-right: 3rem;
}

#live-events-section .nav-left,
#live-events-section .nav-right {
  position: absolute;
  height: var(--navigation-height);
  width: var(--navigation-left);
  top: calc(50% - calc(var(--navigation-height) / 2));
  z-index: 4;
  background-size: contain;
  background-repeat: no-repeat;
  cursor: pointer;
}

#live-events-section .nav-left {
  left: 5px;
  background-image: url("/png/nav-live-event-desktop-left.png");
  background-position: left center;
}
#live-events-section .nav-right {
  right: 5px;
  background-image: url("/png/nav-live-event-desktop-right.png");
  background-position: right center;
}

#live-events-container {
  width: 100%;
  overflow: hidden;
  overflow-x: auto;
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  margin-bottom: 1rem;
  padding-top: 1rem;
  padding-bottom: 1rem;
  margin-bottom: 2rem;
  --live-event-spacing: 1rem;
  --relative-live-event-width: 45%;

  scroll-behavior: smooth;
}

#live-events-container .single-live-event {
  width: calc(
    var(--relative-live-event-width) - calc(var(--live-event-spacing) / 2)
  );
  min-width: calc(
    var(--relative-live-event-width) - calc(var(--live-event-spacing) / 2)
  );
  max-width: calc(
    var(--relative-live-event-width) - calc(var(--live-event-spacing) / 2)
  );
  overflow: hidden;
  margin-right: calc(var(--live-event-spacing) / 2);
}

@media (max-width: 1199.98px) {
  #updates-container {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 991.98px) {
  #updates-container {
    grid-template-columns: 1fr 1fr;
  }

  #live-events-container {
    --relative-live-event-width: 55%;
  }
}
@media (max-width: 767.98px) {
  #updates-container {
    grid-template-columns: 1fr;
  }

  #live-events-container {
    --relative-live-event-width: 60%;
  }

  #live-events-section {
    --navigation-height: 3.5rem;
    --navigation-left: 2.5rem;
    padding-left: 0;
    padding-right: 0;
  }
  #live-events-section .nav-left {
    background-image: url("/png/nav-live-event-left.png");
  }
  #live-events-section .nav-right {
    background-image: url("/png/nav-live-event-right.png");
  }

  
}

@media (max-width: 575.98px) {
  #live-events-container {
    --relative-live-event-width: 70%;
  }
}
</style>