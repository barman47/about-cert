import io from 'socket.io-client'
// console.log(process)
const socket = io(process.env.WS_URL)
// const socket = io()
// console.log("Socket")
// console.log(socket)
export default socket