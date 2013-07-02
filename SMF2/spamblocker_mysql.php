<?php 
/*
    <id>underdog:spamblocker</id>
	<name>Spam Blocker</name>
	<version>1.0</version>
	<type>modification</type>
*/	

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://webdevelop.comli.com	
 * Copyright 2013 underdog@webdevelop.comli.com
 * This software package is distributed under the terms of its Freeware License
 * http://webdevelop.comli.com/index.php/page=spamblocker_license
*/

/*  This file is for mysql setup */
global $smcFunc, $scripturl, $sourcedir;

$i = 0;
$columns = array(
		'reference',
		'user_message',
		'error_message',
		'enable_errorlog',
		'enable_sfs',
		'enable_akismet',
		'enable_honeypot',
		'enable_spamhaus',
		'enable_email',
		'enable_pass',
		'enable_reset',
		'ban_option',
		'ban_full',
		'ban_post',
		'ban_register',
		'ban_login',
		'expiration',
		'expire_time'		
	);

$defaults = array(1,'Access denied.','SpamBlocker IP Ban',2,1,1,1,1,1,1,1,0,0,1,0,1,30);	
$table = 'spamblocker_settings';
$new_columnsTypes = array(
                          'spamblocker_settings' => array (
				'int(10) unsigned NOT NULL AUTO_INCREMENT',
				'varchar(255) NOT NULL',
				'varchar(255) NOT NULL',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 1',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0',
				'int(10) unsigned NOT NULL default 0'
				)
			);	
$whitelist = array('ip_low1', 'ip_high1', 'ip_low2', 'ip_high2', 'ip_low3', 'ip_high3', 'ip_low4', 'ip_high4');								
$blacklist = array('id_ban_group' => 'smallint(5) unsigned NOT NULL default 0', 'id_member' => 'mediumint(8) unsigned NOT NULL default 0');

/*  Create tables if they do not exist  */
if (!check_table_existsSB('spamblocker_settings'))
{
	$result = $smcFunc['db_query']('', "CREATE TABLE {db_prefix}{$table} 
                                   (
					`reference` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
					`user_message` varchar(255) NOT NULL,
					`error_message` varchar(255) NOT NULL,
					`enable_errorlog` int(10) unsigned NOT NULL default 0,									
					`enable_sfs` int(10) unsigned NOT NULL default 0,
					`enable_akismet` int(10) unsigned NOT NULL default 0,
					`enable_honeypot` int(10) unsigned NOT NULL default 0,
					`enable_spamhaus` int(10) unsigned NOT NULL default 0,
					`enable_email` int(10) unsigned NOT NULL default 0,
					`enable_pass` int(10) unsigned NOT NULL default 0,
					`enable_reset` int(10) unsigned NOT NULL default 0,
					`ban_option` int(10) unsigned NOT NULL default 0,
					`ban_full` int(10) unsigned NOT NULL default 1,
					`ban_post` int(10) unsigned NOT NULL default 0,
					`ban_register` int(10) unsigned NOT NULL default 0,
					`ban_login` int(10) unsigned NOT NULL default 0,
					`expiration` int(10) unsigned NOT NULL default 0,
					`expire_time` int(10) unsigned NOT NULL default 0,
					PRIMARY KEY (`reference`))"
				);	
	$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}spamblocker_settings 
					(`reference`, `user_message`, `error_message`, `enable_errorlog`, `enable_sfs`, `enable_akismet`, `enable_honeypot`, `enable_spamhaus`, `enable_email`, `enable_pass`, `enable_reset`, `ban_option`, `ban_full`, `ban_post`, `ban_register`, `ban_login`, `expiration`, `expire_time`) 
					VALUES (1, 'Access denied.', 'SpamBlocker IP Ban', 2, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 0, 1, 30)"
				);									
}

