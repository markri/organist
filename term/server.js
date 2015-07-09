#!/usr/bin/env node

/**
 * @todo
 *  - store output (stream is already there)
 *  - execute command by fetching it from mysql
 *  - embed in organist
 */

var http = require('http')
    , express = require('express')
    , io = require('socket.io')
    , pty = require('pty.js')
    , terminal = require('term.js')
    ;

var fs = require('fs');
var commandIds = [];
var terms = [];
var buff = [];
var stream;
var port = 8080;

process.title = 'term.js';

if (process.argv[2] === '--dump') {
    stream = require('fs').createWriteStream(__dirname + '/dump.log');
}

// Create express app
var app = express();
var server = http.createServer(app);

// Disable any form of browser caching
app.use(function(req, res, next) {
    var setHeader = res.setHeader;
    res.setHeader = function(name) {
        switch (name) {
            case 'Cache-Control':
            case 'Last-Modified':
            case 'ETag':
                return;
        }
        return setHeader.apply(res, arguments);
    };
    next();
});


app.use(terminal.middleware());


// Set port
server.listen(port);


// Disable logging
io = io.listen(server, {
    log: false
});

/**
 * Used for creating right HTML client which will request same URL in result with appropiate socket.io connection headers
 */
app.get('/cmd_:commandid', function(req, res) {
    //console.log(req.params.commandid);
    //res.send();
    commandName = 'cmd_' + req.params.commandid;
    commandId = req.params.commandid;

    var nsp = io.of('/' + commandName);
    nsp.on('connection', function (socket) {

        // singleton createterm
        term = createTerm(commandId, socket, nsp);
    });

    fs.readFile(__dirname + '/client.html', 'utf8', function(err, data) {

        var clientHtml = data;
        clientHtml = clientHtml.replace('{{command_name}}', commandName );
        clientHtml = clientHtml.replace('{{hostname}}', req.hostname );
        clientHtml = clientHtml.replace('{{port}}', port );
        clientHtml = clientHtml.replace('{{proto}}', req.protocol );
        res.send(clientHtml);
    });

});


/**
 *
 * @param commandId
 * @param socket
 * @param nsp
 * @returns term
 */
function createTerm(commandId, socket, nsp) {

    if (commandIds.indexOf(commandId) >= 0) {
        var term = terms[commandIds.indexOf(commandId)];

        // dont reinitialize term, and leave original binded socket for command entry
        return term;
    }

    //socket.join(req.params.commandid);
    term = pty.fork(process.env.SHELL || 'sh', [], {
        name: require('fs').existsSync('/usr/share/terminfo/x/xterm-256color')
            ? 'xterm-256color'
            : 'xterm',
        cols: 120,
        rows: 40,
        cwd: process.env.HOME
    });

    //console.log(''
    //    + 'Created shell with pty master/slave'
    //    + ' pair (master: %d, pid: %d)',
    //    term.fd, term.pid);


    term.on('data', function (data) {
        if (stream) stream.write('OUT: ' + data + '\n-\n');
        return !socket
            ? buff.push(data)
            : nsp.emit('data', data);
    });


    socket.on('data', function (data) {
        if (data === '\u0003') {
            // only allow CTRL-C
            term.write(data);
        }
    });

    socket.on('disconnect', function () {

        //console.log(''
        //    + 'Terminatd original shell'
        //    + ' pair (master: %d, pid: %d)',
        //    term.fd, term.pid);

        socket = null;

        // remove terminal from array
        var index = commandIds.indexOf(commandId);
        terms.splice(index, 1);
        commandIds.splice(index, 1);
    });

    while (buff.length) {
        // If terminal is started earlier than client is initiated
        nsp.emit('data', buff.shift());
    }

    // Add terminal to local array
    commandIds.push(commandId);
    terms.push(term);

    executeCommand(term, commandId);

    return term;
}


function executeCommand(term, commandId){
    var mysql = require('mysql');

    //@todo get parameters from config file
    var connection = mysql.createConnection({
        host     : 'localhost',
        user     : 'root',
        password : 'vagrant',
        database : 'organist'
    });

    connection.query('SELECT * FROM commandLog WHERE id = ' + commandId, function(err, rows) {

        command = rows[0].command;

        if (!err) {
            term.write(command);
        }
    });

    //connection.end();
}
