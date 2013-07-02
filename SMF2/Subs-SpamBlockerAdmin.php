<?php
// Version: 1.0; Spam Blocker  

/*
 *	Configuration Sub-Routines file for the Spam Blocker Mod	
 *	c/o Underdog @ http://webdevelop.comli.com		  
 *	SMF 2 Version				
*/

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://webdevelop.comli.com	
 * Copyright 2013 underdog@webdevelop.comli.com
 * This software package is distributed under the terms of its Freeware License
 * http://webdevelop.comli.com/index.php/page=spamblocker_license
*/

if (!defined('SMF')) 
	die('Hacking attempt...');
	
/*	This file contains functions needed for Spam Blocker Administration.
				
	void function checkFieldSB($tableName,$columnName)
		- Checks if mysql column exists
		
	void function check_table_existsSB($table)
		- Checks if mysql table exists		

	void function createSpamBlocker_setting($tableName, $columnName, $valuex) 
		- Updates spamBlocker mysql table
		
	void function spamBlockerBanCheck($id_ban_group)
		- Returns amount of matching queries from the blacklist & ban_list tables		
		
	void function cleanSpamBlockerQuery($string)
		- String filter prior to saving any user input
	
	void function cleanSpamBlockerInput($string=false)
		- String filter prior to saving comma separated input
	
	void function cleanSpamBlockerRedirect($string=false)
		- String filter prior to saving the redirect URL in the database
		
	void function spamBlockerIP_Exists($ip='255.255.255.254')	
		- Check if IP already exists in the ban list or whitelist
		- Returns string 'banned', 'whitelist' or false	
			
	void function spamBlockerEmendBlacklist($message = false)	
		- Cross checks blacklist and ban tables
		- Emends blacklist table		
		- Returns message

	void function spamBlockerStyles()
		- Reads custom theme css file
		- Locates theme background image file from custom theme css
		- Locates text color for catbg3 from custom theme css
		- Returns image file location and text color (else defaults) as array 		

	void function spamBlockerDeleteMember($member_id)	
		- Deletes all data associated with the user id
		- Makes sure user id is not an administrator
		- Returns false if admin id was attempted
		- Returns true if successful
		
	void function spamBlockerClearBlacklist()	
		- Deletes all data from the blacklist table
		- Deletes all blacklisted data from the ban tables
		- Deletes all related member data from the database

	void function checkSFS_APIkey($key=false)
		- Checks if Stop Forum Spam API key is valid		
		- Returns false if no key was provided
		- Returns message for no connection
		- Returns message for valid/invalid API key
		
	void function spamBlockerDefaultReset()
		- Resets configuration settings to defaults
		- Some settings depend on API keys else their related functions will be disabled
		- Returns reset message
		
*/
  
