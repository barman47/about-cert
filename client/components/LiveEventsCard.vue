<template>
  <div class="live-events-card" @click="goToPage('/events/live/' + liveEvent.id)">
    <div class="left" @click="goToPage('/events/live/' + liveEvent.id)" :style="{backgroundImage: 'url(\'' + coverImageUrl + '\')'}">
      <div class="state-tag">Live</div>
    </div>
    <div class="right">
      <h4 class="title" @click="goToPage('/events/live/' + liveEvent.id)">
        <div class="state-tag">Live</div>
        {{liveEvent.title}}
      </h4>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    liveEvent: {
      type: Object,
      required: true
    }
  },
  computed: {
    coverImageUrl() {
      if (!this.liveEvent.cover_image) return "/test video.png";

      return (
        process.env.BACKEND_BASE_URL.replace(/(\/+)$/, "") +
        "/" +
        this.liveEvent.cover_image.replace(/^(\/+)/, "")
      );
    }
  },
  methods: {
    goToPage(path) {
      if (path == this.$nuxt.$route.path) return;
      this.$nuxt.$router.push(path);
    }
  }
};
</script>

<style scoped>
.live-events-card {
  height: 15rem;
  /* border-radius: 10px; */
  background: white;
  width: 100%;
  overflow: hidden;

  display: grid;
  grid-template-columns: 5fr 4fr;
}

.live-events-card .left {
  background-image: url("/test video.png");
  background-position: center center;
  background-size: cover;
  background-repeat: no-repeat;
  background-color: rgba(0, 0, 0, 0.4);
  background-blend-mode: darken;

  position: relative;
}

.live-events-card .state-tag {
  background: #ff5845;
  color: white;
  display: inline-block;
  padding-top: 0.3rem;
  padding-bottom: 0.3rem;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
  font-size: 0.7rem;
}

.live-events-card .left .state-tag {
  position: absolute;
  top: 0.5rem;
  left: 0.5rem;
}

.live-events-card .right .state-tag {
  display: none;
}

/* For the right side */

.live-events-card .right {
  padding: 0.5rem;
}

.live-events-card .right .title {
  margin-top: 0.4rem;
  font-size: 0.9rem;
  cursor: pointer;
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
  .live-events-card {
    grid-template-columns: 1fr;
    position: relative;
    border-radius: 10px;
    overflow: hidden;
  }
  .live-events-card .left .state-tag {
    display: none;
  }

  .live-events-card .right .state-tag {
    display: inline-block;
  }

  .live-events-card > * {
    position: absolute;
    top: 0;
    bottom: 0;
    right: 0;
    left: 0;
  }

  .live-events-card .right {
    z-index: 2;
    color: white;
  }
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
}
</style>