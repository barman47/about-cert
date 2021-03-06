import http from 'http'
import socketIO from 'socket.io'

export default function () {
  this.nuxt.hook('render:before', (renderer) => {
    const server = http.createServer(this.nuxt.renderer.app)
    // const io = socketIO(server)

    // overwrite nuxt.server.listen()
    // this.nuxt.server.listen = (port, host) => new Promise(resolve => server.listen(port || 3000, host || 'localhost', resolve))
    // close this server on 'close' event
    this.nuxt.hook('close', () => new Promise(server.close))
    // Add socket.io events
    // const messages = []
    // io.on('connection', (socket) => {
    //   console.log("A user connected")
      
    //   setTimeout(() => {
    //       socket.send("This is a message after 4 seconds")
    //   }, 4000);

    //   socket.on("disconnect", () => {
    //       console.log("A user just disconnected")
    //   })

    //   socket.on("send-message", (data) => console.log(data))
    // })
  })
}