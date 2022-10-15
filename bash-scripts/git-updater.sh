#!/usr/bin/env bash

set -e # Stop on error

while :
do
    date
    git fetch
    git reset --hard origin/master
    git clean -f

#    sudo chmod -R 777 temp/
#    sudo rm -Rd temp/
#    sudo chmod 777 git-updater.sh


    # Restart your services here

    while :
    do
        sleep 3600
        git fetch
        if git status | grep 'behind'
        then
            break
        fi
    done
done  
