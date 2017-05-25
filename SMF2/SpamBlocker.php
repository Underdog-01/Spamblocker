<?php
// Version: 1.0; Spam Blocker 

/*
 *	General Functions file for the Spam Blocker Mod		
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
	
/*	This file handles Spam Blocker's main functions.

	void function spamBlockerRegister($name, $email, $ipUser, $data)
		- If executed for general check, $data should be true which will return false for pass and true for fail
		- Primarily executed from /Sources/Register.php
		- Denies access or adds a ban trigger for the ip/email (fatal_error message)
		- Optimizes all related tables on preset number of spam entities
		- Logs out entity flagged as spam
		- Sets 24 hour cache for IP, time and pass/fail
		- Returns false for IP's that are not flagged as spam
			
	void function SpamBlockerCheck($email,$ip, $name, $checkCache)
		- Does not perform the below checks if IP already exists in the 24 hour cache
		- Uses a socket connection or gethostbyname to run an external check on a given ip/email
		- Returns an array of data including ip-check results & database query
		
*/	
	
function spamBlockerRegister($name, $email, $ipUser, $data)
{
	global $smcFunc, $language, $sourcedir, $user_info, $db_name, $db_type, $txt, $context, $modSettings, $settings, $cookiename, $scripturl;	
	loadLanguage('SpamBlocker');
	
	/* Sanitize the inputs prior to processing */
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);	
	$name = filter_var($name, FILTER_SANITIZE_STRING);
	
	require_once($sourcedir.'/ManageBans.php');
	require_once($sourcedir.'/Errors.php');
	require_once($sourcedir . '/ManageServer.php');
	require_once($sourcedir . '/LogInOut.php');
	require_once($sourcedir . '/Subs-Auth.php');
	require_once($sourcedir . '/Subs-SpamBlocker.php');	
	
	/* Check the whitelist && 24 hour cache ... */
	$checkCache = IPspamBlockerCache(trim($ipUser));
	if (IPspamBlockerExists(trim($ipUser)))
		return false;	
	elseif ($checkCache && $checkCache !== 'fail')
		return false;	
	
	$ip = trim($ipUser);
	$ip_array = explode('.', $ip);
	$ip_parts = ip2range($ip);
	$memberID = 0;	
	$time = time();
	$checkDelete = !empty($modSettings['spamBlocker_deleteMembers']) ? (int)$modSettings['spamBlocker_deleteMembers'] : 1;
	$redirect_path = urldecode(!empty($modSettings['spamBlocker_redirectPath']) ? $modSettings['spamBlocker_redirectPath'] : false);
	$enable_redirect = !empty($modSettings['spamBlocker_enableRedirect']) ? (int)$modSettings['spamBlocker_enableRedirect'] : 2;
	
	/* Delete expired bans matching ip/email and then check the current user/entity */
	spamBlockerExpired($ip_parts, $email);
	
	$spamBlocker = SpamBlockerCheck($email, $ipUser, $name, $checkCache);	
	list($spamcheck, $user_message, $error_message, $enable_errorlog, $enable_mod, $enable_email, $enable_reset, $ban_option, $ban_full, $ban_post, $ban_register, $ban_login, $expiration, $expire_time) = $spamBlocker;
	$reason = !empty($user_message) ? $user_message : $txt['spamBlockerSpam'];					
									
	if ((int)$expiration == 0 || (int)$expire_time < 1)
		$date = false;				
	else
		$date = floor(((int)$expire_time * 86400) + time());	
		
	if ($spamcheck && count($ip_parts) == 4 && (int)$enable_mod == 1)
	{		
		if ($data && $ip !== $txt['spamBlocker_defaultIP'] && $spamcheck == true && !$checkCache)
		{
			list($ip_1, $ip_2, $ip_3, $ip_4) = $ip_array;
			$cache = array('ip_1' => $ip_1, 'ip_2' => $ip_2, 'ip_3' => $ip_3, 'ip_4' => $ip_4, 'ip_pass' => 0);
			cache_put_data('spamBlocker_cache', $cache, 3600);
			
			return true;
		}		
		elseif ($data)
			return false;		
				
		if ($enable_errorlog == 1)
			log_error(sprintf($error_message . ' - <span class="remove">' . $ipUser . '</span>', 'IP'));			
		
		if ((int)$ban_option == 1)
		{			
			$notes = 'spamBlocker_id-' . time();
			$key = 0;
			$foundName = false;
			if (!$name)
				$name = 'default_spammer';				
			
			$result = $smcFunc['db_query']('', "SELECT id_ban_group, name						
							FROM {db_prefix}ban_groups
							WHERE name = {string:name}", array('name' => $name));
			while ($val = $smcFunc['db_fetch_assoc']($result))
			{
				$key = (int)$val['id_ban_group'];
				$foundName = $val['name'];
			}			
			$smcFunc['db_free_result']($result);
			
			if (!$foundName)
			{	
				/* Add data to the ban_groups table */	
				if ((!$date) && (((int)$ban_full == 1) || ((int)$ban_post+(int)$ban_register+(int)$ban_login) == 0))			
					$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}ban_groups (`name`, `ban_time`, `expire_time`, `cannot_access`, `cannot_register`, `cannot_post`, `cannot_login`, `reason`, `notes`) VALUES ({string:name} , {int:ban_time}, NULL, 1, 0, 0, 0, {string:reason}, {string:notes})", array('name' => trim($name), 'ban_time' => (int)$time, 'reason' => $reason, 'notes' => $notes));
				elseif (!$date)
					$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}ban_groups (`name`, `ban_time`, `expire_time`, `cannot_access`, `cannot_register`, `cannot_post`, `cannot_login`, `reason`, `notes`) VALUES ({string:name}, {int:ban_time}, NULL, 0, {int:ban_register}, {int:ban_post}, {int:ban_login}, {string:reason}, {string:notes})", array('ban_post' => (int)$ban_post, 'ban_register' => (int)$ban_register, 'ban_login' => (int)$ban_login, 'name' => trim($name), 'ban_time' => (int)$time, 'reason' => $reason, 'notes' => $notes));
				elseif (((int)$ban_full == 1) || ((int)$ban_post+(int)$ban_register+(int)$ban_login) == 0)			
					$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}ban_groups (`name`, `ban_time`, `expire_time`, `cannot_access`, `cannot_register`, `cannot_post`, `cannot_login`, `reason`, `notes`) VALUES ({string:name}, {int:ban_time}, {int:ban_date}, 1, 0, 0, 0, {string:reason}, {string:notes})", array('name' => trim($name), 'ban_time' => (int)$time, 'ban_date' => $date, 'reason' => $reason, 'notes' => $notes));
				else
					$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}ban_groups (`name`, `ban_time`, `expire_time`, `cannot_access`, `cannot_register`, `cannot_post`, `cannot_login`, `reason`, `notes`) VALUES ({string:name}, {int:ban_time}, {int:ban_date}, 0, {int:ban_register}, {int:ban_post}, {int:ban_login}, {string:reason}, {string:notes})", array('ban_post' => (int)$ban_post, 'ban_register' => (int)$ban_register, 'ban_login' => (int)$ban_login, 'name' => trim($name), 'ban_time' => (int)$time, 'ban_date' => $date, 'reason' => $reason, 'notes' => $notes));
			
				$result = $smcFunc['db_query']('', "SELECT id_ban_group, notes						
								FROM {db_prefix}ban_groups
								WHERE notes = {string:notes}", array('notes' => $notes));
				while ($val = $smcFunc['db_fetch_assoc']($result))
					$key = (int)$val['id_ban_group'];
				
				$smcFunc['db_free_result']($result);
				
				if ((int)$key == 0)
				{	
					log_error(sprintf($txt['spamBlocker_registerError'], $name, $scripturl . '?action=register'));
					return false;
				}
				
				/* Do not change the spamBlocker_id- for notes ... this mod needs this for referencing */
				$notes = 'spamBlocker_id-' . (int)$key;
				$request = $smcFunc['db_query']('', "UPDATE {db_prefix}ban_groups SET notes = {string:notes} WHERE id_ban_group = {int:key}", array('notes' => $notes, 'key' => (int)$key));
			}
			/* Do not change the spamBlocker_id- for notes ... this mod needs this for referencing */
			else
			{
				if ((int)$key == 0)
				{	
					log_error(sprintf($txt['spamBlocker_registerError'], $name, $scripturl . '?action=register'));
					return false;
				}
				
				$notes = 'spamBlocker_id-' . (int)$key;			
			}
			
			
			/* Add the ban id to the spamblocker blacklist table */
			$request = $smcFunc['db_query']('', 'DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group LIKE {int:idkey}', array('idkey' => (int)$key));
			$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}spamblocker_blacklist (`id_ban_group`, `id_member`) VALUES ({int:idkey}, {int:member})", array('idkey' => (int)$key, 'member' => (int)$memberID));
			
			/* Add data to the ban_items table */ 				
			$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}ban_items (`id_ban_group`, `ip_low1`, `ip_high1`, `ip_low2`, `ip_high2`, `ip_low3`, `ip_high3`, `ip_low4`, `ip_high4`, `hostname`, `email_address`, `id_member`, `hits`) VALUES ({int:group} , {int:iplow1}, {int:iphigh1}, {int:iplow2}, {int:iphigh2}, {int:iplow3}, {int:iphigh3}, {int:iplow4}, {int:iphigh4}, {string:host}, {string:email}, {int:userid}, {int:hits})", array('group' => $key, 'iplow1' => (int)$ip_parts[0]['low'], 'iphigh1' => (int)$ip_parts[0]['high'], 'iplow2' => (int)$ip_parts[1]['low'], 'iphigh2' => (int)$ip_parts[1]['high'], 'iplow3' => (int)$ip_parts[2]['low'], 'iphigh3' => (int)$ip_parts[2]['high'], 'iplow4' => $ip_parts[3]['low'], 'iphigh4' => $ip_parts[3]['high'], 'host' => $name, 'email' => $email, 'userid' => $memberID, 'hits' => 0));
			if ((int)$enable_email == 1)
				$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}ban_items (`id_ban_group`, `ip_low1`, `ip_high1`, `ip_low2`, `ip_high2`, `ip_low3`, `ip_high3`, `ip_low4`, `ip_high4`, `hostname`, `email_address`, `id_member`, `hits`) VALUES ({int:group} , {int:iplow1}, {int:iphigh1}, {int:iplow2}, {int:iphigh2}, {int:iplow3}, {int:iphigh3}, {int:iplow4}, {int:iphigh4}, {string:host}, {string:email}, {int:userid}, {int:hits})", array('group' => $key, 'iplow1' => 0, 'iphigh1' => 0, 'iplow2' => 0, 'iphigh2' => 0, 'iplow3' => 0, 'iphigh3' => 0, 'iplow4' => 0, 'iphigh4' => 0, 'host' => false, 'email' => $email, 'userid' => 0, 'hits' => 0));
		
			$request = $smcFunc['db_query']('', "SELECT id_ban, id_ban_group FROM {db_prefix}ban_items WHERE id_ban_group LIKE {int:bangroup}",
							array('bangroup' => $key));
			
			while ($row = $smcFunc['db_fetch_assoc']($request))
			{
				$_SESSION['ban']['cannot_access']['ids'][] = $row['id_ban'];
				$_SESSION['ban']['cannot_access']['reason'] = $reason;
			}
			$smcFunc['db_free_result']($request);
		
		
			/* Add additional info to the ban log */
			$user_id = !empty($memberID) ? (int)$memberID : 0;	
			$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}log_banned (`id_member`, `ip`, `email`, `log_time`)
							VALUES ({int:userid} , {string:userip}, {string:useremail}, {string:usertime})",
							array('userid' => (int)$user_id, 'userip' => $ip, 'useremail' => $email, 'usertime' => (int)$time));

			/* Update member table and make sure the ban cache is refreshed. */
			updateSettings(array('banLastUpdated' => (int)$time));		
			updateBanMembers();
			$_SESSION['ban']['last_checked'] = (int)$time;
			$cookie_url = url_parts(!empty($modSettings['localCookies']), !empty($modSettings['globalCookies']));
			setcookie($cookiename . '_', implode(',', $_SESSION['ban']['cannot_access']['ids']), time() + 3153600, $cookie_url[1], $cookie_url[0], 0);
		}
		else
		{
			$_SESSION['ban']['cannot_access']['ids'][] = 0;
			$_SESSION['ban']['cannot_access']['reason'] = $reason;
			$cookie_url = url_parts(!empty($modSettings['localCookies']), !empty($modSettings['globalCookies']));
			setcookie($cookiename . '_', implode(',', $_SESSION['ban']['cannot_access']['ids']), time() + 3153600, $cookie_url[1], $cookie_url[0], 0);
		}
			
		if (((int)$ban_register + (int)$ban_login) != 0)
			unset($_SESSION['ban']['cannot_access']);	
			
		if (((int)$ban_full + (int)$ban_register + (int)$ban_login) != 0)
		{
			/* To be safe, prepare all necessary variables/arrays for the banned entity */
			$user_info['name'] = '';
			$user_info['username'] = '';
			$user_info['is_guest'] = true;
			$user_info['is_admin'] = false;
			$user_info['permissions'] = array();
			$user_info['id'] = 0;
			$context['user'] = array(
				'id' => 0,
				'username' => '',
				'name' => $txt['guest_title'],
				'is_guest' => true,
				'is_logged' => false,
				'is_admin' => false,
				'is_mod' => false,
				'can_mod' => false,
				'language' => !empty($language) ? $language : 'english',
			);		
				
			$_GET['action'] = '';
			$_GET['board'] = '';
			$_GET['topic'] = '';									
		
			/* Logout the spam entity prior to the possible optimization */
			Logout(true, false);
		}	
		elseif ((int)$ban_post == 1)
		{
			$denied_permissions = array(
				'pm_send',
				'calendar_post', 'calendar_edit_own', 'calendar_edit_any',
				'poll_post',
				'poll_add_own', 'poll_add_any',
				'poll_edit_own', 'poll_edit_any',
				'poll_lock_own', 'poll_lock_any',
				'poll_remove_own', 'poll_remove_any',
				'manage_attachments', 'manage_smileys', 'manage_boards', 'admin_forum', 'manage_permissions',
				'moderate_forum', 'manage_membergroups', 'manage_bans', 'send_mail', 'edit_news',
				'profile_identity_any', 'profile_extra_any', 'profile_title_any',
				'post_new', 'post_reply_own', 'post_reply_any',
				'delete_own', 'delete_any', 'delete_replies',
				'make_sticky',
				'merge_any', 'split_any',
				'modify_own', 'modify_any', 'modify_replies',
				'move_any',
				'send_topic',
				'lock_own', 'lock_any',
				'remove_own', 'remove_any',
				'post_unapproved_topics', 'post_unapproved_replies_own', 'post_unapproved_replies_any',
			);
			$user_info['permissions'] = array_diff($user_info['permissions'], $denied_permissions);
		}		
		
		/* Delete the flagged spam entity from the online user log (matching ip) */
		$where = 'ip' . $ipUser;
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}log_online WHERE session LIKE {string:who}", array('who' => $where));
		
		/* cache_put_data('modSettings', null, 90); */
		
		/* The 1 hour cache for a failing entity is only necessary if the member id was created */
		
		if (!$checkCache && (int)$ban_option != 1)
		{
			list($ip_1, $ip_2, $ip_3, $ip_4) = $ip_array;
			$cache = array('ip_1' => $ip_1, 'ip_2' => $ip_2, 'ip_3' => $ip_3, 'ip_4' => $ip_4, 'ip_pass' => 0);
			cache_put_data('spamBlocker_cache', $cache, 3600);
		}		
		
		/* Goodbye Spammer! */
		if ((int)$checkDelete != 1)
			return $key;
		elseif ($enable_redirect == 1 && filter_var($redirect_path, FILTER_VALIDATE_URL) !== false)		
			redirectexit($redirect_path, $context['server']['needs_login_fix']);
		else
			fatal_error($spamBlocker[1], false);	
	}	
	
	/* Create/record 1 hour cache for a passing entity so as not to process it again within that timeframe */	
	list($ip_1, $ip_2, $ip_3, $ip_4) = $ip_array;
	$cache = array('ip_1' => $ip_1, 'ip_2' => $ip_2, 'ip_3' => $ip_3, 'ip_4' => $ip_4, 'ip_pass' => 1);
	cache_put_data('spamBlocker_cache', $cache, 3600);
		
	/* Delete the duplicate guest in the user log (matching ip) */
	$where = 'ip' . $ipUser;
	$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}log_online WHERE session LIKE {string:who}", array('who' => $where));
	
	return false;
}

