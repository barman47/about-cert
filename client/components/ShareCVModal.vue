<template>
    <div class="share-document-modal">
        <div class="modal">
            This is the share document modal
            <div class="search-in-app">
                <h5 class="header">Search in app</h5>
                <div class="search-in-app-input-group">
                    <input type="text" placeholder="Search User Here">
                    <span class="button-container">
                        <button></button>
                    </span>
                </div>

                <div class="search-result">
                    <div class="user-strip" v-for=" i in 5" :key="i">
                        <div class="profile-photo-container">
                            <div class="profile-photo" :style="{backgroundImage: backgroundPhoto('woman-avatar-profile-icon.jpg')}"></div>
                        </div>
                        <div class="name-container">
                            <div class="name">Adedoyin Silas Oluwatobiloba Oluwatobiloba Oluwatobiloba</div>
                        </div>
                        <div class="share-button-container">
                            <button></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        backgroundPhoto(val){
            return `url('${process.env.BACKEND_BASE_URL + val}')`
        }
    }
}
</script>

<style scoped>
    .share-document-modal{
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        align-items:center;
        z-index: 999;
        background: rgba(0,0,0, 0.5);
    }

    .share-document-modal .modal{
        border-radius: 10px;
        min-width: 40%;
        max-height: calc(var(--viewport-height, 100vh) - 100px);
        background: #F0F0F0;
        padding: 1rem;
    }

    .search-in-app .header{
        font-weight: bold;
        color: black;
    }

    .search-in-app .search-in-app-input-group{
        --search-height: 2rem;
        --search-padding: 0.2rem;
        height: var(--search-height);
        display: flex;
        align-items: center;
    }
    .search-in-app .search-in-app-input-group input,
    .search-in-app .search-in-app-input-group .button-container{
        height: 100%;
        margin: 0;
        border: 1px solid #9B9B9B;
        border-radius: 5px;
    }

    .search-in-app .search-in-app-input-group input{
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-right: 0;
        background: white;
        padding-left: 0.2rem;
        padding-right: 0.2rem;
    }

    .search-in-app .search-in-app-input-group .button-container{
        border-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px;
    }

    .search-in-app .search-in-app-input-group button{
        width: calc(var(--search-height) - var(--search-padding) - 2px);
        height: calc(var(--search-height) - var(--search-padding) - 2px);
        border-radius: 3px;
        border:none;
        margin: 0;
        background: #0084ff;
    }

    .search-result{
        --search-result-arrow-height: 1.5rem;
        margin-top: var(--search-result-arrow-height);
        border-radius: 5px;
        position: relative;
    }

    .search-result::before{
        content: "";
        border-top: 0;
        border-right: var(--search-result-arrow-height) solid transparent;
        border-left: var(--search-result-arrow-height) solid transparent;
        border-bottom: var(--search-result-arrow-height) solid white;
        position: absolute;
        left: 1rem;
        bottom: 100%;

    }
    .search-result .user-strip{
        display: grid;
        grid-template-columns: 50px 1fr 50px;
        height: 50px;

    }
    .profile-photo-container{
        padding: 0.4rem;
    }

    .profile-photo-container .profile-photo{
        height: 100%;
        width: 100%;
        clip-path: circle();
        background-size: cover;
        background-repeat: no-repeat;
    }

    .search-result{
        background: white;
    }

    .name-container{
        display: flex;
        align-items: center;
        padding-left: 0.2rem;
        padding-left: 0.2rem;
        overflow: hidden;
    }

    .name-container .name{
        width: 100%;
        overflow: hidden;
        color: black;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 0.7rem;
        font-weight: bold;
    }
</style>