<template>
  <div class="files-display">
      <h2 class="nothing-to-display" v-if="files.length <= 0">
          {{isInTrash ? "Trash is empty" : "Nothing To Display"}}
      </h2>
      <single-file :intent="intent" v-show="!isList" v-for="file in files" :key="file.id" :file="file"></single-file>
      <div class="list-container" v-show="!!isList && files.length > 0">
          <div class="header">
              <div></div>
              <div class="header-item">Name</div>
              <div class="header-item">Size</div>
              <div class="header-item">Date Deleted</div>
              <div></div>
          </div>
          <single-list-file :intent="intent" :file="file" v-for="file in files" :key="file.id" ></single-list-file>
      </div>
  </div>
</template>

<script>
    import SingleFile from "~/components/SingleFile";
    import SingleListFile from "~/components/SingleListFile";
    export default {
        components: {
            SingleFile,
            SingleListFile
        },
        props: ["files", "isList", "intent"],
        computed: {
            isInTrash(){
                return (this.intent || "").toLowerCase() == "trash"
            }
        }
    }
</script>

<style scoped>
    .files-display{
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
    }

    .files-display .nothing-to-display{
        font-style: italic;
        font-weight: normal;
        font-size: 0.7rem;
    }

    .list-container{
        display: flex;
        width: 100%;
        flex-direction: column;

        background: white;
        border-radius: 10px;

        padding: 0.2rem;
        padding-top: 0.5rem;
    }

    .list-container .header{
        display: grid;
        grid-template-columns: 2.5rem 2fr 1fr 3fr 25px;
    }

    .list-container .header > *{
        padding: 5px;
        color: black;
        font-size: 0.75rem;
        border-left: 2px solid #f1e6e6;
    }

    .list-container .header > *:first-child,
    .list-container .header > *:nth-child(2),
    .list-container .header > *:last-child
    {
        border-left: none;
    }

</style>