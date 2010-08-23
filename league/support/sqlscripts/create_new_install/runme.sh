#!/bin/sh

echo "*******************************************************************************"
echo " Make sure that .config/bzlsql.php contains the proper values for sql connection"
echo ""
echo " THIS SCRIPT WILL DELETE ALL TABLES IN THE DATABASE"
echo " Abort now if this is not what you want"
echo "   (Enter to continue)"
echo "*******************************************************************************"
read $ans


php drop_all_tables.php
echo "creating tables ..."

../sql < create_new_tables.sql
../sql < create_badpass.sql
../sql < create_countries.sql
../sql < create_states.sql
../sql < create_siteconfig.sql
../sql < create_forums.sql
../sql < create_themes.sql
../sql < create_smiley.sql
../sql < create_permissions.sql

echo "creating admin player with password of 'admin'"
echo " CHANGE THE PASSWORD with the 'profile' webpage"
php add_admin.php
