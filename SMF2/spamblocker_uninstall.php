<?php
/* 
    <id>underdog:spamblocker</id>
	<name>Spam Blocker</name>
	<version>1.0</version>
	<type>modification</type>
*/	

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://askusaquestion.net	
 * Copyright 2013 Underdog@askusaquestion.net
 * This software package is distributed under the terms of its Freeware License
 * http://askusaquestion.net/index.php/page=spamblocker_license
*/

/*  This file is for removing integration hooks, ban data and/or Spam Blocker tables and columns from the database */

if (!defined('SMF'))
	require '../SSI.php';

// remove_spamblockerDbData();	
	
/* Remove integration hooks */
remove_integration_function('integrate_pre_include', '$sourcedir/SpamBlockerHooks.php');
remove_integration_function('integrate_pre_load', 'spamBlocker_allExpired');
remove_integration_function('integrate_actions', 'spamBlocker_actions');
remove_integration_function('integrate_load_permissions', 'spamBlocker_load_permissions');
remove_integration_function('integrate_admin_areas', 'spamBlocker_admin_areas');
remove_integration_function('integrate_validate_login', 'spamBlocker_login');
remove_integration_function('integrate_create_topic', 'spamBlocker_akismet_topic');
remove_integration_function('integrate_validate_reply', 'spamBlocker_akismet_reply');

/* Remove Spam Blocker tables, settings columns and ban data (string-keys settings columns will remain) */
function remove_spamblockerDbData()
{
    /* This function will not auto initiate until/unless my xml edits are implemented by the SMF development team :) */
    /* If you want to use it at this time, manually call this function prior to the integration removal */
    global $smcFunc, $sourcedir;

    $datum = array();
    $member_id = array();
    $ban_group = array();
    $columns = array('id_ban_group', 'expire_time', 'reason', 'notes', 'id_member');
    
    $request = $smcFunc['db_query']('', "SELECT gp.id_ban_group, gp.expire_time, gp.reason, gp.notes, item.id_member
					FROM {db_prefix}ban_groups AS gp
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
	
	spamBlockerUninstallMemberDelete($member_id);	

    }

    foreach ($ban_group as $group)
    {
	$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
	$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
        $request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
    }

    $tables = array('spamblocker_settings','spamblocker_whitelist','spamblocker_blacklist');
    $settings_columns = array('spamBlocker_enable','spamBlocker_smfError','spamBlocker_hideMembers','spamBlocker_akismetPost','spamBlocker_conn_errs', 'spamBlocker_deleteMembers', 'spamBlocker_postCount', 'spamBlocker_optimizeInt', 'spamBlocker_honeypotType', 'spamBlocker_honeypotThreat');
        
    foreach ($tables as $table)
    {
	if (check_table_existsSB($table))
	    $smcFunc['db_query']('', "DROP TABLE {db_prefix}{$table}");    
    }
   
    foreach ($settings_columns as $column)
	$smcFunc['db_query']('', "DELETE FROM `{db_prefix}settings` WHERE `{db_prefix}settings`.`variable` LIKE '$column'");
        
    $optimize = $smcFunc['db_query']('', "OPTIMIZE TABLE {db_prefix}settings");
    $smcFunc['db_free_result']($optimize);    
}

/*  Check if table exists  */
function check_table_existsSB($table)
{
	global $db_prefix, $smcFunc;
	$check = false;
	$checkval = false;
	$check = $smcFunc['db_query']('', "SHOW TABLES LIKE '{$db_prefix}$table'");
	$checkval = $smcFunc['db_num_rows']($check);
	$smcFunc['db_free_result']($check);
	if ($checkval >0)
		return true;
		
	return false;
}

