<template>
  <div class="bread-crumbs">
      <div class="home-button image"></div>
      <div class="others" v-for="(link, i) in items" :key="i">
          <div class="arrow image"></div>
          <div class="bread-crumb-text" @click="goToLink(link.link)">{{link.text}}</div>
      </div>
  </div>
</template>

<script>
export default {
    data(){
        return {
        }
    },
    props: ["items"],
    methods: {
        goToLink(path){
            if(this.$nuxt.$route.path == path)
                return

            return this.$nuxt.$router.push(path)
        }
    }
}
</script>

<style scoped>
    .bread-crumbs{
        font-size: 0.8rem;
        font-size: #9b9b9b;
        display: flex;
        align-items: center;
    }

    .others{
        display: flex;
        align-items: center;
    }

    .image{
        height: 1.2rem;
        width: 1rem;
        background-repeat: no-repeat;
        background-size: contain;
        background-position: center center;
    }
    .home-button{
        background-image: url("/png/home.png");
    }

    .arrow{
        background-image: url("/png/bread-crumb-arrow.png");
        height: 0.8rem;
    }

    .bread-crumb-text{
        margin-left: 0.5rem;
        cursor: pointer;
        font-size: 0.7rem;
    }

    .bread-crumb-text:hover{
        color: #0084ff;
    }
</style>