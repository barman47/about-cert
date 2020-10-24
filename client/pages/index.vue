<template>
  <div>
    <section id="hero">
      <div id="text-side">
        <div>
          <h1 class="hero-shout">
            Get Organized. <br>
            Stay Updated.
          </h1>
          <p class="hero-brief">
            This isn’t your regular dropbox or google drive...<br>
            It’s an abode for learners to have all of the fun they want
          </p>
        </div>
      </div>
      <div id="image-side">
        <img id="undraw_scrum_board_cesn" src="~/assets/svg/undraw_scrum_board_cesn.svg" alt />
      </div>
      <svg class="svg-background"
        fill="none"
        xmlns="http://www.w3.org/2000/svg">
        <g>
          <rect y="-24%" width="115%" height="124%" rx="14%" fill="#141B5B" />
        </g>
      </svg>

      <img class="rectangle-205" src="~/assets/svg/Rectangle-205.svg" alt="">
      <img class="rectangle-207" src="~/assets/svg/Rectangle-207.svg" alt="">
      <img class="rectangle-206" src="~/assets/svg/Rectangle-206.svg" alt="">
      <img class="ellipse-90" src="~/assets/svg/ellipse-90.svg" alt="">
      <img class="ellipse-89" src="~/assets/svg/ellipse-89.svg" alt="">
    </section>

    <section>
      <about></about>
    </section>
  </div>
</template>

<script>
import About from "~/components/About.vue";

export default {
  components: {
    About,
  },
};
</script>

<style scoped>
section{
  margin-top: 1rem;
}

section:first-child{
  margin-top: 0;
}
/* This is for the hero banner */
#hero {
  display: grid;
  grid-template-areas: "text-side image-side";
  position: relative;
  height: 100vh;
  height: var(--viewport-height, 100vh);
  overflow: hidden;
}

#hero > .svg-background {
  position: absolute;
  top: 0;
  left: 0;
  z-index: -1;
  width: 100%;
  height: 100%;
}

#hero .svg-corner-circle {
  position: absolute;
  top: 0;
  left: 0;
  width: 15em;
  height: 15em;
  z-index: 3;
}

#hero .rectangle-205{
  position: absolute;
  right: 0;
  top: 14rem;
  z-index: 0;
  width: 42rem;
}

#hero .rectangle-206{
  position: absolute;
  bottom: 3%;
  z-index: 0;
  width: 25%;
  left: 50%;
}

#hero .rectangle-207{
  position: absolute;
  bottom: 34%;
  z-index: 0;
  width: 25%;
  left: 67%;
}

#hero .ellipse-90{
  position: absolute;
  bottom: 65%;
  z-index: 0;
  height: 21%;
  left: 43%;
}

#hero .ellipse-89{
  position: absolute;
  bottom: 73%;
  z-index: 0;
  height: 10%;
  left: 40%;
}

#text-side {
  grid-area: text-side;
  position: relative;
  z-index: 2;
}

#text-side > div{
  position: absolute;
  top:50px;
  right:0;
  bottom:0;
  left: 0;
  height: calc(100% - 50px);
  width: 100%;

  display: flex;
  flex-direction: column;
  justify-content: center;
  /* align-items: center; */
  padding-left: 7.8rem;
}

#text-side > div .hero-shout{
  font-size: 3.5rem;
  color: white;
  padding: 0;
  margin: 0;
}

#text-side > div .hero-brief{
  font-size: 1.375rem;
  color: white;
  width: 80%;
  font-style: Light;
  line-height: 1.68rem;
}

#image-side {
  grid-area: image-side;
  position: relative;
}

#undraw_scrum_board_cesn {
  position: absolute;
  z-index: 1;
  width: 80%;
  height: auto;
  left: 0;
  bottom: 6em;
}

/* // Extra small devices (portrait phones, less than 576px)
@media (max-width: 575.98px) { ... }


// Small devices (landscape phones, less than 768px)
@media (max-width: 767.98px) { ... }

// Medium devices (tablets, less than 992px)
@media (max-width: 991.98px) { ... }

// Large devices (desktops, less than 1200px)
@media (max-width: 1199.98px) { ... }

// Extra large devices (large desktops)
// No media query since the extra-large breakpoint has no upper bound on its width */

@media (max-width: 1199.98px) {
  #text-side > div .hero-shout{
    font-size: 2.8rem;
  }
  #text-side > div .hero-brief{
    font-size: 1rem;
  }
}

@media (max-width: 991.98px) {
  /* #hero{
    height: 70vh;
  } */
  #text-side > div{
    padding-left: 4rem;
  }
  #text-side > div .hero-shout{
    font-size: 2.1rem;
  }
  #text-side > div .hero-brief{
    font-size: 1rem;
  }
}

@media (max-width: 767.98px) {
  #text-side > div{
    padding-left: 3rem;
  }
  #text-side > div .hero-shout{
    font-size: 1.5rem;
  }
  #text-side > div .hero-brief{
    font-size: 0.9rem;
  }
}

@media (max-width: 575.98px) {
  #hero{
    height: 50vh;
  }

  #text-side > div .hero-shout{
    font-size: 1.2rem;
  }
  #text-side > div .hero-brief{
    font-size: 0.7rem;
    line-height: 1.2em;
  }
}
</style>
