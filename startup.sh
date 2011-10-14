#!/bin/bash

# sudo defaults write com.apple.loginwindow LoginHook `pwd`/startup.sh

DIR=`dirname $0`

cd $DIR
/usr/local/bin/forever start app.js
