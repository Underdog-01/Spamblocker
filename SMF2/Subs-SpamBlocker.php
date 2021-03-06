<?php
// Version: 1.0; Spam Blocker 

/*
 *	General Sub-Routines file for the Spam Blocker Mod	
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
	
/*	This file handles Spam Blocker's general functions.

	void function IPspamBlockerExists($ip='255.255.255.254')	
		- check if IP exists in the whitelist
		- returns true if ip is in whitelist else returns false
	
	void function IPspamBlockerCache($ip='255.255.255.254')
		- check if IP exists in the 24 hour cache
		- returns true if time has not expired and IP exists in cache
		- returns false and removes entry if time has expired and IP exists in cache
		- returns 'fail' if IP exists in the cache and ip_pass was set to 0 (from a previous fail flag)
		- deletes all expired entries during the process
		- else returns false 
		
	void function spamBlockerExpired()	
		- deletes all ban & blacklist entries matching IP and email of registering user created by this mod that have expired
		- ensures member id is omitted from the members table
		- returns true if any entries were deleted else false
		
	void spamBlockerLoginExpired()	
		- deletes all ban & blacklist entries matching login name created by this mod that have expired
		- ensures member id is omitted from the members table
		- returns true if any entries were deleted else false
		
	void function spamBlockerMemberDelete($member_id)	
		- Deletes all data associated with the user id
		- Makes sure user id is not an administrator
		- Returns false if admin id was attempted or if $member_id is zero
		- Returns true if successful
		
	void function spamBlockerReport($reportedPost = array())		
		- Reports post as spam to Akismet database
		- Reports wrongfully flagged post as ham to Akisment database
		- Flags the message id as processed
		- Returns true if successfully reported else false
		
	void function spamBlockerReportSFS($data)		
		- Reports post as spam to Stop Forum Spam database
		- Initiated from the previous function if this option is enabled
		
	void function spamBlockerReportPost()
		- Initated from spam link in display template
		- Sets up necessary post data for reporting a post
		- Initiates spamBlockerReport function
		- Redirects back to topic and message id
		
	void function spamBlockerBan($memberID, $name, $email, $ipUser, $topic, $msg, $data)
		- Initiated for topics/posts reported as spam
		- If $data is true it will only return the database info as an array
		- Bans the entity from the forum dependent on config settings
		- Deletes the entity member data if that option was enabled in config
	
	void function spamBlockerPostFilter($text)
		- Filters the text for links, images and illegal words
		- Returns an array containing true/false for whether the text/links conform to the presets	
	
*/	

function IPspamBlockerExists($ip='255.255.255.254')
{
	global $sourcedir, $smcFunc;	
	
	$i = 0; 
	$ip_array = explode('.', $ip);
	$hi_low = array();	
	$ipdata = array('ip_low1','ip_high1','ip_low2','ip_high2','ip_low3','ip_high3','ip_low4','ip_high4');

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
			return true; 
	}
		
	return false;
}

function IPspamBlockerCache($ip='255.255.255.254', $pass = 1)
{
	global $sourcedir, $smcFunc;	
	
	$ip_array = explode('.', $ip);
	$ipdata = cache_get_data('spamBlocker_cache', 3600);	
	
	if (!empty($ip_data))
	{
		if (($ip_array[0] == $ip_data['ip_1']) && ($ip_array[1] == $ip_data['ip_2']) && ($ip_array[2] == $ip_data['ip_3']) && ($ip_array[3] == $ip_data['ip_4']) && ($ip_data['ip_pass'] == 1))
			return true;		
		elseif (($ip_array[0] == $ip_data['ip_1']) && ($ip_array[1] == $ip_data['ip_2']) && ($ip_array[2] == $ip_data['ip_3']) && ($ip_array[3] == $ip_data['ip_4']) && ($ip_data['ip_pass'] == 0))
			return 'fail';			
	}
	
	return false;
}