/* Check the Email & IP */
function SpamBlockerCheck($email,$ip, $name, $checkCache)
{
	global $smcFunc, $modSettings, $boardurl, $sourcedir, $scripturl, $txt, $context;
	loadLanguage('SpamBlocker');
	require_once($sourcedir . '/SpamBlockerAkismet.php');
	require_once($sourcedir . '/Subs-Package.php');
	
	$months = 0;
	$sfs = false;
	$feeds = array();
	$html = false;
	
	$setting_types = array('user_message', 'error_message', 'enable_errorlog', 'enable_mod', 'enable_sfs', 'enable_akismet', 'enable_honeypot', 'enable_email', 'enable_pass', 'enable_reset', 'ban_option', 'ban_full', 'ban_post', 'ban_register', 'ban_login', 'expiration', 'expire_time', 'enable_spamhaus');	
	$restrictions = array('ban_full', 'ban_post', 'ban_register', 'ban_login', 'expiration', 'expire_time');
	
	$result = $smcFunc['db_query']('', "SELECT user_message, error_message, enable_errorlog, enable_sfs, enable_akismet, enable_honeypot, enable_email, enable_reset, ban_option, ban_full, ban_post, ban_register, ban_login, expiration, expire_time, enable_spamhaus FROM {db_prefix}spamblocker_settings WHERE reference = 1");
	while ($val = $smcFunc['db_fetch_assoc']($result))
	{
		foreach ($setting_types as $setting_type)
		{					
			if (empty($val[$setting_type]) && in_array($setting_type, $restrictions)) 
				$val[$setting_type] = 0;
			elseif (empty($val[$setting_type]))
				$val[$setting_type] = false;
			
			$spamBlocker[$setting_type] = $val[$setting_type];
		}					
	}
	$smcFunc['db_free_result']($result);	
	
	/* Fetch the Akismet API key */
	$spamBlocker['akismet_key'] = !empty($modSettings['spamBlocker_akismetKey']) ? $modSettings['spamBlocker_akismetKey'] : false;
	$spamBlocker['enable_mod'] = !empty($modSettings['spamBlocker_enable']) ? (int)$modSettings['spamBlocker_enable'] : 1;
	$spamBlocker['honeypot_key'] = !empty($modSettings['spamBlocker_honeypotKey']) ? $modSettings['spamBlocker_honeypotKey'] : false;
	$spamBlocker['honeypot_type'] = !empty($modSettings['spamBlocker_honeypotType']) ? $modSettings['spamBlocker_honeypotType'] : false;
	$spamBlocker['honeypot_threat'] = !empty($modSettings['spamBlocker_honeypotThreat']) ? (int)$modSettings['spamBlocker_honeypotThreat'] : 0;	
	
	/* If permanently deleting members is enabled, these must be disabled */ 
	if ((!empty($modSettings['spamBlocker_deleteMembers']) ? (int)$modSettings['spamBlocker_deleteMembers'] : 1) == 1)
	{
		$spamBlocker['ban_post'] = 0;
		$spamBlocker['ban_login'] = 0;
	}
	
	/* Pass Flag & Data */
	$spamflag = array(false,$spamBlocker['user_message'], $spamBlocker['error_message'], $spamBlocker['enable_errorlog'], $spamBlocker['enable_mod'], $spamBlocker['enable_email'], $spamBlocker['enable_reset'], $spamBlocker['ban_option'], $spamBlocker['ban_full'], $spamBlocker['ban_post'], $spamBlocker['ban_register'], $spamBlocker['ban_login'], $spamBlocker['expiration'], $spamBlocker['expire_time']);
	
	if ((int)$spamBlocker['enable_mod'] != 1)
		return $spamflag;	
	
	if ($checkCache === 'fail')
		return array(true,$spamBlocker['user_message'], $spamBlocker['error_message'], $spamBlocker['enable_errorlog'], $spamBlocker['enable_mod'], $spamBlocker['enable_email'], $spamBlocker['enable_reset'], $spamBlocker['ban_option'], $spamBlocker['ban_full'], $spamBlocker['ban_post'], $spamBlocker['ban_register'], $spamBlocker['ban_login'], $spamBlocker['expiration'], $spamBlocker['expire_time']);
		
	/* Source: Stop Forum Spam ~ Anti spam resource for ip/email */	
	if ($email && (int)$spamBlocker['enable_email'] == 1 && (int)$spamBlocker['enable_sfs'] == 1)
		$sfs = 'http://www.stopforumspam.com/api?ip='.urlencode($ip).'&email='.urlencode($email);
	elseif ($email && (int)$spamBlocker['enable_email'] == 1)
		$sfs = 'http://www.stopforumspam.com/api?email='.urlencode($email);
	elseif ((int)$spamBlocker['enable_sfs'] == 1)
		$sfs = 'http://www.stopforumspam.com/api?ip='.$ip;	
	
	if ($sfs && !$spamflag[0])
	{		
		/* Use a socket connection to get the html */		
		$html = fetch_web_data($sfs) ? fetch_web_data($sfs) : false;
		
		if ((empty($html)) || !$html)
		{
			if ((!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1 && strpos($sfs, 'email') === false)
				log_error(sprintf($txt['spamBlocker_sfsErrorIp'], $name, $scripturl . '?action=register'));
			elseif ((!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1 && strpos($sfs, 'ip') === false)
				log_error(sprintf($txt['spamBlocker_sfsErrorEmail'], $name, $scripturl . '?action=register'));
			elseif ((!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1)
				log_error(sprintf($txt['spamBlocker_sfsErrorIpEmail'], $name, $scripturl . '?action=register'));			
		}
		elseif (strpos($html, 'rate limit exceeded') !== false && (!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1)
			log_error(sprintf($txt['spamBlocker_sfsErrorLimit'], $name, $scripturl . '?action=register'));	
		else
		{
			/* Check for 3 month exemption if opted */
			if ($spamBlocker['enable_pass'] == 1)
			{			
				$dom = new DOMDocument();
				libxml_use_internal_errors(true);
				$dom->loadHTML($html);
				libxml_use_internal_errors(false);
				$date_nodes = $dom->getElementsByTagName('lastseen');
				foreach ($date_nodes as $node)
				{
					$months = 0;
					$elapsed = (int)date('m', time()) - (int)date('m', strtotime(trim($node->nodeValue)));
					$years = 12 * ((int)date('Y', time()) - (int)date('Y', strtotime(trim($node->nodeValue))));
					if ($years > 0)
						$months = $years + $elapsed;
					else
						$months = $elapsed;
			
					if ((int)$months < 3)
						break;
					// $lastseen[] = $node->nodeValue;
				}
			}		
		
			$dom = new DOMDocument();
			libxml_use_internal_errors(true);
			$dom->loadHTML($html);
			libxml_use_internal_errors(false);
			$appear_nodes = $dom->getElementsByTagName('appears');
			foreach ($appear_nodes as $node)
			{
				if (trim($node->nodeValue) == 'yes' && (int)$months < 3)
				{
					$spamflag[0] = true;
					break;
				}	
				// $appears[] = $node->nodeValue;
			}
		}	
	}
	
	/* Source: Project Honeypot ~ Anti spam resource for ip */
	if ((int)$spamBlocker['enable_honeypot'] == 1 && $spamBlocker['honeypot_key'] && !$spamflag[0])
	{
		$lookup = $spamBlocker['honeypot_key'] . '.' . implode('.', array_reverse(explode ('.', $ip ))) . '.dnsbl.httpbl.org';
		$result = explode( '.', gethostbyname($lookup));			
		$pass = true;
		
		if ($result[0] == 127 && count($result) == 4)
		{				
			if ((int)$result[2] >= (int)$spamBlocker['honeypot_threat'])	
				$pass = false;
			
			$honeypotTypes = explode(',', $spamBlocker['honeypot_type']);
			if (in_array((int)$result[3], array_filter(array_map('trim', $honeypotTypes))))
				$pass = true;
			
			if ((int)$spamBlocker['enable_pass'] == 1 && (int)$result[1] > 90)
				$pass = true;	
			
			if (!$pass)
				$spamflag[0] = true;				
				
		}
		elseif ((!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1 && (!empty($result[0]) ? $result[0] : false) !== $spamBlocker['honeypot_key'])
			log_error(sprintf($txt['spamBlocker_honeypotError'], $name, $scripturl . '?action=register'));
		
		
	}
	
	/* Source: Spamhaus ~ Anti spam resource for ip */
	if ((int)$spamBlocker['enable_spamhaus'] == 1 && !$spamflag[0])
	{
		$lookup = implode('.', array_reverse(explode ('.', $ip ))) . '.sbl-xbl.spamhaus.org';
		$result = explode( '.', gethostbyname($lookup));		
		
		if ($result[0] == 127 && count($result) == 4)
		{				
			if ((int)$result[3] >= 2)	
				$spamflag[0] = true;				
		}
		elseif ((!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1 && (!empty($result[4]) ? $result[4] : false) !== 'sbl-xbl')
			log_error(sprintf($txt['spamBlocker_spamhausError'], $name, $scripturl . '?action=register'));
	}			
		
	/* Source: Akismet API ~ Anti spam resource for email and posts */	
	if ((int)$spamBlocker['enable_akismet'] == 1 && $spamBlocker['akismet_key'] && !$spamflag[0])
	{				
		$url = 'http://this_is_anti_spam_login_query_email_only.com';
		$comment = $txt['spamBlockerHello'] . $name;
		$API_Key = $spamBlocker['akismet_key'];
		
		$akismet = new Akismet($scripturl ,$API_Key);
		if ($akismet->isKeyValid())
		{
			$akismet->setCommentAuthor($name);
			$akismet->setCommentAuthorEmail($email);
			// $akismet->setCommentAuthorURL($url);
			$akismet->setCommentContent($comment);
			$akismet->setPermalink($scripturl . '?action=register2');

			if($akismet->isCommentSpam() === 'connection_error' && (!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1)
				log_error(sprintf($txt['spamBlocker_akismetError'], $name, $scripturl . '?action=register'));
			elseif($akismet->isCommentSpam())
				$spamflag[0] = true;
		}	
	}
	
	return $spamflag;
}
?>
