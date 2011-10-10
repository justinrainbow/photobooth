
/**
 * Module dependencies.
 */
var express = require('express'),
    fs      = require('fs'),
    spawn   = require('child_process').spawn,

    app     = module.exports = express.createServer(),
    io      = require('socket.io').listen(app),
    db      = require('redis').createClient(),
    pubsub  = require('redis').createClient(),
    
    photo_dir = __dirname+'/files/photos',
    strip_dir = __dirname+'/files/strips',

    photobooth,
    cfg;

// Configuration

app.configure(function(){
    app.set('views', __dirname + '/views');
    app.set('view engine', 'ejs');
    app.use(express.bodyParser());
    app.use(express.methodOverride());
    app.use(express.compiler({ src: __dirname + '/public', enable: ['less'] }));
    app.use(app.router);
    app.use(express.static(__dirname + '/public'));
    app.use(express.static(__dirname + '/files'));
});

app.configure('development', function(){
    app.use(express.errorHandler({ dumpExceptions: true, showStack: true }));
});

app.configure('production', function(){
    app.use(express.errorHandler());
});

// database

pubsub.on('connected', function () {
    pubsub.subscribe("booth:capture");
});

pubsub.on('reconnected', function () {
    pubsub.subscribe("booth:capture");
})

pubsub.on("message", function (channel, message) {
    console.log("Message: "+channel+" - "+message);

    io.sockets.emit(channel, message);
});
pubsub.subscribe("booth:capture");


io.sockets.on('connection', function (socket) {
    // socket.emit('news', { hello: 'world' });
    // socket.on('my other event', function (data) {
    //     console.log(data);
    // });
});


// Routes

app.get('/', function(req, res){
    res.render('index', {
        title: 'PhotoBooth'
    });
});

function takePhotos(cb) {
    var strip_id;

    photobooth = spawn('./capture.php', [], {
            cwd: __dirname
        });

    photobooth.stderr.on('data', function (data) {
        console.log("stderr: " + data.toString())
    })

    photobooth.on('exit', function (data) {
        photobooth = false;
        cb(strip_id);
    });

    photobooth.stdout.on('data', function (data) {
        console.log("stdout: " + data.toString());
        strip_id = data.toString();
    });

    photobooth.stdin.end();
}

function processStrip(id, cb) {
    var strip = spawn('./console', ['photos:process', '-o', strip_dir+'/'+id+'.jpg', photo_dir+'/'+id], {
        cwd: __dirname
    });
    
    strip.on('exit', function (data) {
        cb(id);
    });
    
    strip.stdin.end();
}

app.get('/snap', function (req, res) {
    db.exists('photolock', function (err, response) {
        if (!response) {
            db.multi()
                .set('photolock', 1)
                .expire('photolock', 60) // only lock the process for 60 seconds
                .exec();

            takePhotos(function (id) {
                db.del('photolock');
                
                if (null !== id) {
                    processStrip(id, function () {
                        spawn('./print.php', [id], {
                            cwd: __dirname
                        })
                    })
                }
            });

            res.json({ type: "success" });
        } else {
            db.ttl('photolock', function (err, ttl) {
                res.json({ type: "failure", msg: "Already running", ttl: ttl });
            });
        }
    });
});

app.get('/config', function (req, res) {
    var cfg = JSON.parse(fs.readFileSync(__dirname+"/config.json"));

    findPrinters(function(printers) {
        res.render('config', {
            title: 'PhotoBooth Config',
            cfg: cfg,
            printers: printers
        });
    });
});

app.post('/config', function (req, res) {
    fs.writeFile(__dirname+"/config.json", JSON.stringify(req.body));

    res.json({ msg: "Saved" });
});

function findPrinters(cb) {
    var printers = spawn(__dirname+"/printers.php");
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

