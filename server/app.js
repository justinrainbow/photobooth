
/**
 * Module dependencies.
 */

var express = require('express'),
    fs      = require('fs'),
    spawn   = require('child_process').spawn,
    socket  = require('socket.io');

var app = module.exports = express.createServer(),
    photobooth;

// Configuration

app.configure(function(){
  app.set('views', __dirname + '/views');
  app.set('view engine', 'ejs');
  app.use(express.bodyParser());
  app.use(express.methodOverride());
  app.use(express.compiler({ src: __dirname + '/public', enable: ['less'] }));
  app.use(app.router);
  app.use(express.static(__dirname + '/public'));
});

app.configure('development', function(){
  app.use(express.errorHandler({ dumpExceptions: true, showStack: true })); 
});

app.configure('production', function(){
  app.use(express.errorHandler()); 
});

var io = socket.listen(app);

// Routes

app.get('/', function(req, res){
  res.render('index', {
    title: 'PhotoBooth'
  });
});

app.get('/snap', function (req, res) {
    if (!photobooth) {
        photobooth = spawn(__dirname+'/../run.sh');
        photobooth.on('exit', function (data) {
            photobooth = false;
        });
        photobooth.stdout.on('data', function (data) {
          console.log(data.toString());
        });
    }
    
    res.send();
});

app.get('/config', function (req, res) {
    var cfg = JSON.parse(fs.readFileSync(__dirname+"/../config.json"));
    
    findPrinters(function(printers) {
        res.render('config', {
            title: 'PhotoBooth Config',
            cfg: cfg,
            printers: printers
        });
    });
});

app.post('/config', function (req, res) {
    fs.writeFile(__dirname+"/../config.json", JSON.stringify(req.body), function (err) {
        
    });
    res.send("");
});

io.sockets.on('connection', function (socket) {
  socket.emit('news', { hello: 'world' });
  socket.on('my other event', function (data) {
    console.log(data);
  });
});

function findPrinters(cb) {
    var printers = spawn(__dirname+"/../printers.php");
    printers.on('exit', function (data) {
        printers = false;
    });
    printers.stdout.on('data', function (data) {
      cb(JSON.parse(data.toString()));
    });
    printers.stdin.end();
};

app.listen(3000);
console.log("Express server listening on port %d in %s mode", app.address().port, app.settings.env);

