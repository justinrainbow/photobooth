
/**
 * Module dependencies.
 */

var express = require('express'),
    spawn = require('child_process').spawn;

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

// Routes

app.get('/', function(req, res){
  res.render('index', {
    title: 'Express'
  });
});

app.get('/snap', function (req, res) {
    if (!photobooth) {
        photobooth = spawn(__dirname+'/../run.sh');
        photobooth.on('exit', function (data) {
            photobooth = false;
        });
        photobooth.stdout.on('data', function (data) {
          console.log(data);
        });
    }
    
    res.send();
});

app.listen(3000);
console.log("Express server listening on port %d in %s mode", app.address().port, app.settings.env);
