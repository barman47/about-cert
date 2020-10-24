<template>
    <div class="chat-message">
        <div class="left" v-if="left == true">
            <div class="display-photo-container">
                <div class="display-photo" :style="{backgroundImage: 'url(\''+ profilePhoto +'\')'}"></div>
            </div>
            <div class="message-content">
                <p>{{text}}</p>
            </div>
        </div>

        <div class="right" v-if="left == false">
            <div class="message-content">
                <p>{{text}}</p>
            </div>
            <div class="display-photo-container">
                <div class="display-photo" :style="{backgroundImage: 'url(\''+ profilePhoto +'\')'}"></div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["left", "text", "thumbnail"],
    computed: {
        profilePhoto(){
            return process.env.BACKEND_BASE_URL.replace(/\/+$/, '') + '/' + (this.thumbnail || 'man-avatar-profile-icon.jpg').replace(/^\/+/, '')
        }
    }
}
</script>

<style scoped>
    .chat-message{
        margin-top: 10px;
        margin-bottom: 10px;
    }
    .left{
        display: grid;
        grid-template-columns: 60px 1fr;
    }

    .right{
        display: grid;
        grid-template-columns: 1fr 60px;
    }

    .display-photo-container{
        display: flex;
        align-items: flex-start;
        justify-content: flex-start;
    }

    .right .display-photo-container{
        justify-content: flex-end;
    }

    .display-photo{
        height: 35px;
        width: 35px;
        border-radius: 50%;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center center;
        position: relative;
    }

    .display-photo::before{
        content: "";
        position: absolute;
        top: -3px;
        bottom: -3px;
        right: -3px;
        left: -3px;
        border-radius: 50%;
    }

    .left .display-photo::before{
        border: #0084ff solid 1.2px;
    }

    .right .display-photo::before{
        border: #24ff00 solid 1.2px;
    }

    .message-content{
        background: #ecfcff;
        color: #9b9b9b;
        font-size: 0.9rem;
        line-height: 1rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
        position: relative;
        border-bottom-right-radius: 5px;
        border-bottom-left-radius: 5px;
    }

    .message-content p{
        line-break: anywhere;
    }

    .left .message-content{
        border-top-right-radius: 5px;
        border-top-left-radius: 0;
    }

    .right .message-content{
        border-top-right-radius: 0;
        border-top-left-radius: 5px;
    }

    .left .message-content::before{
        content: "";
        position: absolute;
        top: 0;
        left: -10px;
        border-top: none;
        border-right: 10px solid #ecfcff;
        border-bottom: 10px solid transparent;
        border-left: none;
    }

    .right .message-content::before{
        content: "";
        position: absolute;
        top: 0;
        right: -10px;
        border-top: none;
        border-left: 10px solid #ecfcff;
        border-bottom: 10px solid transparent;
        border-right: none;
    }
</style>