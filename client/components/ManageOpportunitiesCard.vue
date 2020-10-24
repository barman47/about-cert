<template>
    <div class="opportunity-card">
       <div class="left">
           <h4 class="header">{{opportunity.title}}</h4>
           <div class="body">
               <p>{{opportunity.content}}</p>
           </div>
           <div>
               <div class="delete-button"></div>
           </div>
       </div>
       <div class="right">
           <div>
               <div class="image watch"></div>
               <span>20</span>
           </div>
           <div>
               <div class="image like"></div>
               <span>{{opportunity.likes_count}}</span>
           </div>
           <div>
               <div class="image share"></div>
               <span>20</span>
           </div>
       </div>
    </div>
</template>

<script>
export default {
    props: ["opportunity"]
}
</script>

<style scoped>
    .opportunity-card{
        --right-size: 100px;
        --max-body-height: 300px;
        display: grid;
        grid-template-columns: 1fr var(--right-size);
        grid-gap: 10px;
    }

    .left{
        background: #3A2C51;
        border-radius: 10px;
        padding: 1rem;
    }

    .body{
        max-height: var(--max-body-height);
        overflow-y: auto;
    }

    .delete-button{
        background: url("/png/delete.png");
        /* width: 1.5rem; */
        height: 1.5rem;
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: contain;
        cursor:pointer;
    }

    .header{
        color: white;
        margin-bottom: 0.5rem;
        line-height: 1.6rem;
        margin-top: 0.5rem;
    }

    .body > p{
        color: #acacac;
        font-style: normal;
        font-weight: 300;
        font-size: 0.8rem;
        line-height: 1.4rem;
    }

    /* Right */
    .right{
        background: transparent;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        position: relative;
    }

    .right::before{
        content: "";
        position:absolute;
        top: 0;
        bottom: 0;
        z-index: 0;
        width: 3px;
        left: calc(50% - 3px/2);
        background: white;
    }

    .right > div{
        height: calc(var(--right-size) * 0.8);
        width: calc(var(--right-size) * 0.8);
        background: white;
        border-radius: 5px;
        z-index: 1;
        padding: 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-around;
    }

    .like{
        background-image: url("/png/like 4.png");
    }

    .share{
        background-image: url("/png/share 4.png");
    }
    .watch{
        background-image: url("/png/watch 4.png");
    }


    .right > div .image{
        background-size: contain;
        background-position: center center;
        /* height: 30px; */
        height: calc(var(--right-size) * 0.3);
        background-size: contain;
        width: 100%;
        background-repeat: no-repeat;
    }

    .right > div > span{
        font-size: 0.8rem;
        color: #9b9b9b;
    }

    @media (max-width: 991.98px) { 
        .opportunity-card{
            --right-size: 80px;
        }

        .body{
            --max-body-height: 200px;
        }
    }

    @media (max-width: 767.98px) {
    }

    @media (max-width: 575.98px) {
        .opportunity-card{
            --right-size: 60px;
        }
    }
</style>