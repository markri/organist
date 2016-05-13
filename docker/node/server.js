#!/usr/bin/env node

/**
 *  - store output (stream is already there)
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
var dumpStream;
var logs = [];
var debug = false;

process.title = 'term.js';

if (process.argv.indexOf('--dump') >= 0) {
    dumpStream = require('fs').createWriteStream(__dirname + '/dump.log');
}

if (process.argv.indexOf('--debug') >= 0) {
    debug = true;
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
server.listen(process.env.npm_package_config_port);


// Disable logging
io = io.listen(server, {
    log: debug
});

/**
 * Used for creating right HTML client which will request same URL in result with appropiate socket.io connection headers
 */
app.get('/cmd_:commandid', function(req, res) {

    commandName = 'cmd_' + req.params.commandid;
    commandId = req.params.commandid;

    var nsp = io.of('/' + commandName);
    nsp.on('connection', function (socket) {

        // singleton createterm
        term = createTerm(commandId, socket, nsp);
    });

    res.send();
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

    // create term
    term = pty.fork(process.env.SHELL || 'sh', [], {
        name: require('fs').existsSync('/usr/share/terminfo/x/xterm-256color')
            ? 'xterm-256color'
            : 'xterm',
        cols: 120,
        rows: 40,
        cwd: process.env.HOME
    });

    if (debug) {
        console.log(''
            + 'Created shell with pty master/slave'
            + ' pair (master: %d, pid: %d)',
            term.fd, term.pid);
    }

    term.on('data', function (data) {
        if (dumpStream) {
            dumpStream.write(data + '\n');
        }

        logs[commandId].push(data);

        if (!socket) {
            return;
        }

        return nsp.emit('data', data);
    });

    // only allow CTRL-C as input
    socket.on('data', function (data) {
        if (data === '\u0003') {
            term.write(data);
        }
    });

    socket.on('disconnect', function () {

        if (debug) {
            console.log(''
                + 'Terminated original shell'
                + ' pair (master: %d, pid: %d)',
                term.fd, term.pid);
        }

        socket = null;

        // remove terminal from array
        var index = commandIds.indexOf(commandId);
        terms.splice(index, 1);
        commandIds.splice(index, 1);

        saveOutput(commandId, logs[commandId]);

        //@todo check does it kill the term in processlist?
    });

    // Add terminal to local array
    commandIds.push(commandId);
    terms.push(term);
    logs[commandId] = [];

    executeCommand(term, commandId);

    return term;
}


function saveOutput(commandId, output)
{
    var mysql = require('mysql');

    var connection = mysql.createConnection({
        host     : process.env.npm_package_config_dbhost,
        user     : process.env.npm_package_config_dbuser,
        password : process.env.npm_package_config_dbpassword,
        database : process.env.npm_package_config_dbname
    });

    connection.query(
        'UPDATE ' + process.env.npm_package_config_table +
        ' SET ' + process.env.npm_package_config_logField + ' = "' + connection.escape(output) + '"' +
        ' WHERE ' + process.env.npm_package_config_idField + ' = ' + connection.escape(commandId),
        function(err, rows) {

        if (err) {
            if (debug) {
                console.log(err);
            }
        }
    });

    connection.end();
}

function executeCommand(term, commandId){
    var mysql = require('mysql');
    var connection = mysql.createConnection({
        host     : process.env.npm_package_config_dbhost,
        user     : process.env.npm_package_config_dbuser,
        password : process.env.npm_package_config_dbpassword,
        database : process.env.npm_package_config_dbname
    });

    connection.query('SELECT * FROM ' + process.env.npm_package_config_table + ' WHERE ' + process.env.npm_package_config_idField + ' = ' + connection.escape(commandId), function(err, rows) {

        if (err) {
            if (debug) {
                console.log(err);
            }
            return;
        }

        if (rows.length == 0) {
            if (debug) {
                console.log('Given command id doesnt exist in database');
            }
            return;
        }

        term.write(rows[0][process.env.npm_package_config_commandField]);
        term.write('\u000D'); // carriage return to execute command

    });

    connection.end();
}
