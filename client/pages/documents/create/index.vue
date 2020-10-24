<template>
  <div id="create-document">
    <div class="floating-button-container">
      <div @click="showOtherFloatingButton = !showOtherFloatingButton" class="floating-button add"></div>
      <div v-show="showOtherFloatingButton" @click="goToLink('/documents/create/word')" class="floating-button child word">
        <div class="floating-button-image"></div>
        <div class="floating-button-text">Word</div>
      </div>
      <div v-show="showOtherFloatingButton" @click="goToLink('/documents/create/presentation')"  class="floating-button child presentation">
        <div class="floating-button-image"></div>
        <div class="floating-button-text">Presentation</div>
      </div>
      <div v-show="showOtherFloatingButton" @click="goToLink('/documents/create/pdf')" class="floating-button child pdf">
        <div class="floating-button-image"></div>
        <div class="floating-button-text">PDF</div>
      </div>
      <div v-show="showOtherFloatingButton" @click="goToLink('/documents/create/spreadsheet')"  class="floating-button child spreadsheet">
        <div class="floating-button-image"></div>
        <div class="floating-button-text">Spreadsheet</div>
      </div>

    </div>
  </div>
</template>

<script>
import Editor from '@tinymce/tinymce-vue';
export default {
  layout: "document",
  components: {
  },
  data(){
    return {
      showOtherFloatingButton : false
    }
  },
  methods: {
    goToLink(path){
      if(this.$nuxt.$route.path != path)
      this.$nuxt.$router.push(path)
    }
  },
};
</script>

<style scoped>
    #create-document{
        display: flex;
        height: 100%;
        flex-direction: column;
        position: relative;
    }

    #create-document .floating-button-container{
      --large-size: 3rem;
      --small-size: 2rem;
      position: absolute;
      top: 0.5rem;
      left: 0.5rem;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    #create-document .floating-button{
      display: flex;
      cursor: pointer;
      margin-top: 1rem;
      position: relative;
    }
    #create-document .floating-button:first-child{
      margin-top: 0;
    }

    #create-document .floating-button.child{
      margin-left: calc(var(--large-size) / 2 - var(--small-size) / 2);
      height: var(--small-size);
      display: flex;
      align-items: center;
    }

    #create-document .floating-button .floating-button-image{
      position: absolute;
      left: 0;
      top: 0;
      height: var(--small-size);
      width: var(--small-size);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      background-size: contain;
      background-repeat: no-repeat;
      background-position: center center;
    }

    #create-document .floating-button .floating-button-text{
      font-size: 0.85rem;
      color: white;
      margin-left: calc(var(--small-size) / 2);
      padding-left: calc(var(--small-size) / 2 + 2px);
      height: calc(var(--small-size) * 0.7);
      display: flex;
      align-items: center;
      padding-right: 1rem;
      border-top-right-radius: calc(var(--small-size) * 0.7 / 2);
      border-bottom-right-radius: calc(var(--small-size) * 0.7 / 2);
    }



    #create-document .floating-button.add{
      background-image: url("/png/add-document.png");
      height: var(--large-size);
      width: var(--large-size);
      background-size: contain;
      background-position: center center;
      background-repeat: no-repeat;
    }

    .word .floating-button-image{
      background-image: url("/png/word-logo.png"); 
    }

    .word .floating-button-text{
      background: #0084ff;
    }

    .presentation .floating-button-image{
      background-image: url("/png/presentation-logo.png"); 
    }
    .presentation .floating-button-text{
      background: #FF7A00;
    }

    .pdf .floating-button-image{
      background-image: url("/png/pdf-logo.png"); 
    }

    .pdf .floating-button-text{
      background: #FF5B45;
    }

    .spreadsheet .floating-button-image{
      background-image: url("/png/spreadsheet-logo.png"); 
    }

    .spreadsheet .floating-button-text{
      background: #5CC05C;
    }
</style>