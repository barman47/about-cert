<template>
  <div id="authenticated-layout" :class="{'dark-theme': theme == 'dark'}">
      <navbar></navbar>
      <sidebar></sidebar>
      <div id="authenticated-layout-content">
          <document-sidebar :expanded="documentSidebarExpanded" @toggleExpanded="toggleDocumentSideBar()" ref="documentSidebar"></document-sidebar>
          <div id="other-content" :style="otherStyle">
                <nuxt />
            </div>
      </div>
  </div>

</template>

<script>
import Navbar from "~/components/Navbar.vue";
import Sidebar from "~/components/Sidebar.vue";
import DocumentSidebar from "~/components/DocumentSidebar.vue";

export default {
  components: {
      Navbar,
      Sidebar,
      DocumentSidebar
  },
  middleware: "auth",
  data(){
      return {
          otherStyles: [
              {"grid-area": "documentContent"},
              {"grid-area": "documentBarOverflow-start / documentBarOverflow-start / span 1/ span 2"},
          ],
          documentSidebarExpanded: false,
      }
  },
  methods:{
      toggleDocumentSideBar(){
        this.documentSidebarExpanded = !this.documentSidebarExpanded
      }
  },
  computed: {
    theme(){
      return this.$store.state.theme
    },
    otherStyle(){
      // return this.otherStyles[this.documentSidebarExpanded ? 0 : 1]
      return this.otherStyles[this.documentSidebarExpanded ? 1 : 1]
    }
  }
};
</script>

<style scoped>

#authenticated-layout {
    display: grid;
    grid-template: 
        "sidebar navbar" 50px
        "sidebar authenticated-layout-content" 1fr 
        / 50px 1fr;
    height: 100vh;
    height: var(--viewport-height, 100vh);
    position: relative;
}

#authenticated-layout.dark-theme{
  background: #3F3D56;
}

#authenticated-layout-content{
    grid-area: authenticated-layout-content;
    border-top-left-radius: 10px;
    /* background:#e5e5e5; */
    /* background: #eeeeff; */
    background: #F0F0F0;
    height: 100%;
    overflow:hidden;

    /* Display */
    display: grid;
    grid-template: "documentBar documentBarOverflow documentContent"
                            /50px 150px 1fr;
}

.dark-theme #authenticated-layout-content{
  background: #3A2C51;
}

#other-content{
    grid-area: documentContent;
    height: 100%;
    overflow:hidden;
    overflow-y:auto;
    padding: 1rem;
    padding-left: 2rem;
    padding-right: 2rem;
}

#document-sidebar{
    grid-area: documentBar-start / documentBar-start / span 1 / span 2;
    background: white;
    border: 1px solid #F2F2F2;
    box-shadow: 2px 0px 5px rgba(0, 0, 0, 0.09);
    border-radius: 10px 0px 0px 0px;
    
    height: 100%;
    overflow: hidden;
    overflow-y: auto;
}

.dark-theme #document-sidebar {
    background: #140034;
    border: 1px solid #140034;
}

@media (max-width: 1199.98px) {
  #other-content{
    padding-left: 1rem;
    padding-right: 1rem;
  }
}

@media (max-width: 575.98px) {
  #authenticated-layout {
    grid-template:
      "navbar navbar" 50px
      "authenticated-layout-content authenticated-layout-content" 1fr
      "sidebar sidebar" 50px;
    overflow: hidden;
  }

  #authenticated-layout-content{
    border-top-left-radius: 0;
  }
}
</style>
