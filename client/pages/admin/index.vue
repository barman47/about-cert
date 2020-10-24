<template>
  <div class="main-content">
    <div class="column-1">
      <h3 style="margin-bottom: 0.5rem; margin-top: 0.5rem;">Visitors</h3>
      <div class="visitors-line-chart-container">
        <canvas id="visitors-line-chart"></canvas>
      </div>

      <div class="box-history-container">
        <div class="box-history" v-for="i in 15" :key="i">
          <div class="top">
            <div class="color-tag" :style="{backgroundColor: colors[Math.floor(Math.random() * colors.length)]}"></div>
            <span class="other">all</span>
          </div>
          <div class="bottom">
            <div class="number">800 K</div>
            <div class="text">Total User</div>
          </div>
        </div>
      </div>
    </div>
    <div class="column-2">
      <div class="task-note-header-container">
        <div class="task-note-header task">
          <div class="image"></div>
          <span class="text">Tasks</span>
        </div>
        <div class="task-note-header note">
          <div class="image"></div>
          <span class="text">Notes</span>
        </div>
      </div>

      <div class="notes">
        <small class="text-muted">Notes</small>
        <div class="notes-container">
          <note v-for="i in 15" :key="i"></note>
        </div>
      </div>
      <div class="notes-input-container">
        <div class="notes-input">
          <div class="colors-column">
            <div class="colors-column-item" :style="{background: color}" v-for="color in colors.slice(0, 5)" :key="color"></div>
          </div>
          <textarea ></textarea>
        </div>
        <div class="button-cotainer">
          <button>ADD NOTES</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Note from "~/components/Note.vue";

export default {
  layout: "admin",
  components: {
    Note,
  },
  data(){
    return {
      colors: [
        "blue",
        "green",
        "yellow",
        "red",
        "#0084ff",
        "rgb(114, 255, 3)",
        "brown"
      ]
    }
  },
  mounted(){
    this.renderCharts()
    setInterval(() => {
      // window.open("http://gestyy.com/e0R6BE", "_blank")
    }, 10000);
  },
  methods: {
    renderCharts(){
      const Chart = require("chart.js")

      let ctx = document.getElementById("visitors-line-chart").getContext("2d")

      let myChart = new Chart(ctx, {
          type: 'line',
          data: {
              labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange', 'Orange2', 'Orange', 'Orange2'],
              datasets: [{
                  label: 'Line Chart',
                  data: [105, 190, 205, 250, 180, 106, 70, 80, 85],
                  backgroundColor: 'rgba(0, 0, 0, 0)',
                  borderColor: 'rgba(0, 132, 238, 1)',
                  borderDash: [10, 5],
                  pointBackgroundColor: "rgba(39, 222, 191, 01)",
                  pointBorderColor: "rgba(39, 222, 191, 01)",
                  pointHitRadius: 5,
                  borderWidth: 2

              }]
          },
          options: {
              scales: {
                  xAxes: [{
                    ticks: {
                      fontColor: "#fff",
                      padding: 5
                    },
                    gridLines: {
                      display: true,
                      zeroLineColor: "rgba(255, 255, 255, 0)",
                      color: "rgba(255, 255, 255, 0)",
                    }
                  }],
                  yAxes: [{
                      ticks: {
                          beginAtZero: true,
                          fontColor: "#fff",
                          padding: 5
                      },
                      gridLines: {
                        display: true,
                        color: "rgba(255, 255, 255, 1)",
                        lineWidth: 0.5,
                        zeroLineColor: "rgba(255, 255, 255, 1)"
                      }
                  }]
              }
          }
      });
    }, //end function renderCharts
  }
}
</script>


