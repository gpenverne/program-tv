#!/bin/sh

# This command will update the tv guide
# 
cd /dev/shm/
rm "/dev/shm/tvguide.xm*"
wget "http://www.xmltv.fr/guide/tvguide.xml"
