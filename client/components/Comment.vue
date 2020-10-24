<template>
    <div class="comment-container">
        <!-- <div v-if="comment.comments && comment.comments.length > 0" class="comment-container-before"></div> -->
        <div class="comment" :class="{self: comment.user.id == $auth.user.id}">
            <div class="display-photo-container">
                <div class="display-photo" :style="{backgroundImage: 'url(\''+formatProfileImage(comment.user.thumbnail)+'\')'}"></div>
            </div>
            <div class="content-container">
                <div class="content">
                    <span class="name">{{comment.user.name}}</span>
                    <span>{{comment.content}}</span>
                    <div v-if="hasCommentsGreaterThanTwo">
                        <div class="comment c2" v-for="c in childCommentsToDisplay" :key="c.id">
                            <div>
                                <div class="display-photo" :style="{backgroundImage: 'url(\''+formatProfileImage(c.user.thumbnail)+'\')'}"></div>
                            </div>
                            <div class="content">{{c.content}}</div>
                        </div>
                    </div>
                    <div v-if="needsExpandButton" @click="expand()" class="expand-comment-button-container">
                        <div class="expand-comment-button"></div>
                    </div>
                    <div class="actions-container">
                        <div class="replies"  v-if="comment.user.id == $auth.user.id"><img src="/png/comment-replies-icon-self.png" class="image"/><span>{{comment.comments_count}}</span></div>
                        <div class="replies" v-else><img src="/png/comment-replies-icon.png" class="image"/><span>{{comment.comments_count}}</span></div>
                        <div @click="like()" class="likes"><img :src="'/png/comment-like' + (comment.liked == 1 ? '-active' : '') + '.png'" class="image"/><span>{{comment.likes_count}}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["comment", "post_id"],
    data(){
        return {
            processingCommentLike: false,
            expanded: false,

        }
    },
    computed: {
        hasCommentsGreaterThanTwo(){
            return this.comments.length > 2
        },
        needsExpandButton(){
            return this.comment.comments_count - this.childCommentsToDisplay.length > 0
        },
        comments(){
            return this.comment.comments || []
        },
        childCommentsToDisplay(){
            let comments = this.comments
            return this.expanded ? this.comments : this.comments.slice(0, 2)
        }
    },
    methods: {
        formatProfileImage(img){
            return process.env.BACKEND_BASE_URL.replace(/\/+$/, "") + "/" + (img ? img : "/man-avatar-profile-icon.jpg").replace(/^\/+/, "")
        },
        like(){
            const data = {
                type: "comment",
                id: this.comment.id,
                post_id: this.post_id,
            }

            if(this.processingCommentLike)
                return

            this.processingCommentLike = true
            let attempt = this.$store.dispatch("updates/like", data)
            attempt.finally(() => {
                this.processingCommentLike = false
            })

        },
        expand(){
            this.expanded = true
        }
    }
}
</script>

<style scoped>
    .comment-container{
        overflow:hidden;
        position: relative;
        /* background: rgba(225,0,0, 0.25); */
        display: flex;
        flex-direction: column;
        margin-bottom: 10px;
    }

    .dark-theme .comment-container{
        border-color: #929292
    }

    /* .comment-container .comment-container-before{
        content: "";
        width: 2px;
        background: #9b9b9b;
        position: absolute;
        left: calc(4.25rem - 2px /2);
        top: 1rem;
        bottom: 0;
    } */
    .comment{
        display: grid;
        grid-template-columns: 3rem 1fr;
        margin: 0.25rem;
        /* border: solid 1px #333; */
        position: relative;
        z-index: 2;
    }

    .comment.self {
        grid-template-columns: 1fr 3rem;
    }

    .comment.c2{
        /* padding-left: 2.5rem; */
        position: relative;
        z-index: 2;
    }

    .comment.c2 .content {
        max-width: 100%;
        width: 100%;
        border: none;
    }

    .comment.self .display-photo-container{
        order: 1;
    }

    .comment .display-photo{
        height: 2rem;
        width: 2rem;
        border-radius: 50%;
        background-size: cover;
        background-repeat: no-repeat;
        margin: 0.5rem;
        position:relative;
    }

    /* .comment .display-photo::after{
        content: "";
        position: absolute;
        top: -4px;
        bottom: -4px;
        left: -4px;
        right: -4px;
        z-index: -1;
        background: white;
        border-radius: 50%;
        border: 2px rgba(0, 132, 255, 0.4) solid;
    } */

    .comment .content-container {
        display: flex;
    }

    .comment.self .content-container {
        justify-content: flex-end;
    }

    .comment .content{
        /*background: white;;*/
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: #9b9b9b;
        padding-bottom: 0.5rem;
        word-break: break-word;

        display: inline-flex;
        flex-direction: column;
        max-width: 70%;
        padding: 0.5rem;
        border-radius: 0 14px 14px 14px;
        border: solid 1px #c4c4c4;
    }


    .comment.self .content{
        border-radius: 14px 0 14px 14px;
        background: #0084ff;
        color:white;
    }

    .comment .name{
        color: black;
        margin-bottom: 0.5rem;
    }

    .comment.self .name{
        color:white;
    }

    .dark-theme .comment .name {
        color: white;
    }

    .actions-container {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        margin-top: 0.8rem;
        height: 1rem;
        overflow: hidden;
    }

    .actions-container > *{
        margin-left: 1rem;
        font-size: 0.7rem;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .actions-container > * .image {
        object-fit: contain;
        max-height: 100%;
        height: 100%;
        margin-right: 5px;
    }

    .replies, .likes {
        cursor: pointer;
    }

    /* .comment-container .comment.c2:last-child:before{
        content: "";
        top: 1.5rem;
        left: 0;
        bottom: -1rem;
        background: white;
        width: 100%;
        position:absolute;
        z-index: -1;
    } */

    .expand-comment-button-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0.5rem;
        --expand-button-height: 30px;
    }

    .expand-comment-button {
        background: url("/png/comment-expand-button.png");
        background-position: center center;
        background-repeat: no-repeat;
        background-size: contain;
        height: var(--expand-button-height);
        width: var(--expand-button-height);
        cursor: pointer;
    }

    /* // Large devices (desktops, less than 1200px) */
    @media (max-width: 1199.98px) {
    }

    /* // Medium devices (tablets, less than 992px) */
    @media (max-width: 991.98px) {
    }

    /* // Small devices (landscape phones, less than 768px) */
    @media (max-width: 767.98px) {
        .comment .content{
            max-width: 80%;
        }
    }

    /* // Extra small devices (portrait phones, less than 576px) */
    @media (max-width: 575.98px) {
        .comment .content{
            max-width: 90%;
        }
    }
    
</style>