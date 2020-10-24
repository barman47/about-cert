<template>
  <ul id="home-navigation">
    <slot>
      <li
        :class="{active: $nuxt.$route.name == 'updates'}"
        @click="goToPage('updates')"
      >Recent Updates</li>
      <li :class="{active: $nuxt.$route.name == 'events'}" @click="goToPage('events')">Your Events</li>
      <li
        :class="{active: $nuxt.$route.name == 'opportunities'}"
        @click="goToPage('opportunities')"
      >Opportunities</li>
      <li :class="{active: $nuxt.$route.name == 'grow'}" @click="goToPage('grow')">Grow</li>
    </slot>
  </ul>
</template>

<script>
export default {
  methods: {
    goToPage(page) {
      if (this.$nuxt.$route.path == "/" + page) return;
      this.$nuxt.$router.push({ name: page });
    } //end function goToPage
  },
  mounted() {}
};
</script>

<style scoped>
#home-navigation {
  width: 100%;
  /* overflow: hidden; */
  list-style: none;
  padding: 2px;
  display: flex;
  flex-direction: row;
  align-items: center;
  /* justify-content: flex-start; */
  margin-bottom: 2.5rem;
  flex-wrap: wrap;
  margin-top: 0.2rem;
}

#home-navigation li {
  color: #484848;
  font-size: 1.2rem;
  margin-right: 2rem;
  cursor: pointer;
  /* white-space: nowrap; */
}

.dark-theme #home-navigation li {
  color: #999999;
}

#home-navigation li.active {
  border-bottom: 2px solid #0085ff;
  color: #0085ff;
  font-size: 2rem;
  padding-bottom: 0.5rem;
}

@media (max-width: 1199.98px) {
}

@media (max-width: 991.98px) {
}

@media (max-width: 799.98px) {
  #home-navigation li {
    font-size: 1rem;
    margin-right: 1rem;
  }
  #home-navigation li.active {
    font-size: 1rem;
  }
}

@media (max-width: 767.98px) {
  #home-navigation {
    margin: 0;
    height: 100%;
    padding-bottom: 0;
    padding-right: 0.5rem;
    padding-left: 0.5rem;
  }

  #home-navigation li {
    flex: 1;
    height: 100%;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    margin-right: 0.2rem;
    margin-right: 0.2rem;
  }

  #home-navigation li.active {
    font-size: 1rem;
    padding-bottom: 0;
  }
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
  #home-navigation {
    height: calc(100% - 2px);
    box-shadow: 3px 2px 7px 0px rgba(189, 188, 188, 0.13);
  }
  #home-navigation li {
    font-size: 0.8rem;
  }
  #home-navigation li.active {
    font-size: 0.8rem;
  }
}

@media (max-width: 467px) {
  #home-navigation li {
    font-size: 0.7rem;
  }
  #home-navigation li.active {
    font-size: 0.7rem;
  }
}
</style>