#! /bin/bash

curl -X GET http://cf.x/account/settings/bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d
echo '\n';
#exit;

curl -X POST -d 'uid=bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d&notify_send=1&notify_receive=1&notify_deliver=1' http://cf.x/account/settings
echo '\n';
#exit;

curl -X PUT http://cf.x/account/settings/bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d/1/0/1
echo '\n';
exit;

curl -X DELETE http://cf.x/account/settings/bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d
echo '\n';
exit;

