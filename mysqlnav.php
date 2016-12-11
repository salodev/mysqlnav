#!/usr/bin/php
<?php
require_once(dirname(__FILE__).'/bootstrap.php');
MysqlClientApplication::SetStoreFile(dirname(__FILE__).'/connections.json');
cuif\CUIF::StartApplication('MysqlClientApplication', false);
