<?php

$MYSQL_HOST = "127.0.0.1";
$MYSQL_LOGIN = "root";
$MYSQL_PASSWORD = "";
$MYSQL_DB = "";
$API_KEY = "";

$connect = mysqli_connect($MYSQL_HOST, $MYSQL_LOGIN, $MYSQL_PASSWORD, $MYSQL_DB) or die("Error while connecting to the database...");
