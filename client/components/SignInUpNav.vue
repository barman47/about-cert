<template>
    <div class="sign-in-up-nav grid-1-1">
        <a href="#" @click.prevent="goToRegister" :class="{active:$nuxt.$route.name == 'register' }">Sign Up</a>
        <a href="#" @click.prevent="goToLogin" :class="{active:$nuxt.$route.name == 'login' }">Login</a>
    </div>
</template>

<script>
export default {
    methods:{
        goToRegister(){
            if(this.$nuxt.$route.name == "register") return
            console.log(this.$nuxt.$router.push({name: "register"}))
        },
        goToLogin(){
            if(this.$nuxt.$route.name == "login") return
            console.log(this.$nuxt.$router.push({name : "login"}))
        }
    }
}
</script>

<style scoped>
.sign-in-up-nav{
    height: 45px;
    column-gap: 0;
    width: 90%;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

a{
    text-align: center;
    text-decoration: none;
    background: #f8f8f9;
    color: #0084ff;
    border: #0084ff solid 1px;
    display: flex;
    align-items:center;
    justify-content: center;
}

a.active{
    background: #0084ff;
    color: white;
}
</style>