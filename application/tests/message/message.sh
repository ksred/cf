#! /bin/bash

#curl -X GET http://cf.x/message/bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d
#echo '\n';
#exit;

curl -X POST -d 'from=8d1302e8aa54bd59e2f4f398c66ff94f2650d4d34679ee02cf5fc61b9cabcee5&to=bb44bf07cf9a2db0554bba63a03d822c927deae77df101874496df5a6a3e896d&message=Hello World' http://cf.x/message
echo '\n';
exit;

#curl -X PUT http://cf.x/message/MESSAGEID/Hello+Brave+New+World
#echo '\n';
#exit;

#curl -X DELETE http://cf.x/message/MESSAGEID
#echo '\n';
#exit;

