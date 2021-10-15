var mysql = require('mysql');

var conn = mysql. createConnection({
    host:'127.0.0.1',
    user:dbUser,
    password:dbPw,
    database:db
});
module.exports = conn;