/* Delete expired bans */	
function spamBlockerExpired($ip_array, $email)
{	
	global $smcFunc;		
	$ban_ids = array();
	$check = false;	
	$yvalues = array('current_time' => time() + 86400, 'email' => $email);
	$xvalues = array(
			'ip_low1' => $ip_array[0]['low'],			
			'ip_low2' => $ip_array[1]['low'],			
			'ip_low3' => $ip_array[2]['low'],			
			'ip_low4' => $ip_array[3]['low'],			
			'current_time' => time() + 86400,
		);	
	
	$result = $smcFunc['db_query']('', "SELECT ban.id_ban_group, ban.expire_time, bi.id_member
						FROM {db_prefix}ban_groups AS ban						
						INNER JOIN {db_prefix}ban_items AS bi ON (bi.id_ban_group = ban.id_ban_group)											
						AND ip_low1 = {int:ip_low1} 
						AND ip_low2 = {int:ip_low2} 
						AND ip_low3 = {int:ip_low3} 
						AND ip_low4 = {int:ip_low4} 
						WHERE {int:current_time} > ban.expire_time  
						LIMIT 1", $xvalues);
	while ($val = $smcFunc['db_fetch_assoc']($result))
		$ban_ids[] = array('id_ban' => (!empty($val['id_ban_group']) ? $val['id_ban_group'] : 0), 'id_member' => (!empty($val['id_member']) ? $val['id_member'] : 0));				
	
	$smcFunc['db_free_result']($result);
	
	foreach ($ban_ids as $ref)
	{
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ref['id_ban']));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ref['id_ban']));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ref['id_ban']));	
		if ((int)$ref['id_member'] > 0)
			spamBlockerMemberDelete($ref['id_member']);
		$check = true;		
	}
	
	if (!$email)
		return $check;
	
	$ban_ids = array();
	$result = $smcFunc['db_query']('', "SELECT ban.id_ban_group, ban.expire_time, bi.email_address, bi.id_member
						FROM {db_prefix}ban_groups AS ban						
						LEFT JOIN {db_prefix}ban_items AS bi ON (bi.id_ban_group = ban.id_ban_group)
						WHERE {int:current_time} > ban.expire_time  
						AND bi.email_address = {string:email} 											
						LIMIT 1", $yvalues);
	while ($val = $smcFunc['db_fetch_assoc']($result))
		$ban_ids[] = array('id_ban' => (!empty($val['id_ban_group']) ? $val['id_ban_group'] : 0), 'id_member' => (!empty($val['id_member']) ? $val['id_member'] : 0));				
	
	$smcFunc['db_free_result']($result);
	
	foreach ($ban_ids as $ref)
	{
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group LIKE {int:ban}",array('ban' => $ref['id_ban']));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group LIKE {int:ban}",array('ban' => $ref['id_ban']));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group LIKE {int:ban}",array('ban' => $ref['id_ban']));	
		if ((int)$ref['id_member'] > 0)
			spamBlockerMemberDelete($ref['id_member']);
		$check = true;		
	}
	
	return $check;	
}

function spamBlockerLoginExpired($username)
{	
	global $smcFunc;		
	$ban_ids = array();
	$check = false;	
	
	if (!$username)
		return $check;
	
	$values = array('username' => $username, 'current_time' => time() + 86400);
		
	$result = $smcFunc['db_query']('', "SELECT ban.id_ban_group, ban.expire_time, ban.name, item.id_member
						FROM {db_prefix}ban_groups AS ban
						LEFT JOIN {db_prefix}ban_items AS item ON (item.id_ban_group = ban.id_ban_group)
						WHERE {int:current_time} > ban.expire_time  
						AND ban.name LIKE {string:username}
						LIMIT 1", $values);
	while ($val = $smcFunc['db_fetch_assoc']($result))
		$ban_ids[] = array('id_ban' => (!empty($val['id_ban_group']) ? $val['id_ban_group'] : 0), 'id_member' => (!empty($val['id_member']) ? $val['id_member'] : 0));				
	
	$smcFunc['db_free_result']($result);
	
	foreach ($ban_ids as $key => $ref)
	{
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ref['id_ban']));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ref['id_ban']));
		$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ref['id_ban']));	
		if ((int)$ref['id_member'] > 0)
			spamBlockerMemberDelete($ref['id_member']);
		$check = true;		
	}
	
	return $check;	
}

