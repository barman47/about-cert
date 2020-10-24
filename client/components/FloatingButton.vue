<template>
    <div class="button" @click="$emit('clicked')">
        <div class="text" ><slot></slot></div>
        <div class="image-container">
            <div class="image" :style="{'background-image': 'url(\'' + image + '\')'}"></div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["text", "image"]
}
</script>

<style scoped>
    .button{
        height: 40px;
        border-radius: 20px;
        background: #0084ee;
        color: white;
        display: flex;
        align-items: center;
        font-size: 0.9rem;
        font-weight: normal;
        /* padding: 5px; */
        padding-left: 1rem;
        box-shadow: 0px 4px 4px rgba(0, 132, 255, 0.25);
        cursor: pointer;
    }

    .text{
        flex: 1;
    }

    .image-container{
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: white;
        margin: 3px;
        margin-left: 5px;
        padding: 8px;
    }

    .image {
        height: 100%;
        width: 100%;
        background-size: contain;
        /* clip-path: circle(); */
        border-radius: 50%;
    }
</style>