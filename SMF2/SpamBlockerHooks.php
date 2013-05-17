<?php

/*
 *	Spam Blocker for SMF - Modification v1.0	 
 *	c/o Underdog @ http://askusaquestion.net	
 *	This file is for the integration hooks		
*/

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://askusaquestion.net	
 * Copyright 2013 Underdog@askusaquestion.net
 * This software package is distributed under the terms of its Freeware License
 * http://askusaquestion.net/index.php/page=spamblocker_license
*/

if (!defined('SMF'))
	die('Hacking attempt...');
	
/*	This file handles all of Spam Blocker's integrated hooks

	void function spamBlocker_array_insert(&$input, $key, $insert, $where = 'before', $strict = false)
		- General function to insert data into array
		
	void function spamBlocker_actions(&$actionArray)
		- Inserts necessary data into SMF index action array
		
	void function spamBlocker_actions(&$actionArray)
		- Inserts necessary data into SMF permissions array	
		
	void function spamBlocker_admin_areas(&$admin_areas)
		- Inserts necessary data into SMF Admin array
		
	void function spamBlocker_login($user, $hash_psswd, $cookie)
		- Adjusts database tables for expired bans
		- returns true if user was not in blacklist
		- returns 'retry' if an expired ban entity was adjusted
		
	void spamBlocker_akismet_topic($msg_options, $topic_options, $poster_options)
		- Filters started topic through Akismet database
		- Returns prior to filtering if Admin, mod is disabled, absolved membergroup or users is over the preset threshold 
		- Ignores filter if Akismet key is invalid
		- Adjusts necessary database tables for unapproved topic
	
	void spamBlocker_akismet_reply($msg_options, $topic_options, $poster_options)
		- Filters post through Akismet database
		- Returns prior to filtering if Admin, mod is disabled, absolved membergroup or user is over the preset threshold 
		- Ignores filter if Akismet key is invalid
		- Adjusts necessary database tables for unapproved post
		
	void spamBlocker_allExpired()
		- daily maintenance function commences at login
		- deletes all ban & blacklist entries created by this mod that have expired 
		- ensures member id is omitted from the members table
		- returns true if any entries were deleted else false		
	
	void function spamBlocker_register(&$regOptions, &$theme_vars)
		- initiates the registration check
		- returns the associated id_ban_group value within the $regOptions array

*/	
	
function spamBlocker_array_insert(&$input, $key, $insert, $where = 'before', $strict = false)
{
	$position = array_search($key, array_keys($input), $strict);
	
	// Key not found -> insert as last
	if ($position === false)
	{
		$input = array_merge($input, $insert);
		return;
	}
	
	if ($where === 'after')
		$position += 1;

	// Insert as first
	if ($position === 0)
		$input = array_merge($insert, $input);
	else
		$input = array_merge(
			array_slice($input, 0, $position, true),
			$insert,
			array_slice($input, $position, null, true)
		);		
}
	
function spamBlocker_actions(&$actionArray)
{
	global $modSettings;
	loadLanguage('SpamBlocker');
		
	$actionArray['SpamBlocker'] = array('SpamBlockerAdmin.php', 'spamblocker');
	$actionArray['SpamBlockerReport'] = array('Subs-SpamBlocker.php', 'spamBlockerReportPost');
}


function spamBlocker_load_permissions(&$permissionGroups, &$permissionList, &$leftPermissionGroups, &$hiddenPermissions, &$relabelPermissions)
{
	global $context;
	loadLanguage('SpamBlocker');
		
	$permissionList['membergroup'] += array(			
			'spamBlocker_settings' => array(false, 'spamBlocker_perms', 'spamBlocker_perms'),
			'spamBlocker_postCount' => array(false, 'spamBlocker_perms', 'spamBlocker_perms'),
	);
	
	$context['non_guest_permissions'] = array_merge(
		$context['non_guest_permissions'],
		array(
			'spamBlocker_settings',
			'spamBlocker_postCount',
		)
	);
	
	$permissionGroups['membergroup']['simple'] += array(
			'spamBlocker_perms',		
	);	
	$permissionGroups['membergroup']['classic'] += array(
			'spamBlocker_perms',		
	);	
		
}

