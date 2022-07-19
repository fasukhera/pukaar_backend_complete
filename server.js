var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
var Redis = require('ioredis');
var redis = new Redis();
var users = [];



http.listen(8005, function () {
    console.log('Listening to port 8005');
});

redis.subscribe('private-channel', function() {
    console.log('subscribed to private channel');
});

redis.subscribe('notification-channel', function() {
    console.log('subscribed to notification channel');
});

redis.subscribe('session-buying-channel', function() {
    console.log('subscribed to session-buying-channel channel');
});

redis.subscribe('session-donated-channel', function() {
    console.log('subscribed to session-donated-channel channel');
});

redis.on('message', function(channel, message) {
    message = JSON.parse(message);
    console.log(message);
    if (channel == 'private-channel') {
        let data = message.data.data;
        let sender_id = data.sender_id;
        let content = data.content;
        let created_at = data.created_at;
        let sender_name = message.data.data.sender_name;
        let reciever_id = data.reciever_id;
        let event = message.event;
        let message_id = data.message_id;
        io.to(`${users[reciever_id]}`).emit(channel + ':' + message.event, data);
    }

    if (channel == 'notification-channel') {
        let data = message.data.data;
        let sender_id = data.sender_id;
        let sender_name = data.sender_name;
        let reciever_id = data.reciever_id;
        let event = message.event;
        io.to(`${users[reciever_id]}`).emit(channel + ':' + message.event, data);
    }

    if (channel == 'session-donated-channel') {
        let data = message.data.data;
        let reciever_id = data.reciever_id;
        let event = message.event;
        io.to(`${users[reciever_id]}`).emit(channel + ':' + message.event, data);
    }

    if (channel == 'session-buying-channel') {
        let data = message.data.data;
        let reciever_id = data.reciever_id;
        let event = message.event;
        io.to(`${users[reciever_id]}`).emit(channel + ':' + message.event, data);
    }
});

io.on('connection', function (socket) {
    socket.on("user_connected", function (user_id) {
        users[user_id] = socket.id;
        console.log("User" +user_id+ "socket_id" +socket.id);
        io.emit('updateUserStatus', users);
        console.log("user connected "+ user_id);
    });

    socket.on('disconnect', function() {
        var i = users.indexOf(socket.id);
        users.splice(i, 1, 0);
        io.emit('updateUserStatus', users);
        console.log(users);
    });
});
redis.subscribe('session-channel', function() {
    console.log('subscribed to session channel');
});

redis.on('message', function(channel, message) {
    message = JSON.parse(message);
    console.log(message);
    if (channel == 'private-channel') {
        let data = message.data.data;
        let sender_id = data.sender_id;
        let content = data.content;
        let created_at = data.created_at;
        let sender_name = message.data.data.sender_name;
        let reciever_id = data.reciever_id;
        let event = message.event;
        let message_id = data.message_id;
        io.to(`${users[reciever_id]}`).emit(channel + ':' + message.event, data);
    }

    if (channel == 'notification-channel') {
        let data = message.data.data;
        let sender_id = data.sender_id;
        let sender_name = data.sender_name;
        let reciever_id = data.reciever_id;
        let event = message.event;
        io.to(`${users[reciever_id]}`).emit(channel + ':' + message.event, data);
    }

    if (channel == 'session-channel') {
        let data = message.data.data;
        let reciever_id = data.reciever_id;
        io.to(`${users[reciever_id]}`).emit(channel + ':' + message.event, data);
    }
    if (channel == 'session-response-channel') {
        let data = message.data.data;
        let client_id = data.client_id;
        io.to(`${users[client_id]}`).emit(channel + ':' + message.event, data);
    }
    if (channel == 'change-therapist-req-channel') {
        let data = message.data.data;
        let admin_id = data.Admin;
        io.to(`${users[admin_id]}`).emit(channel + ':' + message.event, data);
    }

    if (channel == 'change-therapist-res-channel') {
        let data = message.data.data;
        let client_id = data.client_id;
        io.to(`${users[client_id]}`).emit(channel + ':' + message.event, data);
    }
});

redis.subscribe('change-therapist-req-channel', function() {
    console.log('subscribed to change therapist request channel');
});

redis.subscribe('change-therapist-res-channel', function() {
    console.log('subscribed to change therapist response channel');
});