if (!check_table_existsSB('spamblocker_whitelist'))
{
	$result = $smcFunc['db_query']('', "CREATE TABLE {db_prefix}spamblocker_whitelist
                                   (
					`reference` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,									
					`ip_low1` tinyint(3) unsigned NOT NULL default 0,
					`ip_high1` tinyint(3) unsigned NOT NULL default 0,
					`ip_low2` tinyint(3) unsigned NOT NULL default 0,
					`ip_high2` tinyint(3) unsigned NOT NULL default 0,
					`ip_low3` tinyint(3) unsigned NOT NULL default 0,
					`ip_high3` tinyint(3) unsigned NOT NULL default 0,
					`ip_low4` tinyint(3) unsigned NOT NULL default 0,
					`ip_high4` tinyint(3) unsigned NOT NULL default 0,									
					PRIMARY KEY (`reference`))");							
}


if (check_table_existsSB('spamblocker_cache'))
	$request = $smcFunc['db_query']('', "DROP TABLE {db_prefix}spamblocker_cache");

if (!check_table_existsSB('spamblocker_blacklist'))
{
	$result = $smcFunc['db_query']('', "CREATE TABLE {db_prefix}spamblocker_blacklist
                                   (
					`reference` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,									
					`id_ban_group` smallint(5) unsigned NOT NULL default 0,
					`id_member` mediumint(8) unsigned NOT NULL default 0,
					PRIMARY KEY (`reference`))"
				);							
}				
		
/*  Add extra needed columns into existing tables for columns native to Spam Blocker */
/*  This is necessary due to possible manual deletion or otherwise  */

foreach ($columns as $columnName)
{	                       
	$columnType = $new_columnsTypes[$table][$i];				   
	if (!checkFieldSB($table,$columnName))
	{
		$request = $smcFunc['db_query']('', "ALTER TABLE {db_prefix}$table ADD $columnName $columnType");	
		$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$table SET $columnName = '{$defaults[$i]}'");
	} 		
	$i++;
}

foreach ($whitelist as $column)
{
	if (!checkFieldSB('spamblocker_whitelist',$column))
		$request = $smcFunc['db_query']('', "ALTER TABLE {db_prefix}spamblocker_whitelist ADD '{$column}' tinyint(3) unsigned NOT NULL default 0");				
}

foreach ($blacklist as $columnx => $specs)
{
if (!checkFieldSB('spamblocker_blacklist',$columnx))	
	$request = $smcFunc['db_query']('', "ALTER TABLE {db_prefix}spamblocker_blacklist ADD {$columnx} {$specs}");	
}

if (!checkFieldSB('messages','spamblocker'))
	$request = $smcFunc['db_query']('', "ALTER TABLE {db_prefix}messages ADD spamblocker int(10) unsigned NOT NULL default 0");	


/* Insert integration hooks */
add_integration_function('integrate_pre_include', '$sourcedir/SpamBlockerHooks.php');
add_integration_function('integrate_pre_load', 'spamBlocker_allExpired');
add_integration_function('integrate_actions', 'spamBlocker_actions');
add_integration_function('integrate_load_permissions', 'spamBlocker_load_permissions');
add_integration_function('integrate_admin_areas', 'spamBlocker_admin_areas');
add_integration_function('integrate_validate_login', 'spamBlocker_login');
add_integration_function('integrate_create_topic', 'spamBlocker_akismet_topic');
add_integration_function('integrate_create_reply', 'spamBlocker_akismet_reply');
add_integration_function('integrate_register', 'spamBlocker_register');

/* Adjust table type/collation */
SB_adjust_table();

$url = $scripturl . '?action=admin;area=spamBlocker;';
if((!empty($_SERVER['HTTP_USER_AGENT'])) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false)
{
	@header("Refresh:2; url:" . $url);
	@header("Location:".$url);
}	
else
	@header("Refresh:1; url=$url");
 
/*  END - Mysql setup  */

/* Check if the column exists */
function checkFieldSB($tableName,$columnName)
{		
	if (check_table_existsSB($tableName))
	{
		global $smcFunc;
		$check = false;
		$checkval = false;
		$check = $smcFunc['db_query']('', "DESCRIBE {db_prefix}$tableName $columnName");
		$checkval = $smcFunc['db_num_rows']($check);
		$smcFunc['db_free_result']($check);
		if ($checkval > 0)
			return true;
	}
	
	return false;
} 

/*  Returns amount of columns in a table  */
function checkTableSB($tableName)
{
	global $smcFunc;
	$check = false;
	$checkval = false;
	$check = $smcFunc['db_query']('', "DESCRIBE {db_prefix}$tableName");
	$checkval = $smcFunc['db_num_rows']($check);
	$smcFunc['db_free_result']($check);
	if ($checkval > 0) 
		return $checkval;

	return false;
} 

/*  Check if table exists  */
function check_table_existsSB($table)
{
	global $smcFunc;
	$check = false;
	$checkval = false;
	$check = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}$table'");
	$checkval = $smcFunc['db_num_rows']($check);
	$smcFunc['db_free_result']($check);
	if ($checkval >0)
		return true;
		
	return false;
}

/* Adjust Spam Blocker tables to common Type & Collation */
function SB_adjust_table()
{
	global $smcFunc, $db_name;	
	$tables = array('spamblocker_settings', 'spamblocker_whitelist', 'spamblocker_blacklist');
	
	/* Query Engine & Collation of the database */
	$result = $smcFunc['db_query']('', "SHOW TABLE STATUS FROM `$db_name`");
	while ($val = $smcFunc['db_fetch_assoc']($result))
	{
		$engine = $val['Engine'];
		$collation = $val['Collation'];
		$charsetx = explode('_', $val['Collation']);
		$charset = $charsetx[0];          
	}
	$smcFunc['db_free_result']($result);
	
	/* Adjust SpamBlocker tables to match */
	foreach ($tables as $table)
	{
		$alterTable = $smcFunc['db_query']('', "ALTER TABLE {db_prefix}{$table} CONVERT TO CHARACTER SET {$charset} COLLATE {$collation}");
		$alterTable = $smcFunc['db_query']('', "ALTER TABLE {db_prefix}{$table} ENGINE = {$engine}");
	}
}	
?>
