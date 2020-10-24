<template>
    <div class="opportunity-card">
        <div class="like-container" @click="like()" :data-like-count="!!opportunity.likes_count ? opportunity.likes_count : ''">
            <div class="like" :style="'background-image: url(\'/png/like '+(!!opportunity.liked ? '2' : '1')+'.png\')'"></div>
            </div>
        <div class="header"><span>{{opportunity.title}}</span> </div>
        <div class="body">{{opportunity.content}}</div>
        <div class="activity">
            <div>
                <div></div>
            </div>
            <div>
                <div></div>
                <button @click="visitSite()">Visit Site</button>
            </div>
        </div>
        <input type="text" class="input-field">
    </div>
</template>

<script>
export default {
    props: ["opportunity"],
    methods: {
        visitSite(){
            let link = this.opportunity.link
            if(!link.startsWith("http://") || !link.startsWith("https://"))
                link = "http://" + link

            window.open(link, "_blank")
                
        },
        like(){
            const data = {
                id: this.opportunity.id,
                type: "opportunity"
            }

            this.$store.dispatch("opportunities/like", data)
        }
    }
}
</script>

<style scoped>
    .opportunity-card{
        background: white;
        display: grid;
        grid-template-rows: calc(1rem + 40px) 1fr 50px 50px;
        border-radius: 5px;
        
        padding: 0;
        row-gap: 10px;
        /* overflow: hidden; */
        position: relative;
    }

    .header{
        border-bottom: 1px solid #ddd;
        color: black;
        width: 100%;
        overflow-x: hidden;
        font-size: 1.2rem;
        font-weight: bold;
        font-style:normal;
        white-space: nowrap;
        text-overflow: ellipsis;
        padding: 1rem;
        padding-right: 5rem;
    }

    .body{
        padding-top: 1rem;
        padding-bottom: 1rem;
        color: #757474;
        line-height: 1.5rem;
        padding-left: 1rem;
        padding-right: 1rem;
        /* height: 100%; */
        max-height: 300px;
        overflow: hidden;
        overflow-y: auto;
    }

    .like-container{
        position: absolute;
        top: -0.75rem;
        right: 2rem;
        cursor: pointer;
    }

    .like{
        height: 1.5rem;
        width: 1.5rem;
        background-color: transparent;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center center;
        position: relative;
        z-index: 1;
    }

    .like-container::before{
        content: "";
        position:absolute;
        top: -0.5rem;
        right: -0.5rem;
        bottom: -0.5rem;
        left: -0.5rem;
        background: #d1d1d1;
        border-radius: 50%;
    }

    .like-container[data-like-count]::after{
        content: attr(data-like-count);
        position:absolute;
        bottom: -1.5rem;
        left: 0;
        width: 100%;
        display: flex;
        align-content: center;
        justify-content: center;
        font-size: 0.9rem;
        line-height: 1rem;
        color: #9b9b9b;
    }

    .input-field{
        width: 100%;
        border: none;
        border-top: 1px solid #ddd;
        padding-left: 1rem;
        padding-right: 1rem;
        text-indent: 10px;
        padding-right: calc(1rem + 50px );
        color: #9b9b9b;
        font-size: 0.9rem;
        background: url("/png/smiley.png") no-repeat right 20px center;
    }

    .input-field:focus{
        outline: none;
        
    }
    
    .activity{
        display: flex;
        align-items: center;
        padding-left: 1rem;
        padding-right: 1rem;
    }

    .activity > div{
        flex: 1;
    }

    .activity > div:first-child > div:first-child{
        background-image: url("/png/info.png");
        background-repeat: no-repeat;
        background-position: left center;
        height: 1.2rem;
        background-size: contain;
        cursor: pointer;
        width: 2rem;
    }

    .activity > div:nth-child(2){
        display: flex;
        align-items: center;
        justify-content: flex-end;
    }
    .activity > div:nth-child(2) > div {
        margin-left: 5px;
    }

    .activity > div:nth-child(2) > div:first-child{
        background-image: url("/png/share.png");
        background-repeat: no-repeat;
        height: 1.2rem;
        width: 1.2rem;
        background-size: contain;
        cursor: pointer;
    }

    .activity > div:nth-child(2) > div:first-child:hover{
        background-image: url("/png/share 2.png");
        background-color: #ccedff;
        position: relative;
        height: 1.5rem;
        width: 1.5rem;
        border-radius: 50%;
    }

    .activity > div:nth-child(2) > button{
        height: 2rem;
        font-size: 0.8rem;
        border: 1.5px solid #0084ff;
        border-radius: 1rem;
        color: #0084ff;
        background: white;
        padding-left: 1rem;
        padding-right: 1rem;
        cursor: pointer;
        margin-left: 1rem;
    }

    @media (max-width: 767.98px) { 
        .opportunity-card{
            grid-template-rows: calc(1rem + 40px) 1fr 50px 50px;
        }
    }
</style>