/* Check if the column exists */
function checkFieldSB($tableName,$columnName)
{	
	$checkTable = check_table_existsSB($tableName);
	if ($checkTable == true)
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

/*  Check if table exists  */
function check_table_existsSB($table)
{
	global $smcFunc;
	$check = false;
	$checkval = false;
	$check = $smcFunc['db_query']('', "SHOW TABLES LIKE '{db_prefix}$table'");
	$checkval = $smcFunc['db_num_rows']($check);
	$smcFunc['db_free_result']($check);
	if ((int)$checkval > 0)
		return true;
		
	return false;
}

/*  Update table -> column -> value for spam blocker settings */
function createSpamBlocker_setting($tableName, $columnName, $valuex) 
{
	global $smcFunc;
	$value = cleanSpamBlockerQuery($valuex);
		
	if (empty($tableName) || empty($columnName))
		return;		
	elseif (empty($value))
		$value = false;
		
	$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET $columnName = '{$value}' WHERE `{db_prefix}$tableName`.`reference` = 1 LIMIT 1");
}

/* Check ban_list for multiple entires of ban group */
function spamBlockerBanCheck($id_ban_group = 0)
{
	global $smcFunc;
	$count = 0;
	
	$result = $smcFunc['db_query']('', "SELECT id_ban_group	FROM {db_prefix}ban_items WHERE id_ban_group = '{$id_ban_group}' LIMIT 2");
	while ($val = $smcFunc['db_fetch_assoc']($result))
		$count++;			
	
	$smcFunc['db_free_result']($result);
	
	return $count;	
}

function cleanSpamBlockerQuery($string=false)
{
	$string = filter_var($string, FILTER_SANITIZE_STRING);
	$filtered_string = "";
	$patterns = array("/\&/","/\+/","/delete/i", "/update/i","/union/i","/insert/i","/drop/i","/http/i","/--/i");  
	$string = preg_replace($patterns, "" , $string);
	for ($i=0;$i<strlen($string);$i++)
		{
			$current_char = substr($string,$i,1);
			if (ctype_alnum($current_char) == TRUE || $current_char == "_" || $current_char == "/" || $current_char == "-" || $current_char == " ")
				{$filtered_string .= $current_char;}
		}  
	$filtered_string = trim($filtered_string, ' ');		   
	return $filtered_string;
}

function cleanSpamBlockerInput($string=false)
{
	$textFilter = array_unique(explode(',', $string));
	sort($textFilter);
	$words = '';
	foreach ($textFilter as $word)
	{
		if (strlen(strip_tags($word)) > 3)
			$words .= cleanSpamBlockerQuery(trim($word)) . ',';
	}
				
	$words = rtrim($words, ',');
	$textFilter = array_unique(explode(',', $words));
	sort($textFilter);
	$words = '';
	foreach ($textFilter as $word)
		$words .= cleanSpamBlockerQuery(trim($word)) . ', ';
	
	$words = rtrim($words, ', ');
	
	return $words;
}

function cleanSpamBlockerRedirect($string=false)
{
	$string = urlencode(filter_var($string, FILTER_SANITIZE_URL));
	return $string;	
}

function spamBlockerIP_filter($ip="0.0.0.0")
{
	global $context;
	$ipArray = explode('.', (trim($ip)));
	$x = 0;
	if (count($ipArray) != 4)
		return false;
	
	foreach ($ipArray as $ipQuery)
	{
		$range = explode('-', $ipQuery);
		if ($ipQuery == '*')
		{
			$ipLow[$x] = 0;
			$ipHigh[$x] = 255;
		}
		elseif (count($range) == 2)
		{
			if ($range[0] == '*')
				$range[0] = 0;
			if ($range[1] == '*')
				$range[1] = 255;
			if ((int)$range[0] < 0 || (int)$range[0] > 255)
				return false;
			if ((int)$range[1] < 0 || (int)$range[1] > 255)
				return false;
			if ((int)$range[0] > (int)$range[1])
				return false;
			
			$ipLow[$x] = (int)$range[0];
			$ipHigh[$x] = (int)$range[1];					
		}
		elseif ((int)$ipQuery < 0 || (int)$ipQuery > 255)
			return false;				
		else
		{			
			$ipLow[$x] = (int)$ipQuery;
			$ipHigh[$x] = (int)$ipQuery;							
		}
			
		$x++;	
	}	
	
	$ipLows = implode('.', $ipLow);
	$ipHighs = implode('.', $ipHigh);
	
	if ((filter_var($ipLows, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) && (filter_var($ipHighs, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)))
	{	
		$_SESSION['spamBlockerIP_Error'] = false;
		return array($ipLow[0], $ipHigh[0], $ipLow[1], $ipHigh[1], $ipLow[2], $ipHigh[2], $ipLow[3], $ipHigh[3]);
	}
	
	return false;	
}

function spamBlockerIP_Exists($ip='255.255.255.254')
{
	global $sourcedir, $smcFunc;	
	
	$i = 0; 
	$ip_array = explode('.', $ip);
	$hi_low = array();
	$ipdata = array('ip_low1','ip_high1','ip_low2','ip_high2','ip_low3','ip_high3','ip_low4','ip_high4');

	$request = $smcFunc['db_query']('', "SELECT ip_low1,ip_high1,ip_low2,ip_high2,ip_low3,ip_high3,ip_low4,ip_high4 FROM {db_prefix}ban_items ORDER BY id_ban ASC");
	while ($val = $smcFunc['db_fetch_assoc']($request))
	{	
		foreach ($ipdata as $data)
		{
			if (empty($val[$data]))
				$val[$data] = 0;

			$hi_low[$i][$data] = (int)$val[$data];
		}
		$i++;
	}
	$smcFunc['db_free_result']($request);

	foreach ($hi_low as $z)
	{
		if (($ip_array[0] >= $z['ip_low1']  && $ip_array[0] <= $z['ip_high1']) && ($ip_array[1] >= $z['ip_low2']  && $ip_array[1] <= $z['ip_high2']) && ($ip_array[2] >= $z['ip_low3']  && $ip_array[2] <= $z['ip_high3']) && ($ip_array[3] >= $z['ip_low4']  && $ip_array[3] <= $z['ip_high4']))
			return 'banned'; 
	}

	$i = 0;
	$hi_low = array();
	$request = $smcFunc['db_query']('', "SELECT ip_low1,ip_high1,ip_low2,ip_high2,ip_low3,ip_high3,ip_low4,ip_high4 FROM {db_prefix}spamblocker_whitelist ORDER BY reference ASC");
	while ($val = $smcFunc['db_fetch_assoc']($request))
	{	
		foreach ($ipdata as $data)
		{
			if (empty($val[$data]))
				$val[$data] = 0;

			$hi_low[$i][$data] = (int)$val[$data];
		}
		$i++;
	}
	$smcFunc['db_free_result']($request);

	foreach ($hi_low as $z)
	{
		if (($ip_array[0] >= $z['ip_low1']  && $ip_array[0] <= $z['ip_high1']) && ($ip_array[1] >= $z['ip_low2']  && $ip_array[1] <= $z['ip_high2']) && ($ip_array[2] >= $z['ip_low3']  && $ip_array[2] <= $z['ip_high3']) && ($ip_array[3] >= $z['ip_low4']  && $ip_array[3] <= $z['ip_high4']))
			return 'whitelist'; 
	}
		
	return false;
}

function spamBlockerEmendBlacklist($message = false)
{
	global $smcFunc, $txt;	
	loadLanguage('SpamBlocker');
	
	if (!$message)
		$message = $txt['spamBlockerIP_BlacklistEmend'];
	
			$references = array();
			$result = $smcFunc['db_query']('', "SELECT black.reference, black.id_ban_group, ban.id_ban_group, ban.name FROM {db_prefix}spamblocker_blacklist AS black
												 LEFT JOIN {db_prefix}ban_groups AS ban ON (ban.id_ban_group = black.id_ban_group)	
												 WHERE black.reference ORDER BY black.reference ASC");
			while ($val = $smcFunc['db_fetch_assoc']($result))
			{	
				if ((!empty($val['name'])) || $val['name'])
					continue; 
                                 
				$references[] = $val['reference'];				
			}
			$smcFunc['db_free_result']($result);

			foreach ($references as $ref)
				$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE reference = '{$ref}'");
		
	return $message;
					
}

function spamBlockerStyles()
{
	/* This routine fixes an issue with border-radius for some custom themes */
	global $settings;
	$dir = $settings['theme_dir'] . '/css/index.css';
	$theme_bg_file = $settings['actual_theme_url'].'/images/admin/spamblocker_main_block.png';
	$color = 'color:#F8F8FF;';
	$selectors = array();
	$sstyles = array();
	$npos = 0;
	$sstl = 0;
	$sarray = array();
	$catbg_css = array();
	$catbg = -1;
	$title = -1;
	$spos = 0;
	if (!file_exists($dir))
		return array('bg' => $theme_bg_file, 'color' => $color);
	
	$lines = file($dir);
	foreach ($lines as $line_num => $line)
	{
		if (empty($cssstyles))
			$cssstyles = trim($line);
		else
			$cssstyles .= trim($line);
	}
   
	$tok = strtok($cssstyles, "{}");

	while ($tok !== false)
	{
		$sarray[$spos] = $tok;
		$spos++; 
		$tok = strtok("{}");
	}
 
	$size = count($sarray);

	for($i = 0; $i<$size; $i++)
	{
		if ($i % 2 == 0)
		{
			$selectors[$npos] = $sarray[$i];
			$npos++;    
		}
		else
		{
			$sstyles[$sstl] = $sarray[$i];
			$sstl++;
		} 
	}
  
	foreach ($selectors as $key => $a)
	{
		if (strpos($a, 'titlebg') !== false)
		{
			$title = $key;
			break;
		}		
	}
	
	foreach ($selectors as $key => $a)
	{		
		if (strpos($a, 'catbg3') !== false)
		{
			$catbg = $key;
			break;
		}
	}
	
	if ((int)$catbg > -1)
		$catbg_css = explode(';',strtolower($sstyles[$catbg]));
        foreach ($catbg_css as $key => $css)
        {            
		if (strpos($css, "color") !== false)
		{
			$color = trim($css) . ';';
			break;
		}
	}
	
	if ((int)$title < 0)
		return array('bg' => $theme_bg_file, 'color' => $color);
	
	preg_match('/(?<=\()(.+)(?=\))/is', $sstyles[$title], $match);
	if (count($match) > 0)
		$theme_bg_file = str_replace('..', $settings['theme_url'], $match[1]);
		
	if ((empty($theme_bg_file)) || !$theme_bg_file)
		$theme_bg_file = $settings['actual_theme_url'].'/images/admin/spamblocker_main_block.png';
		
	return array('bg' => $theme_bg_file, 'color' => $color);
}

function spamBlockerDeleteMember($member_id)
{
	global $smcFunc, $sourcedir;
	$users = array((int)$member_id);
	require_once($sourcedir . '/PersonalMessage.php');
	require_once($sourcedir . '/ManageAttachments.php');	
	
	/* Protect admins from deletion (just to be safe) */
	$request = $smcFunc['db_query']('', '
		SELECT id_member, member_name, CASE WHEN id_group = {int:admin_group} OR FIND_IN_SET({int:admin_group}, additional_groups) != 0 THEN 1 ELSE 0 END AS is_admin
		FROM {db_prefix}members
		WHERE id_member = ({int:user})
		LIMIT 1',
		array(
			'user' => $member_id,
			'admin_group' => 1,
		)
	);
	$admins = array();
	$user_log_details = array();
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		if ($row['is_admin'])
			$admins[] = $row['id_member'];		
	}
	$smcFunc['db_free_result']($request);
	
	if ((in_array($member_id, $admins)) || (int)$member_id == 0)
		return false;

	// Make these peoples' posts guest posts.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}messages
		SET id_member = {int:guest_id}, poster_email = {string:blank_email}
		WHERE id_member = ({int:user})',
		array(
			'guest_id' => 0,
			'blank_email' => '',
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}polls
		SET id_member = {int:guest_id}
		WHERE id_member = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);

	// Make these peoples' posts guest first posts and last posts.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}topics
		SET id_member_started = {int:guest_id}
		WHERE id_member_started = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}topics
		SET id_member_updated = {int:guest_id}
		WHERE id_member_updated = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_actions
		SET id_member = {int:guest_id}
		WHERE id_member = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_banned
		SET id_member = {int:guest_id}
		WHERE id_member = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_errors
		SET id_member = {int:guest_id}
		WHERE id_member = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);

	// Delete the member.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}members
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);

	// Delete the logs...
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_actions
		WHERE id_log = {int:log_type}
			AND id_member = ({int:user})',
		array(
			'log_type' => 2,
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_boards
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_comments
		WHERE id_recipient = ({int:user})
			AND comment_type = {string:warntpl}',
		array(
			'user' => $member_id,
			'warntpl' => 'warntpl',
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_group_requests
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_karma
		WHERE id_target = ({int:user})
			OR id_executor = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_mark_read
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_notify
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_online
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_subscribed
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_topics
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}collapsed_categories
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);

	// Make their votes appear as guest votes - at least it keeps the totals right.
	//!!! Consider adding back in cookie protection.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_polls
		SET id_member = {int:guest_id}
		WHERE id_member = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);

	// Delete personal messages.	
	deleteMessages(null, null, array($member_id));

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}personal_messages
		SET id_member_from = {int:guest_id}
		WHERE id_member_from = ({int:user})',
		array(
			'guest_id' => 0,
			'user' => $member_id,
		)
	);

	// They no longer exist, so we don't know who it was sent to.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}pm_recipients
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);

	// Delete avatar.	
	removeAttachments(array('id_member' => array($member_id)));

	// It's over, no more moderation for you.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}moderators
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}group_moderators
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);

	// If you don't exist we can't ban you.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}spamblocker_blacklist
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);
			
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}ban_items
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);

	// Remove individual theme settings.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}themes
		WHERE id_member = ({int:user})',
		array(
			'user' => $member_id,
		)
	);

	// These users are nobody's buddy nomore.
	$request = $smcFunc['db_query']('', '
		SELECT id_member, pm_ignore_list, buddy_list
		FROM {db_prefix}members
		WHERE FIND_IN_SET({raw:pm_ignore_list}, pm_ignore_list) != 0 OR FIND_IN_SET({raw:buddy_list}, buddy_list) != 0',
		array(
			'pm_ignore_list' => implode(', pm_ignore_list) != 0 OR FIND_IN_SET(', array($member_id)),
			'buddy_list' => implode(', buddy_list) != 0 OR FIND_IN_SET(', array($member_id)),
		)
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}members
			SET
				pm_ignore_list = {string:pm_ignore_list},
				buddy_list = {string:buddy_list}
			WHERE id_member = {int:id_member}',
			array(
				'id_member' => $row['id_member'],
				'pm_ignore_list' => implode(',', array_diff(explode(',', $row['pm_ignore_list']), array($member_id))),
				'buddy_list' => implode(',', array_diff(explode(',', $row['buddy_list']), array($member_id))),
			)
		);
	$smcFunc['db_free_result']($request);

	// Make sure no member's birthday is still sticking in the calendar...
	updateSettings(array(
		'calendar_updated' => time(),
	));

	updateStats('member');
	
	return true;
}

