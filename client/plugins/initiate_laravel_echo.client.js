import Echo from "laravel-echo"
import Vue from "vue"

let io = require('socket.io-client')

export default ({ env, store }, inject) => {
    store.dispatch("laravel_echo/initializeLaravelEcho")
}