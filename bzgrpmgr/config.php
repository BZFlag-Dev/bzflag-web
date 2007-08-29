<?php

// Copyright (c) 1993 - 2004 Tim Riker
//
// This package is free software;  you can redistribute it and/or
// modify it under the terms of the license found in the file
// named COPYING that should have accompanied this file.
//
// THIS PACKAGE IS PROVIDED ``AS IS'' AND WITHOUT ANY EXPRESS OR
// IMPLIED WARRANTIES, INCLUDING, WITHOUT LIMITATION, THE IMPLIED
// WARRANTIES OF MERCHANTIBILITY AND FITNESS FOR A PARTICULAR PURPOSE.

// MySQL settings
$config['sql_hostname'] = "localhost";
$config['sql_database'] = "bzgrpmgr";
$config['sql_username'] = "bzgrpmgr";
$config['sql_password'] = "bzgrpmgr";

$config['phpbb_sql_hostname'] = "localhost";
$config['phpbb_sql_database'] = "bzgrpmgr_phpbb2";
$config['phpbb_sql_username'] = "bzgrpmgr";
$config['phpbb_sql_password'] = "bzgrpmgr";
$config['phpBB2_users_table'] = "phpbb_users";

// Data layer settings
$data = new data_phpbb2();
$data->init_databases( $config );


?>