function spamBlockerClearBlacklist()
{
	global $smcFunc, $sourcedir;
	
	$datum = array();
	$member_id = array();
	$ban_group = array();
	$columns = array('id_ban_group', 'expire_time', 'reason', 'notes', 'id_member');
	
	$request = $smcFunc['db_query']('', "SELECT gp.id_ban_group, gp.expire_time, gp.reason, gp.notes, item.id_member FROM {db_prefix}ban_groups AS gp
						LEFT JOIN {db_prefix}ban_items AS item ON (item.id_ban_group = gp.id_ban_group)");
			
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		foreach ($columns as $column)
			$data[$column] = $row[$column];
                           	
		$datum[] = $data;
	}
	$smcFunc['db_free_result']($request);

	foreach ($datum as $key => $check_data)
	{
		if (strpos($check_data['notes'], 'spamBlocker_id-') !== false)
		{
			$member_id[] = $check_data['id_member'];
			$ban_group[] = $check_data['id_ban_group'];
		}
	}

	foreach ($member_id as $id_member)
	{
		if ((int)$id_member == 0)
			continue;
	
		spamBlockerDeleteMember($member_id);
	}

	foreach ($ban_group as $group)
	{
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
	        $request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
	}
}

function checkSFS_APIkey($key=false)
{
	global $sourcedir;
	
	if (!$key)
		return $key;
	
	$sfs ='http://www.stopforumspam.com/add.php?username=USERNAME&ip_addr=IPADDRESS&email=EMAILADDRESS&api_key='.$key.'&f=xmldom';
	$html = false;
	$message = 'No connection';
	
	/* Use a socket connection to get the html ... this was commented out as it is not working for the key check ... reason unknown */
	// require_once($sourcedir . '/Subs-Package.php');
	// $html = fetch_web_data($sfs);
	
	if ((empty($_SERVER['HTTP_USER_AGENT'])) || !$_SERVER['HTTP_USER_AGENT'])
		$agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:19.0) Gecko/20100101 Firefox/19.0';
	else
		$agent = $_SERVER['HTTP_USER_AGENT'];
			
	/* Use cURL to get the html */
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);		
	curl_setopt($ch, CURLOPT_URL,$sfs);
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5); 
	$html = curl_exec($ch);
	curl_close($ch);	
		
	if ($html)
	{
		if (strpos(trim($html), 'could not validate api key') !== false)					
			$message = 'Invalid SFS Key';
		else
			$message = 'Valid SFS Key';
	}

	return $message;
}