function spamBlocker_admin_areas(&$admin_areas)
{
	global $context, $modSettings, $scripturl, $txt;
	loadLanguage('SpamBlocker');
	
	$subsections = array(
				'spamBlockerSettings' => array($txt['spamBlocker_settings']),
				'spamBlockerWhitelist' => array($txt['spamBlockerWhitelist']),
				'spamBlockerBlacklist' => array($txt['spamBlockerBlacklist']),
				'spamBlockerLookup' => array($txt['spamBlockerLookup']),
			);
	
	spamBlocker_array_insert($admin_areas, 'members',
			array(
				'spamBlocker_admin' => array(
				'title' => $txt['spamBlocker_tabtitle'],
				'permission' => array('spamBlocker_settings'),
				'areas' => array(
					'spamBlocker' => array(
						'label' => $txt['spamBlocker_settings'],
						'file' => 'SpamBlockerAdmin.php',
						'function' => 'spamBlocker',
						'icon' => 'spamBlocker_settings.png',
						'permission' => array('spamBlocker_settings'),			
						'subsections' => $subsections,
					),
					'spamBlockerLicense' => array(
						'label' => $txt['spamBlocker_license'],
						'file' => 'SpamBlockerAdmin.php',
						'function' => 'LicenseSpamBlocker',
						'icon' => 'spamBlocker_license.png',
						'permission' => array('spamBlocker_settings'),			
						'subsections' => array(),
					),
					'spamBlockerGuide' => array(
						'label' => $txt['spamBlocker_guide'],
						'file' => 'SpamBlockerAdmin.php',
						'function' => 'GuideSpamBlocker',
						'icon' => 'spamBlocker_guide.png',
						'permission' => array('spamBlocker_settings'),			
						'subsections' => array(),
					),
				),
			),
		)
	);
}

function spamBlocker_login($user, $hash_psswd, $cookie)
{
	/* This should be initiated even if the Spam Blocker mod is disabled else previously banned entites will gain access */
	/* Expired entities will need to register anew to be checked again! */	
	
	global $sourcedir;	
	
	require_once($sourcedir . '/Subs-SpamBlocker.php');
	
	/* First check for expired Spam Blocker bans */ 
	$check = spamBlockerLoginExpired($user);
	
	if ($check)
		return 'retry';
	
	return true;	
}

function spamBlocker_akismet_topic($msg_options, $topic_options, $poster_options)
{
	global $smcFunc, $modSettings, $scripturl, $sourcedir, $context, $user_info, $txt;
	
	if ((!empty($modSettings['spamBlocker_enable']) ? (int)$modSettings['spamBlocker_enable'] : 2) != 1)
		return false;
	
	require_once($sourcedir . '/SpamBlockerAkismet.php');	
	require_once($sourcedir . '/Subs-SpamBlocker.php');
	require_once($sourcedir . '/Errors.php');
	loadLanguage('SpamBlocker');
	
	/* Fetch the Akismet API key */
	$spamBlocker['akismet_key'] = !empty($modSettings['spamBlocker_akismetKey']) ? $modSettings['spamBlocker_akismetKey'] : false;
	$spamBlocker['postmod'] = !empty($modSettings['postmod_active']) ? (int)$modSettings['postmod_active'] : 0;
	$spamBlocker['enable_akismet_post'] = !empty($modSettings['spamBlocker_akismetPost']) ? (int)$modSettings['spamBlocker_akismetPost'] : 1;
	$spamBlocker['postCount'] = !empty($modSettings['spamBlocker_postCount']) ? (int)$modSettings['spamBlocker_postCount'] : 0;
	$spamBlocker['enable'] = !empty($modSettings['spamBlocker_enable']) ? (int)$modSettings['spamBlocker_enable'] : 2;
	$spamBlocker['reporting_errors'] = !empty($modSettings['spamBlocker_report_errs']) ? (int)$modSettings['spamBlocker_report_errs'] : 1;
	$spamflag = false;
	
	/* Admins, absolved membergroups or users over the preset threshold are exempt */
	if ($context['user']['is_admin'] || $spamBlocker['enable'] != 1)
		return;
	elseif ((int)$user_info['posts'] > (int)$spamBlocker['postCount'] && (int)$spamBlocker['postCount'] != 0)
		return;
	elseif (!AllowedTo('spamBlocker_postCount'))	
		return; 	
	
	/* An available and valid Akismet API key, Post Moderation & Spam Blocker Post Filter must be enabled for this feature */
	if ($spamBlocker['akismet_key'] && ((int)$spamBlocker['enable_akismet_post'] + (int)$spamBlocker['postmod']) == 2)
	{
		$akismet = new Akismet($scripturl ,$spamBlocker['akismet_key']);
		if ($akismet->isKeyValid())
		{
			$akismet->setCommentAuthor($poster_options['name']);
			$akismet->setCommentAuthorEmail($poster_options['email']);
			// $akismet->setCommentAuthorURL($url);
			$akismet->setCommentContent($msg_options['body']);
			$url = $scripturl . '/topic,'.$topic_options['id'].'.html';			
			
			$akismet->setPermalink($url); 
			if($akismet->isCommentSpam() === 'connection_error' && (!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1)
				log_error(sprintf($txt['spamBlocker_akismetError'], $poster_options['name'], $url));
			elseif($akismet->isCommentSpam())
				$spamflag = true;
		}		
	}
	
	if ($spamflag)
	{			
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET approved = 0, unapproved_posts = 1
			WHERE id_topic = {int:id_topic}',
			array('id_topic' => (int)$topic_options['id']));	
		
		
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}messages
			SET approved = 0, spamblocker = 1
			WHERE id_msg = {int:id_msg}',
			array('id_msg' => (int)$msg_options['id']));
		
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}boards
			SET unapproved_topics = unapproved_topics + 1, 	unapproved_posts = unapproved_posts + 1		
			WHERE id_board = {int:id_board}',
			array('id_board' => (int)$topic_options['board']));
		
		/* Enter the data into the log if that option is enabled */
		if ($spamBlocker['reporting_errors'] == 1)
			log_error(sprintf($txt['spamBlockerReport'] . ' - <span class="remove">' . $user_info['ip'] . '</span>', $txt['spamBlockerReportMsg']));
	}		
}

