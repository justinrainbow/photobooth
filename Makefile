
install:
	./install.php ~/Library/LaunchAgents/com.symplelabs.photobooth.plist
	launchctl load -w ~/Library/LaunchAgents/com.symplelabs.photobooth.plist

all:
	cp config.json.dist config.json
	mkdir -p files/strips files/photos