function spamBlockerDefaultReset()
{
	global $modSettings, $smcFunc, $sourcedir, $txt;
	loadLanguage('SpamBlocker');
	require_once($sourcedir . '/ManageServer.php');	
	$message = $txt['spamBlocker_defaultConfig']; 
	
	$setting_types = array('user_message', 'error_message', 'honeypot_key', 'sfs_key', 'akismet_key');	
	
	$result = $smcFunc['db_query']('', "SELECT user_message, error_message
						FROM {db_prefix}spamblocker_settings
						WHERE reference = 1");
	while ($val = $smcFunc['db_fetch_assoc']($result))
	{
		foreach ($setting_types as $setting_type)
		{									
			if (empty($val[$setting_type])) 
				$val[$setting_type] = false;
				
			$settings[$setting_type] = trim($val[$setting_type]);
		}			
					
	}
	$smcFunc['db_free_result']($result);
	
	$settings['honeypot_key'] = !empty($modSettings['spamBlocker_honeypotKey']) ? trim($modSettings['spamBlocker_honeypotKey']) : false;
	$settings['sfs_key'] = !empty($modSettings['spamBlocker_sfsKey']) ? trim($modSettings['spamBlocker_sfsKey']) : false;
	$settings['akismet_key'] = !empty($modSettings['spamBlocker_akismetKey']) ? trim($modSettings['spamBlocker_akismetKey']) : false;
	$settings['redirect_path'] = urldecode(!empty($modSettings['spamBlocker_redirectPath']) ? $modSettings['spamBlocker_redirectPath'] : false);
	
	$new_setting_types = array(
				'user_message' => $settings['user_message'] ? $settings['user_message'] : 'Access denied',
				'error_message' => $settings['error_message'] ? $settings['error_message'] : 'SpamBlocker IP Ban', 
				'enable_errorlog' => 2,
				'enable_sfs' => 1,
				'enable_akismet' => $settings['akismet_key'] ? 1 : 0,
				'enable_honeypot' => $settings['honeypot_key'] ? 1 : 0,
				'enable_spamhaus' => 1,
				'enable_email' => 1,
				'enable_pass' => 1,
				'enable_reset' => 1,
				'ban_option' => 1,
				'ban_full' => 1,
				'ban_post' => 0,
				'ban_register' => 0,
				'ban_login' => 0,
				'expiration' => 1,
				'expire_time' => 6,
			);
	
	foreach ($new_setting_types as $setting => $value)
	{
		$request = $smcFunc['db_query']('', "UPDATE {db_prefix}spamblocker_settings SET {$setting} = '{$value}'");
		continue;
	}	
	
	if ($settings['akismet_key'])
	{
		$setArray['spamBlocker_akismetPost'] = 1;
		updateSettings($setArray);			
		$modSettings['spamBlocker_akismetPost'] = 1;		
	}
	else
	{
		$setArray['spamBlocker_akismetPost'] = 2;
		updateSettings($setArray);			
		$modSettings['spamBlocker_akismetPost'] = 2;		
	}
	
	if ($settings['sfs_key'])
	{
		$setArray['spamBlocker_PostSFS'] = 1;
		updateSettings($setArray);			
		$modSettings['spamBlocker_PostSFS'] = 1;
	}
	else
	{
		$setArray['spamBlocker_PostSFS'] = 2;
		updateSettings($setArray);			
		$modSettings['spamBlocker_PostSFS'] = 2;
	}	
	
	if (!$settings['sfs_key'] && !$settings['akismet_key'])
	{
		$setArray = array('spamBlocker_PostDisplay' => 2, 'spamBlocker_postCount' => 0, 'spamBlocker_report_errs' => 2);
		updateSettings($setArray);
		$modSettings['spamBlocker_postCount'] = 0;
		$modSettings['spamBlocker_PostDisplay'] = 2;		
		$modSettings['spamBlocker_report_errs'] = 2;
	}
	else
	{
		$setArray = array('spamBlocker_PostDisplay' => 1, 'spamBlocker_postCount' => 5, 'spamBlocker_report_errs' => 1);
		updateSettings($setArray);
		$modSettings['spamBlocker_postCount'] = 5;
		$modSettings['spamBlocker_PostDisplay'] = 1;		
		$modSettings['spamBlocker_report_errs'] = 1;
	}
	
	if (filter_var($settings['redirect_path'], FILTER_VALIDATE_URL) !== false)
	{
		$setArray['spamBlocker_enableRedirect'] = 1;
		updateSettings($setArray);			
		$modSettings['spamBlocker_enableRedirect'] = 1;	
	}
	else
	{
		$setArray['spamBlocker_enableRedirect'] = 2;
		updateSettings($setArray);			
		$modSettings['spamBlocker_enableRedirect'] = 2;
	}
	
	$setArray = array('spamBlocker_smfError' => 2, 'spamBlocker_hideMembers' => 2, 'spamBlocker_conn_errs' => 1, 'spamBlocker_enable' => 1, 'spamBlocker_deleteMembers' => 1, 'spamBlocker_honeypotThreat' => 0, 'spamBlocker_honeypotType' => 0, 'spamBlocker_PostFilter' => 1, 'spamBlocker_linksCount' => 0, 'spamBlocker_imagesCount' => 0, 'spamBlocker_charsCount' => 300, 'spamBlocker_filteredText' => cleanSpamBlockerInput($txt['spamBlocker_textFilter']), 'spamBlocker_charsLowCount' => 10, 'spamBlocker_wordCount' => 1);	
	updateSettings($setArray);			
	$modSettings['spamBlocker_smfError'] = 2;
	$modSettings['spamBlocker_hideMembers'] = 2;
	$modSettings['spamBlocker_conn_errs'] = 1;	
	$modSettings['spamBlocker_enable'] = '1';	
	$modSettings['spamBlocker_deleteMembers'] = 1;
	$modSettings['spamBlocker_honeypotThreat'] = 0;
	$modSettings['spamBlocker_honeypotType'] = 0;
	$modSettings['spamBlocker_PostFilter'] = 1;
	$modSettings['spamBlocker_linksCount'] = 0;
	$modSettings['spamBlocker_imagesCount'] = 0;
	$modSettings['spamBlocker_charsCount'] = 300;
	$modSettings['spamBlocker_wordCount'] = 1;
	$modSettings['spamBlocker_charsLowCount'] = 10;	
	$modSettings['spamBlocker_filteredText'] = cleanSpamBlockerInput($txt['spamBlocker_textFilter']);
	return $message;
}
?>