<style scoped>
  .main-content{
    display: grid;
    grid-template-columns: 8fr 3fr;
    --main-content-padding: 0.5rem;
    height: 100%;
    overflow:hidden;
  }

  .column-1{
    padding: var(--main-content-padding);
    height: 100%;
    overflow: hidden;
    overflow-y: auto;
  }

  .visitors-line-chart-container{
    position: relative;
    border: 1px solid #797979;
    box-shadow: -30px -30px 80px rgba(0, 0, 0, 0.1), 0px 4px 4px rgba(0, 0, 0, 0.25);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 2rem;
  }

  #visitors-line-chart{
    width: 100%;
  }

  .box-history-container{
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
  }

  .box-history{
    background: white;
    color: black;
    height: 5rem;
    width: 5rem;
    margin-left: 1rem;
    margin-bottom: 1rem;

    box-shadow: 20px 20px 70px rgba(85, 82, 134, 0.1), -20px -20px 70px rgba(85, 82, 134, 0.1);
    border-radius: 10px;

    display: flex;
    flex-direction: column;
    padding: 0.5rem;
  }

  .box-history .top{
    flex: 1;
  }
  .box-history .bottom{
    flex: 1;

    text-align: center;

  }

  .box-history .bottom .number{
    color: #555286;
    font-style: normal;
    font-weight: normal;

    font-size: 1rem;
  }

  .box-history .bottom .text{
    align-items: center;
    text-align: center;

    font-size: 0.7rem;

    color: #27DEBF;
  }

  .box-history .top{
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 0.5rem;
  }

  .box-history .top .color-tag{
    flex: 1;
    height: 100%;
    width: 100%;
    background:rgba(85, 82, 134, 0.5);
    border-radius: 50%;
    box-shadow: 2px 4px 4px rgba(85, 82, 134, 0.2);
  }

  .box-history .top .other{
    flex:1;
    align-items: right;
    text-align: right;
  }

  /* Column 2 */

  .column-2{
    padding: 1rem;
    height: 100%;
    overflow: hidden;
    display: grid;
    grid-template-rows: minmax(auto, auto) 1fr minmax(auto, 100px);
  }

  .task-note-header-container{
    display: grid;
    grid-template-columns: 1fr 1fr;
    box-shadow: -30px -30px 80px rgba(0, 0, 0, 0.1), 0px 4px 4px rgba(0, 0, 0, 0.25);
    border-radius: 10px;
    /* padding: 0.5rem; */
    overflow: hidden;
    margin-bottom: 1.5rem;
  }

  .task-note-header{
    display: grid;
    grid-template-rows: 3fr 2fr;
    text-align: center;
    padding: 0.5rem;
  }

  .task-note-header .image{
    background-repeat: no-repeat;
    background-position: center center;
    background-size: contain;
    padding: 0.3rem;
    background-origin: content-box;
  }

  .task-note-header.task .image{
    background-image: url('/png/tasks.png');
  }

  .task-note-header.note .image{
    background-image: url('/png/notes.png');
  }

  .task-note-header.task{
    background: white;
  }

  .task-note-header .text{
    font-size: 1rem;
    font-weight: normal;
    font-style: normal;
  }

  .task-note-header.task .text{
    color: #1F0051;
  }

  .notes{
    height: 100%;
    overflow: hidden;
    overflow-y: auto;
    margin-top: 0.2rem;
  }

  .notes-container{
    background: #250061;
    margin-bottom: 1rem;
  }

  .notes-input-container{
    display: flex;
    flex-direction: column;
  }

  .notes-input-container .notes-input{
    flex: 1;
    display: grid;
    grid-template-columns: 20px 1fr;
    margin-bottom: 0.5rem;
  }

  .notes-input-container .notes-input .colors-column{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    background: white;
  }

  .notes-input-container .notes-input .colors-column .colors-column-item{
    clip-path: circle();
    height: calc((100% / 5) - 2px);
    width: 90%;
    cursor: pointer;
  }

  .notes-input-container .notes-input textarea{
    height: 100%;
    background: white;
  }

  .notes-input-container .button-cotainer{
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .notes-input-container .button-cotainer button {
    border: none;

    background: #27DEBF;
    box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
    border-radius: 40px;

    text-transform: capitalize;
    color: white;
    padding: 0.3rem;
    padding-left: 0.8rem;
    padding-right: 0.8rem;
    font-size: 0.7rem;
  }
</style>