function spamBlockerMemberDelete($member_id = 0)
{
	global $smcFunc, $sourcedir;
		
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

function spamBlockerDefinition($select = 0)
{
	global $txt, $modSettings;	
	loadLanguage('SpamBlocker');
	$checkDelete = !empty($modSettings['spamBlocker_PostBan']) ? (int)$modSettings['spamBlocker_PostBan'] : 2;
	
	if ($checkDelete == 1)
		$spam[0] = array('value' => '0', 'title' => $txt['spamBlockerSpamTitle'], 'text' => $txt['spamBlockerSpamText'], 'disabled' => '', 'detail' => $txt['spamBlockerSpamDetail'] . $txt['spamBlockerSpamDelete']);
	else	
		$spam[0] = array('value' => '0', 'title' => $txt['spamBlockerSpamTitle'], 'text' => $txt['spamBlockerSpamText'], 'disabled' => '', 'detail' => $txt['spamBlockerSpamDetail']);
	
	$spam[1] = array('value' => '1', 'title' => $txt['spamBlockerSafeTitle'], 'text' => $txt['spamBlockerSafeText'], 'disabled' => '', 'detail' => $txt['spamBlockerSafeDetail']);
	$spam[2] = array('value' => '2', 'title' => $txt['spamBlockerNoSpamTitle'], 'text' => $txt['spamBlockerNoSpamText'], 'disabled' => 'disabled', 'detail' => $txt['spamBlockerNoSpamDetail']);
	$spam[3] = array('value' => '3', 'title' => $txt['spamBlockerNoSafeTitle'], 'text' => $txt['spamBlockerNoSafeText'], 'disabled' => 'disabled', 'detail' => $txt['spamBlockerNoSafeDetail']);
	
	return $spam[(int)$select];	
}

function spamBlockerReport($reportedPost = array())
{
	global $smcFunc, $modSettings, $boardurl, $scripturl, $txt, $sourcedir;
	loadLanguage('SpamBlocker');
	
	/* Fetch the Akismet/SFS API keys and check if they're enabled */ 
	$spamBlocker['akismet_key'] = !empty($modSettings['spamBlocker_akismetKey']) ? $modSettings['spamBlocker_akismetKey'] : false;
	$spamBlocker['SFS_key'] = !empty($modSettings['spamBlocker_sfsKey']) ? $modSettings['spamBlocker_sfsKey'] : false;
	$spamBlocker['akismet_enable'] = !empty($modSettings['spamBlocker_akismetPost']) ? (int)$modSettings['spamBlocker_akismetPost'] : 0;
	$spamBlocker['SFS_PostEnable'] = !empty($modSettings['spamBlocker_PostSFS']) ? (int)$modSettings['spamBlocker_PostSFS'] : 0;
	$spamBlocker['reporting_errors'] = !empty($modSettings['spamBlocker_report_errs']) ? (int)$modSettings['spamBlocker_report_errs'] : 1;
	
	if (!$spamBlocker['akismet_key'] || (int)$spamBlocker['akismet_enable'] != 1)
		return false;
	
	require_once($sourcedir . '/SpamBlockerAkismet.php');	
	require_once($sourcedir.'/Errors.php');
	$report = 0;	

	/* Fetch the necessary data from the database to send to Akismet */	
	$request = $smcFunc['db_query']('', '
		SELECT id_msg, id_topic, id_member, subject, poster_name, poster_email, poster_ip, body, approved, spamblocker
		FROM {db_prefix}messages
		WHERE id_msg = {int:message} AND id_topic = {int:topic} LIMIT 1',
		array('message' => $reportedPost['id_msg'], 'topic' => $reportedPost['id_topic'],)
	);
	while ($row = $smcFunc['db_fetch_assoc']($request))
	{
		$postArray = array('id_msg', 'id_topic', 'id_member', 'subject', 'poster_name', 'poster_email', 'poster_ip', 'body', 'approved', 'spamblocker');
		foreach ($postArray as $post)
		{
			if (!empty($row[$post]))
				$postData[$post] = $row[$post];
			else
				$postData[$post] = false;
		}
	}
	$smcFunc['db_free_result']($request);
	
	$akismet = new Akismet($scripturl.'?action=forum' ,$spamBlocker['akismet_key']);
	if (!$postData['body'] || (int)$postData['approved'] == 1 || !$akismet->isKeyValid())
		return false;
	
	$url = $scripturl . '/topic,'.$postData['id_topic'].'.msg'.$postData['id_msg'].'.html#msg'.$postData['id_msg'];
	$akismet->setCommentAuthor($postData['poster_name']);
	$akismet->setCommentAuthorEmail($postData['poster_email']);
	// $akismet->setCommentAuthorURL($url);
	$akismet->setCommentContent($postData['body']);
	$akismet->setPermalink($url);
	
	if($akismet->isCommentSpam() === 'connection_error')
	{
		if ((!empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1) == 1)
			log_error(sprintf($txt['spamBlocker_akismetError'], $name, $scripturl . '?action=register'));
			
		return false;
	}
	
	/* Are we reporting this as ham?? (mistaken as spam) */
	if ((int)$postData['spamblocker'] == 1)
	{
		$akismet->submitHam();
		$report = 3;
	}
	/* ... then we must be reporting this as spam since Akismet did not */
	else
	{
		$report = 2;
		$akismet->submitSpam();
	}
	
	/* Send data to SFS database if enabled and it is flagged as spam */
	if ((int)$postData['spamblocker'] != 1 && $spamBlocker['SFS_PostEnable'] == 1 && $spamBlocker['SFS_key'])
		spamBlockerReportSFS('username='.iconv($context['character_set'], 'UTF-8', $postData['poster_name']).'&ip_addr='.urlencode($postData['poster_ip']).'&email='.urlencode($postData['poster_email']).'&api_key='.$spamBlocker['SFS_key'].'&evidence='.iconv($context['character_set'], 'UTF-8', $postData['body']));		
	
	/* Set the spamblocker column to show this post has already been reported */
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}messages
		SET spamblocker = {int:spam}				
		WHERE id_msg = {int:message} AND id_topic = {int:topic}',
		array('message' => $reportedPost['id_msg'], 'topic' => $reportedPost['id_topic'], 'spam' => $report)
	);	
	
	/* Enter the data into the log if that option is enabled */
	if ($spamBlocker['reporting_errors'] == 1)
		log_error(sprintf($txt['spamBlockerReport'] . ' - <span class="remove">' . $postData['poster_ip'] . '</span>', $txt['spamBlockerReportMsg']));
	
	return true;
}