/* Delete members and their data */
function spamBlockerUninstallMemberDelete($member_id)
{
	global $smcFunc, $sourcedir;
	$users = array((int)$member_id);
	require_once($sourcedir . '/PersonalMessage.php');
	require_once($sourcedir . '/ManageAttachments.php');
	require_once($sourcedir . '/Subs.php');	
	
	/* Protect admins from deletion (just to be safe) */
	$request = $smcFunc['db_query']('', '
		SELECT id_member, member_name, CASE WHEN id_group = {int:admin_group} OR FIND_IN_SET({int:admin_group}, additional_groups) != 0 THEN 1 ELSE 0 END AS is_admin
		FROM {db_prefix}members
		WHERE id_member IN ({array_int:user_list})
		LIMIT ' . count($users),
		array(
			'user_list' => $users,
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
	
	if (in_array($member_id, $admins) || (int)$member_id == 0)
		return false;

	// Make these peoples' posts guest posts.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}messages
		SET id_member = {int:guest_id}, poster_email = {string:blank_email}
		WHERE id_member IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'blank_email' => '',
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}polls
		SET id_member = {int:guest_id}
		WHERE id_member IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);

	// Make these peoples' posts guest first posts and last posts.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}topics
		SET id_member_started = {int:guest_id}
		WHERE id_member_started IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}topics
		SET id_member_updated = {int:guest_id}
		WHERE id_member_updated IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_actions
		SET id_member = {int:guest_id}
		WHERE id_member IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_banned
		SET id_member = {int:guest_id}
		WHERE id_member IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_errors
		SET id_member = {int:guest_id}
		WHERE id_member IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);

	// Delete the member.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}members
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);

	// Delete the logs...
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_actions
		WHERE id_log = {int:log_type}
			AND id_member IN ({array_int:users})',
		array(
			'log_type' => 2,
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_boards
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_comments
		WHERE id_recipient IN ({array_int:users})
			AND comment_type = {string:warntpl}',
		array(
			'users' => $users,
			'warntpl' => 'warntpl',
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_group_requests
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_karma
		WHERE id_target IN ({array_int:users})
			OR id_executor IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_mark_read
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_notify
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_online
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_subscribed
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}log_topics
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}collapsed_categories
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);

	// Make their votes appear as guest votes - at least it keeps the totals right.
	//!!! Consider adding back in cookie protection.
	$smcFunc['db_query']('', '
		UPDATE {db_prefix}log_polls
		SET id_member = {int:guest_id}
		WHERE id_member IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);

	// Delete personal messages.	
	deleteMessages(null, null, $users);

	$smcFunc['db_query']('', '
		UPDATE {db_prefix}personal_messages
		SET id_member_from = {int:guest_id}
		WHERE id_member_from IN ({array_int:users})',
		array(
			'guest_id' => 0,
			'users' => $users,
		)
	);

	// They no longer exist, so we don't know who it was sent to.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}pm_recipients
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);

	// Delete avatar.	
	removeAttachments(array('id_member' => $users));

	// It's over, no more moderation for you.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}moderators
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}group_moderators
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);

	// If you don't exist we can't ban you.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}spamblocker_blacklist
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);
			
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}ban_items
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);

	// Remove individual theme settings.
	$smcFunc['db_query']('', '
		DELETE FROM {db_prefix}themes
		WHERE id_member IN ({array_int:users})',
		array(
			'users' => $users,
		)
	);

	// These users are nobody's buddy nomore.
	$request = $smcFunc['db_query']('', '
		SELECT id_member, pm_ignore_list, buddy_list
		FROM {db_prefix}members
		WHERE FIND_IN_SET({raw:pm_ignore_list}, pm_ignore_list) != 0 OR FIND_IN_SET({raw:buddy_list}, buddy_list) != 0',
		array(
			'pm_ignore_list' => implode(', pm_ignore_list) != 0 OR FIND_IN_SET(', $users),
			'buddy_list' => implode(', buddy_list) != 0 OR FIND_IN_SET(', $users),
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
				'pm_ignore_list' => implode(',', array_diff(explode(',', $row['pm_ignore_list']), $users)),
				'buddy_list' => implode(',', array_diff(explode(',', $row['buddy_list']), $users)),
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
?>