function spamBlocker_akismet_reply($msg_options, $topic_options, $poster_options)
{
	global $smcFunc, $modSettings, $scripturl, $sourcedir, $context, $user_info, $txt;
	
	if ((!empty($modSettings['spamBlocker_enable']) ? (int)$modSettings['spamBlocker_enable'] : 2) != 1)
		return false;
	
	require_once($sourcedir . '/SpamBlockerAkismet.php');
	require_once($sourcedir . '/Subs-SpamBlocker.php');
	require_once($sourcedir . '/Errors.php');
	loadLanguage('SpamBlocker');
	
	/* Fetch the Akismet API key */
	$spamBlocker['akismet_key'] = !empty($modSettings['spamBlocker_akismetKey']) ? $modSettings['spamBlocker_akismetKey'] : false;
	$spamBlocker['postmod'] = !empty($modSettings['postmod_active']) ? (int)$modSettings['postmod_active'] : 0;
	$spamBlocker['enable_akismet_post'] = !empty($modSettings['spamBlocker_akismetPost']) ? (int)$modSettings['spamBlocker_akismetPost'] : 1;
	$spamBlocker['postCount'] = !empty($modSettings['spamBlocker_postCount']) ? (int)$modSettings['spamBlocker_postCount'] : 0;
	$spamBlocker['reporting_errors'] = !empty($modSettings['spamBlocker_report_errs']) ? (int)$modSettings['spamBlocker_report_errs'] : 1;
	$spamflag = false;
	
	/* Admins, absolved membergroups or users over the preset threshold are exempt */
	if ($context['user']['is_admin'])
		return;
	elseif ((int)$user_info['posts'] > (int)$spamBlocker['postCount'] && (int)$spamBlocker['postCount'] != 0)
		return;
	elseif (!AllowedTo('spamBlocker_postCount'))	
		return; 	
	
	/* An available and valid Akismet API key, Post Moderation & Spam Blocker Post Filter must be enabled for this feature */
	if ($spamBlocker['akismet_key'] && ((int)$spamBlocker['enable_akismet_post'] + (int)$spamBlocker['postmod']) == 2)
	{
		$akismet = new Akismet($scripturl ,$spamBlocker['akismet_key']);
		if ($akismet->isKeyValid())
		{
			$akismet->setCommentAuthor($poster_options['name']);
			$akismet->setCommentAuthorEmail($poster_options['email']);
			// $akismet->setCommentAuthorURL($url);
			$akismet->setCommentContent($msg_options['body']);
			$url = $scripturl . '/topic,'.$topic_options['id'].'.msg'.$msg_options['id'].'.html#msg'.$msg_options['id'];
			
			$akismet->setPermalink($url); 
			if($akismet->isCommentSpam() === 'connection_error' && (!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1)
				log_error(sprintf($txt['spamBlocker_akismetError'], $poster_options['name'], $url));
			elseif($akismet->isCommentSpam())
				$spamflag = true;
		}		
	}
	
	if ($spamflag)
	{		
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}topics
			SET unapproved_posts = unapproved_posts + 1
			WHERE id_topic = {int:id_topic}',
			array('id_topic' => (int)$topic_options['id']));
				
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}messages
			SET approved = 0, spamblocker = 1
			WHERE id_msg = {int:id_msg}',
			array('id_msg' => (int)$msg_options['id']));
		
		$smcFunc['db_query']('', '
			UPDATE {db_prefix}boards
			SET unapproved_posts = unapproved_posts + 1
			WHERE id_board = {int:id_board}',
			array('id_board' => (int)$topic_options['board']));
		
		/* Enter the data into the log if that option is enabled */
		if ($spamBlocker['reporting_errors'] == 1)
			log_error(sprintf($txt['spamBlockerReport'] . ' - <span class="remove">' . $user_info['ip'] . '</span>', $txt['spamBlockerReportMsg']));
	}	
}