function spamBlockerReportSFS($data)
{	
   $fp = fsockopen("www.stopforumspam.com",80);
   fputs($fp, "POST /add.php HTTP/1.1\n" );
   fputs($fp, "Host: www.stopforumspam.com\n" );
   fputs($fp, "Content-type: application/x-www-form-urlencoded\n" );
   fputs($fp, "Content-length: ".strlen($data)."\n" );
   fputs($fp, "Connection: close\n\n" );
   fputs($fp, $data);
   fclose($fp);
   
   return true;
}

function spamBlockerReportPost()
{
	global $scripturl, $modSettings;	
	
	/* Extra varification aside from checking the session as an extra precaution */
	if ((!empty($_SESSION['spamBlocker_PostCheck']) ? $_SESSION['spamBlocker_PostCheck'] : false) != 'spamBlockerPostCheck')
		redirectexit($scripturl . '?action=forum');
		
	checkSession('request');
	$spamPost['id_msg'] = !empty($_REQUEST['report_spam']) ? (int)$_REQUEST['report_spam'] : 0; 
	$spamPost['id_topic'] = !empty($_REQUEST['topic']) ? (int)$_REQUEST['topic'] : 0;
		
	if (isset($_REQUEST['report_spam']) && allowedTo('spamblocker_settings') && (!empty($modSettings['spamBlocker_PostDisplay']) ? (int)$modSettings['spamBlocker_PostDisplay'] : 2) == 1)
	{					
		if ($spamPost['id_msg'] > 0 && $spamPost['id_topic'] > 0)	
			spamBlockerReport($spamPost);			
	}
	
	redirectexit($scripturl . '?topic='. $spamPost['id_topic']. '.msg'. $spamPost['id_msg']. '#msg'. $spamPost['id_msg']);
}

