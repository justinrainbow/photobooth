#!/bin/bash

./console photos:capture
./console photos:process -o strip.jpg tools/raw/

# lp -d "C3400__192_168_100_101____MiniMax-2" -o page-top=0 -o page-bottom=0 -o scaling=100 strip.jpg