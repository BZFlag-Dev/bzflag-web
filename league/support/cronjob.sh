#!/bin/sh

BINPATH=`echo $0 | sed -e s/[^/]*$//`
cd $BINPATH

sqlscripts/sqldumpgzip BACKUP

