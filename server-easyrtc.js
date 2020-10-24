// Load required modules
var http    = require("http");              // http server core module
var express = require("express");           // web framework external module
// var serveStatic = require('serve-static');  // serve static files
var socketIo = require("socket.io");        // web socket external module

// This sample is using the easyrtc from parent folder.
// To use this server_example folder only without parent folder:
// 1. you need to replace this "require("../");" by "require("open-easyrtc");"
// 2. install easyrtc (npm i open-easyrtc --save) in server_example/package.json

var easyrtc = require("open-easyrtc"); // EasyRTC internal module

// Set process name
process.title = "aboutcert-easyrtc-server";

// Setup and configure Express http server. Expect a subfolder called "static" to be the web root.
var app = express();
// app.use(serveStatic('static', {'index': ['index.html']}));

// Start Express http server on port 8080
var webServer = http.createServer(app);

// Start Socket.io so it attaches itself to Express server
var socketServer = socketIo.listen(webServer, {"log level":1});

easyrtc.setOption("logLevel", "debug");
// easyrtc.setOption("appAutoCreateEnable", false);

let roomData = {}


// Overriding the default easyrtcAuth listener, only so we can directly access its callback
easyrtc.events.on("easyrtcAuth", function(socket, easyrtcid, msg, socketCallback, callback) {
    easyrtc.events.defaultListeners.easyrtcAuth(socket, easyrtcid, msg, socketCallback, function(err, connectionObj){
        if (err || !msg.msgData || !msg.msgData.credential || !connectionObj) {
            callback(err, connectionObj);
            return;
        }

        connectionObj.setField("credential", msg.msgData.credential, {"isShared":false});

        console.log("["+easyrtcid+"] Credential saved!", connectionObj.getFieldValueSync("credential"));

        callback(err, connectionObj);
    });
});

// To test, lets print the credential to the console for every room join!
easyrtc.events.on("roomJoin", function(connectionObj, roomName, roomParameter, callback) {
    console.log("["+connectionObj.getEasyrtcid()+"] Credential retrieved!", connectionObj.getFieldValueSync("credential"));
    easyrtc.events.defaultListeners.roomJoin(connectionObj, roomName, roomParameter, callback);
});

easyrtc.events.on("roomLeave", function(connectionObj, roomName, roomParameter, callback) {
    if(roomData[roomName] && roomData[roomName][connectionObj.getEasyrtcid()])
        delete roomData[roomName][connectionObj.getEasyrtcid()]
    easyrtc.events.defaultListeners.roomLeave(connectionObj, roomName, roomParameter, callback);
});


easyrtc.events.on("easyrtcMsg", function(connectionObj, msg, socketCallback, next) {
    // console.log(connectionObj)
    console.log("===============================>")
    console.log("===============================>")
    console.log("===============================>")
    console.log("===============================>")
    console.log("===============================>")
    console.log("===============================>")
    console.log("===============================>")
    console.log(msg)

    let msgType = msg.msgType
    let msgData = msg.msgData
    let easyrtcid = connectionObj.getEasyrtcid()

    // Joining room successful
    if(msgType == 'room_join'){
        if(!roomData[msgData.roomName]){
            roomData[msgData.roomName] = {}
        }
        if(!roomData[msgData.roomName][easyrtcid]){
            roomData[msgData.roomName][easyrtcid] = {}
        }
        Object.assign(roomData[msgData.roomName][easyrtcid], {userData: msgData.userData, easyrtcid: easyrtcid})
        console.log("AaAAaaaaaaaaaaaaaaaaaaaaaaaaaa")
        console.log(roomData)
        socketCallback({msgType: "success",  msgData: "success"})
        return
    }

    // Reetrieving room users
    if(msgType == 'room_users'){
        socketCallback({msgType: "success",  msgData: roomData[msgData.roomName] || {}})
        return
    }
    easyrtc.events.defaultListeners.easyrtcMsg(connectionObj, msg, socketCallback, next);
});



// Start EasyRTC server
var rtc = easyrtc.listen(app, socketServer, null, function(err, rtcRef) {
    console.log("Initiated");

    rtcRef.events.on("roomCreate", function(appObj, creatorConnectionObj, roomName, roomOptions, callback) {
        console.log("roomCreate fired! Trying to create: " + roomName);

        appObj.events.defaultListeners.roomCreate(appObj, creatorConnectionObj, roomName, roomOptions, callback);
    });
});

// Listen on port 8080
webServer.listen(8080, function () {
    console.log('listening on port 8080');
});


// easyrtc.events.on("easyrtcMsg", function(connectionObj, msg, socketCallback, next) {
//     console.log("dsafsadsfdsfdfsfdfsdfsddfssdfsda-------------------------------------")
//     console.log("=================> Connection Object")
//     console.log(connectionObj)
//     console.log("====================> message")
//     console.log(msg)
//     console.log("====================> SocketCallback" + socketCallback)
//     console.log(socketCallback)
//     // if(msgType == "something_disgusting")
//     //     console.log("============================================================================"+ msgType)

//     dataToShip = {
//         msgType: "setUserCfg",
//         msgData: {
//             // setUserCfg: {
//                 apiField2: {
//                     customFields : {
//                         id: "some-fucking-id"
//                     }
//                 }
//             // }
//         }
//     }

//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")
//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")
//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")
//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")
//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")

//     // connectionObj.events.defaultListeners.easyrtcCmd(connectionObj, dataToShip, socketCallback, next)
//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")
//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")
//     console.log("^6^6^^^^^^^6^^^^^^^6^^^^^^^^6^^^^^^^^^^^^^^^^^^")
//     easyrtc.events.defaultListeners.easyrtcMsg(connectionObj, msg, socketCallback, next);
//     next(null)
// });
