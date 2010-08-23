#!/bin/sh

# create a release gzip from a fresh checkout from CVS

if [ -z "$1" ]; then
  echo "Please specify an output file name (without the .tgz suffix)"
  echo "  NOTE: file will be created relative to the /tmp directory unless an"
  echo "        absolute path is specified."
  exit 5
fi


rm -rf /tmp/webleague
cd ../..
cp -r webleague /tmp

cd /tmp/webleague

## delete all occurances of 'CVS' directories and '.cvsignore' files
find -name CVS -exec rm -rf \{\} \;
find -name ".cvsignore" -exec rm \{\} \;

rm support/release.sh
mv support/README .
rm .config/config.php
rm .config/bzlsql.php

cd ..
tar cvzf $1.tgz webleague



