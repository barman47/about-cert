<template>
  <div class="note" :style="{borderLeftColor: color}">
    <div class="note-content">
      <div class="is-done">
        <span class="marker"></span>
      </div>
      <div class="note-text" >
        Lorem ipsum dolor, sit amet consectetur adipisicing elit.
        Perspiciatis, dolorem sapiente recusandae beatae tempore porro
        atque soluta cum, corporis totam inventore
         harum non quaerat voluptate fugit a iste ut officia!
      </div>
      <div class="edit-column"> <span class="marker"></span></div>
    </div>
    <div class="note-footer">
      <small :style="{color: color}">Average</small>
      <small>Added yester day at 2:30</small>
    </div>
  </div>
</template>

<script>
export default {
  data(){
    return {
      colors: [
        "blue",
        "green",
        "yellow",
        "white",
        "red",
        "#0084ff",
        "rgb(114, 255, 3)",
        "brown"
      ]
    }
  },
  computed: {
    color(){
      return this.colors[Math.floor(Math.random() * this.colors.length)]
    }
  }
}
</script>

<style scoped>
  .note{
    width: 100%;
    border-left: 2px solid #999;
    border-bottom: 0.5px solid #B2B2B2;
    padding: 0.5rem;
  }

  .note-content{
    display: flex;
    align-items: flex-start;
  }

  .is-done,
  .edit-column{
    width: 25px;
  }

  .note-text{
    flex: 1;
    font-size: 0.75rem;
    line-height: 1.15rem;
    font-style: normal;
    font-weight: 300;
  }

  /* Footer */
  .note-footer{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem;
  }

  .is-done{
    width: 1rem;
    height: 0.5rem;
    clip-path: circle();

    background: #999;
  }

</style>
