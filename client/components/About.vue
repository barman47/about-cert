<template>
    <div>
        <h4 class="about-header">About Us</h4>
        <div class="underline">
          <div class="line"></div>
        </div>
        <div id="wrapper">
          <div class="area item-1">
            <div class="info-box">
              <h5 class="header">Our Mission</h5>
              <div>
                <p class="content">
                  AboutCert aims to continually create and be a solution to serve the basic needs of everyone under the learning. To help our users create their better tomorrow.
                </p>

              </div>
            </div>
          </div>
          <div class="area item-2">
            <div class="image-container">
              <img class="image" src="~/assets/png/undraw_hire_te5y1.png" alt="">
            </div>
          </div>
          <div class="area item-3">
            <div class="image-container">
              <img class="image" src="~/assets/png/undraw_about_us_page_ee1k1.png" alt="">
            </div>
          </div>
          <div class="area item-4">
            <div class="info-box">
              <h5 class="header">Who We Are</h5>
              <p class="content">
                We are a group of optimists, idealists , students, Developers and Designers  that passionately care about growth and change of the learning community, which is our major driving force for creating AboutCert. And hope that "in our own little way, we would shake the world" – Mahatma Gandhi
              </p>
            </div>
          </div>
        </div>

        <h4 class="about-header">The Team</h4>
        <div class="underline">
          <div class="line"></div>
        </div>

        <team />
    </div>
</template>

<script>
  import Team from "~/components/Team.vue";
  export default {
    components: {
      Team
    },
    data(){
      return {
        
      }
    }
  }
</script>

<style scoped>
#wrapper{
    display: grid;
    grid-template-columns: 1fr 1fr;
    grid-column-gap: 2rem;
    grid-row-gap: 4em;
    padding-left: 4rem;
    padding-right: 4rem;
    padding-bottom: 10em;
}

.about-header{
  text-align: center;
  color: #141B5B;
  font-size: 1.8rem;
  font-style: normal;
  font-weight: normal;
  margin-bottom: 0.2rem;
}

.underline{
  display: flex;
  align-items:center;
  justify-content: center;
  margin-bottom: 2rem;
}

.underline .line{
  height: 2px;
  background: #42D4A3;
  display: inline-block;
  width: 3rem;
  border-radius: 3px;
}

.area{
  display: flex;
  align-items: center;
  justify-content: center;
}

.area .info-box{
  box-shadow: 4px 4px 16px rgba(0, 0, 0, 0.2);
  border-radius: 10px;
  width: 80%;
  height: 16rem;
  padding: 1rem;
  padding-left: 3rem;
  padding-right: 3rem;
  text-align: center;
}

.area .info-box .header{
  color: #141B5B;
  font-style: normal;
  font-weight: normal;
  font-size: 1.2rem;
}

.area .info-box .content{
  font-size: 0.8rem;
  line-height: 1.2rem;
  /* shape-outside: polygon(100% 0, 100% 100%, 0 100%); */
}

.area .image-container{
  display: flex;
  align-items: center;
  justify-content: center;
}

.area .image{
  max-height: 100%;
  max-width: 100%;
}

@media (max-width: 1199.98px) {
    #wrapper{
        grid-column-gap: 2rem;
        grid-row-gap: 3em;
        padding-left: 3rem;
        padding-right: 3rem;
    }
}

@media (max-width: 991.98px) {
}

@media (max-width: 767.98px) {
    #wrapper {
        grid-template-columns: 1fr;
        padding-left: 1rem;
        padding-right: 1rem;
        padding-bottom: 5rem;
    }

    .area .image-container{
      width: 80%;
    }
    .area .image{
      width: 100%;
      height: auto;
    }

    .area.item-1{
      order: 2;
    }

    .area.item-2{
      order: 1;
    }

    .area.item-3{
      order: 3;
    }

    .area.item-4{
      order: 3;
    }
}

@media (max-width: 575.98px) {
    /* #wrapper {
    } */

    .area .info-box{
      width: 95%;
    }
}
</style>