/* Daily deletion of expired bans ~ 1000 queries every 6 hours = 4000 max queries a day (3 hour interval for queries exceeding 500) */
function spamBlocker_allExpired()
{
	global $smcFunc, $modSettings, $sourcedir;
	
	if ((!empty($modSettings['spamBlocker_enable']) ? (int)$modSettings['spamBlocker_enable'] : 2) != 1)
		return false;
	
	require_once($sourcedir . '/ManageServer.php');
	require_once($sourcedir . '/Subs-SpamBlocker.php');
	$checkTime = !empty($modSettings['spamBlocker_Expired']) ? (int)$modSettings['spamBlocker_Expired'] : 0;
	$currentTime = time() + 86400;
	$expireTime = strtotime('+6 hours', $currentTime);
	if ((int)$checkTime == 0)
	{
		$setArray['spamBlocker_Expired'] = (int)$expireTime;
		updateSettings($setArray);			
		$modSettings['spamBlocker_Expired'] = (int)$expireTime;	
		return false;	
	}
	
	if ((int)$currentTime < (int)$checkTime)
		return false;
	
	/* Delete expired bans */	
	$datum = array();
	$member_id = array();
	$ban_group = array();
	$count = 0;
	$columns = array('id_ban_group', 'expire_time', 'reason', 'notes', 'id_member');

	$request = $smcFunc['db_query']('', "
			SELECT gp.id_ban_group, gp.expire_time, gp.reason, gp.notes, item.id_member
			FROM {db_prefix}ban_groups AS gp
			LEFT JOIN {db_prefix}ban_items AS item ON (item.id_ban_group = gp.id_ban_group)
			WHERE {int:now} > gp.expire_time
			LIMIT 500",array('now' => $currentTime));
			
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		foreach ($columns as $column)
			$data[$column] = $row[$column];
                         	
		$datum[] = $data;
		$count++;
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
		if ((int)$id_member > 0)
			spamBlockerMemberDelete($member_id);
	}

	foreach ($ban_group as $group)
	{
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$group));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$group));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$group));
	}
	
	if ((int)$count > 498)
		$expireTime = strtotime('+3 hours', $currentTime);
	
	$setArray['spamBlocker_Expired'] = (int)$expireTime;
	updateSettings($setArray);			
	$modSettings['spamBlocker_Expired'] = (int)$expireTime;	
	return true;
}

function spamBlocker_register(&$regOptions, &$theme_vars)
{
	global $sourcedir, $user_info, $smcFunc;
	
	require_once($sourcedir.'/SpamBlocker.php');	
	$regOptions['spamBlocker'] = spamBlockerRegister($smcFunc['strtolower']($regOptions['username']), $regOptions['email'], $user_info['ip'], false);
}
?>