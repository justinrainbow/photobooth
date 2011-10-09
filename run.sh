#!/bin/bash

DIR=`dirname $0`

# ./console photos:capture $DIR/tools/raw/ $DIR/hook.php 

./console photos:process -o $DIR/strip.jpg $DIR/tools/raw/

# lp -d "C3400__192_168_100_101____MiniMax-2" -o page-top=0 -o page-bottom=0 -o scaling=100 strip.jpg
# lp -d "HP_Color_LaserJet_CM3530_MFP" strip.jpg
