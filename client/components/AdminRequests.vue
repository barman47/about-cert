<template>
  <div class="admin-requests-main-content">
    <h4 class="header">Requests</h4>
    <div class="admin-requests-main-container">
      <AdminRequestsSingleRequest @singleRequestButtonClicked="$emit('singleRequestButtonClicked',$event)" :request="request" v-for="request in requests" :key="request.id"></AdminRequestsSingleRequest>
    </div>
  </div>
</template>

<script>
import AdminRequestsSingleRequest from "~/components/AdminRequestsSingleRequest.vue"
export default {
  components: {
    AdminRequestsSingleRequest
  },
  computed: {
    requests(){
      return this.$store.getters["admin/requests"]
    }
  },
  mounted() {
  }
}
</script>

<style>
  .admin-requests-main-content{
    background: white;
    padding: 0.5rem;
    color: black;
    height: 100%;
    width: 100%;
    border-radius: 10px;
    overflow:hidden;

    display: flex;
    flex-direction: column;
  }

  .admin-requests-main-content > .header{
    margin-top: 0.5rem;
    font-weight: normal;
    font-size: 16px;
    line-height: 20px;
    color: #1F014F;
  }

  .admin-requests-main-container{
    flex:1;
    overflow-y: auto;
  }

  .admin-requests-main-container::-webkit-scrollbar {
    width: 0.4em;
  }
  
  .admin-requests-main-container::-webkit-scrollbar-track {
    box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
  }
  
  .admin-requests-main-container::-webkit-scrollbar-thumb {
    /* background-color: darkgrey; */
    /* outline: 1px solid slategrey; */
    background-color: #C4C4C4;
    border-radius: 10px;    
  }

  
</style>