function spamBlockerPostFilter($text)
{
	global $txt, $modSettings, $user_info, $smcFunc;
	$defaults = array(array(), array(), array(), array(), 0, 0, 0, 0, false, false, false);
	list($links, $images, $plainText, $count, $chars, $low_chars, $strLength, $counter, $linkz, $imagez, $lastText) = $defaults;	
	$enableFilter = !empty($modSettings['spamBlocker_PostFilter']) ? (int)$modSettings['spamBlocker_PostFilter'] : 2;
	$maxPostcount = !empty($modSettings['spamBlocker_postCount']) ? $modSettings['spamBlocker_postCount'] : -1;
	$filteringText = !empty($modSettings['spamBlocker_filteredText']) ? $smcFunc['strtolower']($modSettings['spamBlocker_filteredText'], "UTF-8") : $smcFunc['strtolower']($txt['spamBlocker_textFilter'], "UTF-8");

	if ($enableFilter != 1 || AllowedTo('spamBlocker_settings'))
		return array('words' => 0, 'links' => 0, 'images' => 0, 'chars' => 0, 'low_chars' => 0);	
	elseif ($user_info['posts'] > $maxPostcount || $maxPostcount == -1)
		return array('words' => 0, 'links' => 0, 'images' => 0, 'chars' => 0, 'low_chars' => 0);
	
	$dom = new DOMDocument();
	libxml_use_internal_errors(true);
	$dom->loadHTML($text);
	libxml_use_internal_errors(false);	
		
	$plainText = str_word_count(trim($smcFunc['strtolower']($dom->textContent), 'UTF-8'), 1);
	$strLength = strlen($smcFunc['strtolower'](trim($dom->textContent), 'UTF-8'));	
	$textFilter = str_word_count(trim($filteringText), 1);
	$count = array_intersect($textFilter, $plainText);
	
	foreach ($plainText as $msgtext)
	{
		if ($counter > 0)
			$lastText = $plainText[$counter - 1];		
			
		if (strpos('http://', $msgtext) !== false && strpos('[img', $lastText) === false)
			$links[] = $msgtext;			
		elseif (strpos('http://', $msgtext) !== false && strpos('[img', $lastText) !== false)
			$images[] = $msgtext;		
			
		$counter++;	
	}				
	
	if (count($links) && count($links) > (!empty($modSettings['spamBlocker_linksCount']) ? (int)$modSettings['spamBlocker_linksCount'] : 0))
		$linkz = true;
		
	if (count($images) && count($images) > (!empty($modSettings['spamBlocker_imagesCount']) ? (int)$modSettings['spamBlocker_imagesCount'] : 0))
		$imagez = true;		
	
	if ($strLength >= (!empty($modSettings['spamBlocker_charsCount']) ? (int)$modSettings['spamBlocker_charsCount'] : 300))
		$chars = true;
	
	if ($strLength <= (!empty($modSettings['spamBlocker_charsLowCount']) ? (int)$modSettings['spamBlocker_charsLowCount'] : 1))
		$low_chars = true;
		
	return array('words' => count($count), 'links' => $linkz, 'images' => $imagez, 'chars' => $chars, 'low_chars' => $low_chars);
}
?>
