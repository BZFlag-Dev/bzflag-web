#!/bin/sh

cd `echo $0 | sed -e s/[^/]*$//`
MYPATH=`pwd`
EXCLUDE=$MYPATH/excludeFromBackup
CONFIG=$MYPATH/../.config/bzlsql.php

. $CONFIG

cd ../..
WEBDIR=${LEAGUErootdir##*/}

TARNAME=$1
if [ "$1" == "" ] ; then
	TARNAME=$LEAGUEbackups/LEAGUECODE-`date +%Y%m%d-%H%M`
fi

tar -X$EXCLUDE -cvzf $TARNAME.tgz $WEBDIR

