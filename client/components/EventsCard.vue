<template>
    <div class="events-card">
        <div class="left" @click="goToEvent()">
            <div class="image" :style="{backgroundImage: 'linear-gradient(to bottom, #eee, #9d9d9d), url(\''+eventPhoto+'\')'}"></div>
        </div>
        <div class="right">
            <h4 class="title" @click="goToEvent()" >{{event.title}}</h4>
            <span class="date">{{date}}</span>
            <div>
                <span class="date-posted-title">Date Posted: </span> &nbsp; <span class="date-posted">{{dateCreated}}</span>
            </div>
            <div>
                <span class="time-posted-title">Time: </span> &nbsp; <span class="time-posted">{{timeCreated}}</span>
            </div>
            <div class="like-container m-top">
                <span class="like" style="background-image: url('/png/like 3.png')"></span> {{event.likes_count}}
            </div>
            <div class="like-container">
                <span class="like" style="background-image: url('/png/watch 3.png')"></span> {{event.watching_count}}
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["event"],
    methods: {
        goToEvent(){
            return this.$nuxt.$router.push("/updates/" + this.event.id)
        }
    },
    computed: {
        date(){
            return (new Date(this.event.time)).toDateString()
        },
        dateCreated(){
            return (new Date(this.event.created_at)).toDateString()
        },
        timeCreated(){
            let date = new Date(this.event.created_at)
            return date.getHours() + ":" + date.getMinutes()
        },
        eventPhoto(){
            return process.env.BACKEND_BASE_URL + (this.event.img ? this.event.img : "test.jpg")
        }
    }
}
</script>

<style scoped>
.events-card {
    display: grid;
    grid-template-columns: 7fr 5fr;
    border-radius: 10px;
    overflow: hidden;
    background: white;
}

.left{
    /* min-height: 350px; */
    background: linear-gradient(to bottom, #eee, #9d9d9d);
    position: relative;
}

.left .image{
    top: 0.5em;
    right: 0.5em;
    bottom: 0.5em;
    left:0.5em;
    background-size: contain;
    background-repeat: no-repeat;
    position: absolute;
    z-index: 1;
    background-position: center center;
    cursor: pointer;

    background-color: linear-gradient(to bottom, #eee, #9d9d9d);
    background-blend-mode: darken;
}

.right{    
    padding: 2rem 1rem 2rem 1rem;
    display: flex;
    flex-direction: column;
}

.title{
    margin: 0.5rem 0 0.5rem 0;
    font-size: 1rem;
    font-style: normal;
    font-weight: 500;
    cursor: pointer;
}

.date{
    font-size: 0.8rem;
    font-weight: normal;
    font-style: normal;
    color: #0084ff;
    display: block;
    margin-bottom: 1rem;
}

.date-posted-title{
    font-weight: 300;
    font-size: 0.8rem;
    color: #c4c4c4;
}

.date-posted{
    font-weight: 300;
    font-size: 0.8rem;
    color: #4ed6d6;
}

.time-posted-title{
    font-weight: 300;
    font-size: 0.8rem;
    color: #c4c4c4;
}

.time-posted{
    font-weight: 300;
    font-size: 0.8rem;
    color: #4ed6d6;
}

.like-container{
    display: grid;
    grid-template-columns: 2rem 1fr;
    color: #c4c4c4;
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}

.like-container.m-top{
    margin-top: 1rem;
}

.like-container .like{
    width: 100%;
    height: 100%;
    background-size: contain;
    background-position: center center;
    background-repeat: no-repeat;
}

@media (max-width: 575.98px) { 
    .events-card {
        grid-template-columns: 7fr 5fr;
    }
}
</style>