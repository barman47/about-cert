<template>
  <div id="profile-nav">
    <div
      :class="{active: $nuxt.$route.path == '/profile/edit'}"
      @click="goToLink('/profile/edit')"
    >Edit Profile</div>
    <div
      :class="{active: $nuxt.$route.path == '/profile/cv/create'}"
      class
      @click="goToLink('/profile/cv/create')"
    >Create CV</div>
    <div
      :class="{active: $nuxt.$route.path == '/profile/cv/saved'}"
      class
      @click="goToLink('/profile/cv/saved')"
    >Saved CVs</div>
    <div
      :class="{active: $nuxt.$route.path == '/profile/cv/job-kit'}"
      class
      @click="goToLink('/profile/cv/job-kit')"
    >Job Kit</div>
  </div>
</template>

<script>
export default {
  methods: {
    goToLink(path) {
      if (path == this.$nuxt.$route.path) return;
      this.$nuxt.$router.push(path);
    }
  },
  mounted() {}
};
</script>

<style scoped>
#profile-nav {
  height: 50px;
  width: 100%;
  border-radius: 10px;
  overflow: hidden;
  background: white;
  display: flex;
  align-items: center;
  padding-left: 1rem;
  padding-right: 1rem;
}

#profile-nav > div {
  height: 100%;
  padding-right: 1rem;
  padding-left: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c4c4c4;
  font-style: normal;
  font-weight: normal;
  margin-left: 10px;
  margin-right: 10px;
  cursor: pointer;
  text-align: center;
}

#profile-nav > div.active {
  position: relative;
  color: #0084ff;
}

#profile-nav > div.active::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #0084ff;
}
@media (max-width: 575.98px) {
    #profile-nav{
        padding-left: 0.5rem;
        padding-right: 0.5rem;

    }
    #profile-nav > div{
        flex: 1;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        margin-left: 0;
        margin-right: 0;
    }
}
</style>
