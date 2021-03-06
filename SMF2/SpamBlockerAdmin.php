<?php
// Version: 1.0; Spam Blocker  

/*
 *	Main admin settings file for the Spam Blocker Mod	
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
	
/*	This file handles all of Spam Blocker's admin settings.

	void function spamBlocker()
		- Loads correct file based on subaction
		- Fills $context with needed data from form(s)

	void function SettingsSpamBlocker()
		- Admin menu sub action
		- Logic for configuration admin menu
		- Loads the Spam Blocker admin template

	void function WhitelistSpamBlocker()
		- Admin menu sub action
		- Logic for IP Whitelist admin menu
		- Loads the Spam Blocker Whitelist admin template

	void function BlacklistSpamBlocker()
		- Admin menu sub action
		- Logic for IP Blacklist admin menu
		- Loads the Spam Blocker Blacklist admin template

	void function LookupSpamBlocker()
		- Admin menu sub action
		- Logic for IP/Email Lookup
		- Manually checks anti-spam source databases for form input IP/Email
		
	void function LicenseSpamBlocker()
		- Admin menu sub action
		- Redirects to a pdf file that displays the Spam Blocker license

	void function GuideSpamBlocker()
		- Admin menu sub action
		- Redirects to a pdf file that displays the Spam Blocker guide
	
*/	

function spamBlocker()
{
	global $scripturl, $txt, $context, $sourcedir, $settings, $modSettings;
		
	$context['robot_no_index'] = true;	
	require_once($sourcedir . '/ManageServer.php');
	require_once($sourcedir . '/Subs-SpamBlockerAdmin.php');
	$context[$context['admin_menu_name']]['tab_data']['title'] = $txt['spamBlocker_tabtitle'];	
	$_SESSION['spamBlockerIP_Error'] = !empty($_SESSION['spamBlockerIP_Error']) ? $_SESSION['spamBlockerIP_Error'] : false;
	$_SESSION['spamBlockerIP_Msg'] = !empty($_SESSION['spamBlockerIP_Msg']) ? $_SESSION['spamBlockerIP_Msg'] : false;
	$_SESSION['spamBlockerConfig_Msg'] = !empty($_SESSION['spamBlockerConfig_Msg']) ? $_SESSION['spamBlockerConfig_Msg'] : false;	
	$_SESSION['spamBlockerConfigSFS_Key'] = !empty($_SESSION['spamBlockerConfigSFS_Key']) ? $_SESSION['spamBlockerConfigSFS_Key'] : false;	
		
	/*  Fill $context with save data if necessary  */	
	$spamblocker_settings = array('user_message', 'error_message', 'enable_errorlog', 'enable_mod', 'enable_sfs', 'enable_akismet', 'enable_honeypot', 'enable_spamhaus', 'enable_email', 'enable_pass', 'enable_expired', 'enable_reset', 'enable_emend', 'ban_option', 'smf_error', 'ban_full', 'ban_post', 'ban_register', 'ban_login', 'expiration', 'expire_time', 'hide_members', 'delete_members', 'akismet_key', 'honeypot_key', 'enable_post', 'akismet_count', 'links_count', 'enable_errs', 'honeypot_threat', 'honeypot_type', 'enable_delBlacklist', 'enable_postSFS', 'sfs_key', 'enable_postDisplay', 'enable_default', 'enable_postFilter', 'report_errs', 'redirect_path', 'enable_redirect', 'text_filter', 'chars_count', 'chars_low_count', 'word_count', 'images_count');	
	$whitelist = array('user_ip', 'delete', 'id_member', 'delete_member');
	$lookups = array('input_email', 'input_ip');
	
	foreach ($spamblocker_settings as $setting_type)
	{			
		if (empty($_REQUEST[$setting_type][0]))
			$_REQUEST[$setting_type][0] = false;
		
		$context['spamblocker'][$setting_type] = $_REQUEST[$setting_type][0];			
	}
	
	foreach ($lookups as $lookup)
	{			
		if (empty($_REQUEST[$lookup][0]))
			$_REQUEST[$lookup][0] = false;
		
		$context['spamblocker'][$lookup] = $_REQUEST[$lookup][0];			
	}
	
	/* Akismet Post Filtering can only be used if Post Moderation is enabled. If it is not, automatically disable Akismet Post Filtering. */ 
	if ((!empty($modSettings['postmod_active']) ? (int)$modSettings['postmod_active'] : 0) == 0)
		$context['spamblocker']['enable_post'] = 1;
	
	/* If permanently deleting members is enabled, these must be disabled */ 
	if ((!empty($modSettings['spamBlocker_deleteMembers']) ? (int)$modSettings['spamBlocker_deleteMembers'] : 1) == 1)
	{
		$context['spamblocker']['ban_post'] = 0;
		$context['spamblocker']['ban_login'] = 0;
	}	
	
	foreach ($whitelist as $user_ip)
	{			
		if (empty($_REQUEST[$user_ip][0]))
			$_REQUEST[$user_ip][0] = false;	
						
		$context['spamblocker'][$user_ip] = $_REQUEST[$user_ip][0];	
		
		if (($user_ip == 'delete' || $user_ip == 'id_member') && !empty($_REQUEST[$user_ip]))
			$context['spamblocker'][$user_ip] = $_REQUEST[$user_ip];		
	}	

	/* Keep these values if they have already been defined else use the defaults */
	if (empty($context['SB_blacklist_sort']))
		$context['SB_blacklist_sort'] = 'date';
	if (empty($context['SB_blacklist_page']))
		$context['SB_blacklist_page'] = 1;
	if (empty($context['SB_increment']))
		$context['SB_increment'] = 5;
		
	/* Images and settings for pagination */
	$prevX = $settings['default_theme_url'] .'/images/admin/spamblocker_back_enabled_hover.png';
	$prevY = $settings['default_theme_url'] .'/images/admin/spamblocker_back_disabled.png';
	$nextX = $settings['default_theme_url'] .'/images/admin/spamblocker_forward_enabled_hover.png';
	$nextY = $settings['default_theme_url'] .'/images/admin/spamblocker_forward_disabled.png';
	$context['vertical_bar_x'] = '<img src="'.$settings['default_theme_url'] .'/images/admin/spamblocker_vertical_bar-x.gif" alt="" />';
	$context['vertical_bar_y'] = '<img src="'.$settings['default_theme_url'] .'/images/admin/spamblocker_vertical_bar-y.gif" alt="" />';
	$context['spamblocker_prev'] = '<a onMouseOver="document.spPrev.src=\''.$prevX. '\'" onMouseOut="document.spPrev.src=\''.$prevY. '\'"><img style="vertical-align:top;" name="spPrev" src="'.$settings['default_theme_url'] .'/images/admin/spamblocker_back_disabled.png" alt="" title="" /></a>';
	$context['spamblocker_next'] = '<a onMouseOver="document.spNext.src=\''.$nextX. '\'" onMouseOut="document.spNext.src=\''.$nextY. '\'"><img style="vertical-align:top;" name="spNext" src="'.$settings['default_theme_url'] .'/images/admin/spamblocker_forward_disabled.png" data-hover="'.$settings['default_theme_url'] .'/images/admin/forward_disabled.png" alt="" title="" /></a>';		
	$context['spamblocker_prev_plus'] = '<a onMouseOver="document.spPrevPlus.src=\''.$prevX. '\'" onMouseOut="document.spPrevPlus.src=\''.$prevY. '\'"><img style="vertical-align:top;" name="spPrevPlus" src="'.$settings['default_theme_url'] .'/images/admin/spamblocker_back_disabled.png" alt="" title="" /></a>';
	$context['spamblocker_next_plus'] = '<a onMouseOver="document.spNextPlus.src=\''.$nextX. '\'" onMouseOut="document.spNextPlus.src=\''.$nextY. '\'"><img style="vertical-align:top;" name="spNextPlus" src="'.$settings['default_theme_url'] .'/images/admin/spamblocker_forward_disabled.png" data-hover="'.$settings['default_theme_url'] .'/images/admin/forward_disabled.png" alt="" title="" /></a>';	
	$context['SB_increment'] = !empty($_REQUEST['spamBlockerIncrement']) ? (int)$_REQUEST['spamBlockerIncrement'] : $context['SB_increment'];
	$context['SB_blacklist_sort'] = !empty($_REQUEST['SB_blacklist_sort']) ? $_REQUEST['SB_blacklist_sort'] : $context['SB_blacklist_sort'];
	$context['SB_blacklist_page'] = (!empty($_REQUEST['blacklist_page']) ? (int)$_REQUEST['blacklist_page'] : $context['SB_blacklist_page']) -1;
	$context['spamblocker_styles'] = spamBlockerStyles();
		
	/* Set the javascript variables for enable and disable from the language strings */
	$context['spamblocker_enable_disable'] = '
	<script type="text/javascript"><!-- // --><![CDATA[
		var spamblocker_enabled = '. json_encode(iconv($context['character_set'], 'UTF-8', $txt['spamBlocker_enabled'])).'
		var spamblocker_disabled = '. json_encode(iconv($context['character_set'], 'UTF-8', $txt['spamBlocker_disabled'])).'
	// ]]></script>';	
						
	$subActions = array(
					'spamBlockerSettings' => array('SettingsSpamBlocker'),	
					'spamBlockerWhitelist' => array('WhitelistSpamBlocker'),
					'spamBlockerBlacklist' => array('BlacklistSpamBlocker'),
					'spamBlockerLookup' => array('LookupSpamBlocker'),
					'spamBlockerLicense' => array('LicenseSpamBlocker'),
					'spamBlockerGuide' => array('GuideSpamBlocker'), 										
				);
		
	$_REQUEST['sa'] = isset($_REQUEST['sa']) && isset($subActions[$_REQUEST['sa']]) ? $_REQUEST['sa'] : 'spamBlockerSettings'; 

	if (isset($subActions[$_REQUEST['sa']][1]))
		isAllowedTo('spamBlocker_settings');

	$subActions[$_REQUEST['sa']][0]();
}

function SettingsSpamBlocker()
{
	global $txt, $scripturl, $context, $smcFunc, $sourcedir, $modSettings, $settings, $boardurl;
	loadLanguage('SpamBlocker');	
	
	if (!AllowedTo('spamBlocker_settings'))
		fatal_lang_error('spamBlocker_ErrorMessage',false);	
	
	require_once($sourcedir . '/SpamBlockerAkismet.php');
	require_once($sourcedir . '/ManageServer.php');	
	$context['robot_no_index'] = true;	
	$context[$context['admin_menu_name']]['tab_data']['description'] = $txt['spamBlocker_general'];
	$tableName = 'spamblocker_settings';
	$_SESSION['spamBlockerConfig_Msg'] = !empty($_SESSION['spamBlockerConfig_Msg']) ? $_SESSION['spamBlockerConfig_Msg'] : '&nbsp;';	
	$_SESSION['spamBlockerConfigSFS_Key'] = !empty($_SESSION['spamBlockerConfigSFS_Key']) ? $_SESSION['spamBlockerConfigSFS_Key'] : '&nbsp;';	
	$toggles = array(
		'sb_mod' => 'enable_mod',
		'sb_sfs' => 'enable_sfs',
		'sb_akismet' => 'enable_akismet',
		'sb_honeypot' => 'enable_honeypot',
		'sb_spamhaus' => 'enable_spamhaus',
		'sb_email' => 'enable_email',
		'sb_pass' => 'enable_pass',
		'sb_err' => 'enable_errorlog',
		'sb_ban' => 'ban_option',
		'sb_reset' => 'enable_reset',
		'sb_smf_err' => 'smf_error',
		'sb_hide_members' => 'hide_members',
		'sb_delete_members' => 'delete_members',
		'sb_akismet_post' => 'enable_post',
		'sb_connection_errs' => 'enable_errs',
		'sb_postSFS' => 'enable_postSFS',
		'sb_postDisplay' => 'enable_postDisplay',
		'sb_postFilter' => 'enable_postFilter',
		'sb_reporting_errs' => 'report_errs',
		'sb_enableRedirect' => 'enable_redirect',
		);	
	
	$setting_types = array(
		'user_message' => array('string', true, 'user_message'),
		'error_message' => array('string', true, 'error_message'),
		'enable_errorlog' => array('enable_int', true, 'enable_errorlog'),
		'enable_sfs' => array('enable_int', true, 'enable_sfs'),
		'enable_akismet' => array('enable_int', true, 'enable_akismet'),
		'enable_honeypot' => array('enable_int', true, 'enable_honeypot'),
		'enable_spamhaus' => array('enable_int', true, 'enable_spamhaus'),
		'enable_email' => array('enable_int', true, 'enable_email'),
		'enable_pass' => array('enable_int', true, 'enable_pass'),
		'enable_reset' => array('enable_int', true, 'enable_reset'),
		'ban_option' => array('enable_int', true, 'ban_option'),
		'enable_mod' => array('general', true, 'spamBlocker_enable'),
		'smf_error' => array('general', true, 'spamBlocker_smfError'),
		'hide_members' => array('general', true, 'spamBlocker_hideMembers'),
		'delete_members' => array('general', true, 'spamBlocker_deleteMembers'),
		'enable_post' => array('general', true, 'spamBlocker_akismetPost'),
		'enable_errs' => array('general', true, 'spamBlocker_conn_errs'),
		'enable_postSFS' => array('general', true, 'spamBlocker_PostSFS'),
		'enable_postFilter' => array('general', true, 'spamBlocker_PostFilter'),
		'report_errs' => array('general', true, 'spamBlocker_report_errs'),
		'enable_redirect' => array('general', true, 'spamBlocker_enableRedirect'),
		'enable_postDisplay' => array('general', true, 'spamBlocker_PostDisplay'),
		'akismet_count' => array('abs', false, 'spamBlocker_postCount'),
		'links_count' => array('abs', false, 'spamBlocker_linksCount'),		
		'honeypot_threat' => array('abs', false, 'spamBlocker_honeypotThreat'),
		'chars_count' => array('abs', false, 'spamBlocker_charsCount'),
		'chars_low_count' => array('abs', false, 'spamBlocker_charsLowCount'),
		'word_count' => array('abs', true, 'spamBlocker_wordCount'),
		'images_count' => array('abs', false, 'spamBlocker_imagesCount'),		
		'ban_post' => array('banopt', false, 'ban_post'),
		'ban_register' => array('banopt', false, 'ban_register'),
		'ban_login' => array('banopt', false, 'ban_login'),
		'akismet_key' => array('key', true, 'spamBlocker_akismetKey'),
		'honeypot_key' => array('key', true, 'spamBlocker_honeypotKey'),
		'sfs_key' => array('key', true, 'spamBlocker_sfsKey'),
		'enable_expired' => array('expired', true, 'enable_expired'),		
		'enable_emend' => array('emend', true, 'enable_emend'),			
		'ban_full' => array('full_ban', false, 'ban_full'),
		'expiration' => array('zero', false, 'expiration'),
		'expire_time' => array('exp_time', false, 'expire_time'),		
		'honeypot_type' => array('type', false, 'honeypot_type'),
		'enable_delBlacklist' => array('del', true, 'enable_delBlacklist'),				
		'enable_default' => array('default', true, 'enable_default'),		
		'redirect_path' => array('path', true, 'redirect_path'),
		'text_filter' => array('text', true, 'text_filter'),		
		);
	
	if ((int)$context['spamblocker']['enable_expired'] != 2 && (int)$context['spamblocker']['enable_emend'] != 2)
		$_SESSION['spamBlockerConfig_Msg'] = '&nbsp;';
		
	/*  Check for new settings values and save to database if necessary */
	if (isset($_GET['save']))
		checkSession('request');
	
	if ((int)$context['spamblocker']['enable_sfs'] == 1 && (int)$context['spamblocker']['enable_spamhaus'] == 1 && (int)$context['spamblocker']['enable_akismet'] == 1 && (int)$context['spamblocker']['enable_honeypot'] == 1 && $context['spamblocker']['enable_email'] == 1)
	{
		$context['spamblocker']['enable_email'] = 2;
		$_SESSION['spamBlockerConfig_Msg'] = $txt['spamBlockerSource_NoneError'];		
	}
		
	foreach ($setting_types as $key => $data)
	{	
		if (!$data[1] && empty($context['spamblocker'][$key]) && isset($_REQUEST['save']))
			$context['spamblocker'][$key] = 0;				
		elseif (empty($context['spamblocker'][$key]))
		{
			$context['spamblocker'][$key] = false;
			continue;
		}	
		
		$value = $context['spamblocker'][$key];
		
		switch ($data[0])
		{
			case 'string':
				$val = trim($value);
				createSpamBlocker_setting($tableName, $key, $val);
				continue 2;			
			case 'banopt':
				$val = ((int)$value == 1) ? 1 : 0;
				$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET {$key} = {int:val}", array('val' => $val));
				$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET ban_full = 0");
				continue 2;
			case 'enable_int':
				$val = ($value == 2) ? 1 : 2;
				$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET {$key} = {int:val}", array('val' => $val));				
				continue 2;
			case 'general':
				$val = ($value == 2) ? 1 : 2;
				$setArray[$data[2]] = $val;
				updateSettings($setArray);			
				$modSettings[$data[2]] = $val;
				continue 2;
			case 'abs':
				if ($key === 'chars_low_count' && (int)$value < 1)
					$value = 1;
				if ($key === 'chars_low_count' && (int)$value >= (!empty($modSettings['spamBlocker_charsCount']) ? (int)$modSettings['spamBlocker_charsCount'] : 60))
					$value = (!empty($modSettings['spamBlocker_charsCount']) ? (int)$modSettings['spamBlocker_charsCount'] : 60) - 1;
				if ($key === 'chars_count' && (int)$value < 60)
					$value = 300;
				if ($key === 'honeypot_threat' && ((int)$value < 0 || (int)$value > 255))
					$value = 1;
				$val = ($value < 0) ? 0 : (int)$value;
				$setArray[$data[2]] = abs((int)$val);
				updateSettings($setArray);			
				$modSettings[$data[2]] = abs((int)$val);
				continue 2;
			case 'key':
				$val = preg_replace('/\s+/', '', $value);			
				$setArray[$data[2]] = cleanSpamBlockerQuery($val);
				updateSettings($setArray);			
				$modSettings[$data[2]] = cleanSpamBlockerQuery($val);	
				continue 2;
			case 'exp_time':
				if ((int)$value < 1)
				{
					$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET expire_time = 0");
					$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET expiration = 0");
					continue 2;
				}
				$days = (int)$value;
				$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET expire_time = '{$days}'");
				$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET expiration = 1");
				continue 2;			
			case 'del':
				if ((($value == 2) ? 2 : 1) == 2)
				{
					spamBlockerClearBlacklist();
					$_SESSION['spamBlockerConfig_Msg'] = $txt['spamBlockerClearBlacklistMsg'];			
				}
				continue 2;
			case 'default':
				if ((($value == 2) ? 2 : 1) == 2)
				$_SESSION['spamBlockerConfig_Msg'] = spamBlockerDefaultReset();					
				continue 2;
			case 'expired':
				if ((($value == 2) ? 2 : 1) == 2)
				{
					/* Delete expired bans */			
					$now = time() + 86400;
					$datum = array();
					$member_id = array();
					$ban_group = array();
					$columns = array('id_ban_group', 'expire_time', 'reason', 'notes', 'id_member');
					$request = $smcFunc['db_query']('', "
						SELECT gp.id_ban_group, gp.expire_time, gp.reason, gp.notes, item.id_member
						FROM {db_prefix}ban_groups AS gp
						LEFT JOIN {db_prefix}ban_items AS item ON (item.id_ban_group = gp.id_ban_group)
						WHERE {int:now} > gp.expire_time",array('now' => $now));
			
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
						if ((int)$id_member > 0 && (int)$context['spamblocker']['delete_member'] == 1)
							spamBlockerDeleteMember($member_id);
					}

					foreach ($ban_group as $group)
					{
						$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
						$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
						$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group = {int:ban}",array('ban' => (int)$group));
					}
										
					$_SESSION['spamBlockerConfig_Msg'] = $txt['spamBlockerIP_BlacklistExpired'];
				}
				continue 2;
			case 'emend':
				if ((($value == 2) ? 2 : 1) == 2)
				{
					/* Emend Blacklist */						
					$_SESSION['spamBlockerConfig_Msg'] = spamBlockerEmendBlacklist($txt['spamBlockerIP_BlacklistEmend']);
					
					/* Optimize all spamblocker and ban tables */
					$result1 = $smcFunc['db_query']('', "OPTIMIZE TABLE {db_prefix}spamblocker_blacklist");
					$smcFunc['db_free_result']($result1);
					$result2 = $smcFunc['db_query']('', "OPTIMIZE TABLE {db_prefix}spamblocker_whitelist");
					$smcFunc['db_free_result']($result2);
					$result3 = $smcFunc['db_query']('', "OPTIMIZE TABLE {db_prefix}spamblocker_settings");
					$smcFunc['db_free_result']($result3);
					$result4 = $smcFunc['db_query']('', "OPTIMIZE TABLE {db_prefix}ban_groups");
					$smcFunc['db_free_result']($result4);
					$result5 = $smcFunc['db_query']('', "OPTIMIZE TABLE {db_prefix}ban_items");
					$smcFunc['db_free_result']($result5);				
				}
				continue 2;
			case 'full_ban':				
				$val = ((int)$value == 1) ? 1 : 0;
				if ($val == 1)
				{
					$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET {$key} = {int:val}", array('val' => $val));
					$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET ban_register = 0");
					$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET ban_login = 0");
					$request = $smcFunc['db_query']('', "UPDATE {db_prefix}$tableName SET ban_post = 0");					
				}	
				continue 2;
			case 'path':
				if (filter_var($context['spamblocker']['redirect_path'], FILTER_VALIDATE_URL) !== false)
					$val = cleanSpamBlockerRedirect($value);
				else
					$val = $boardurl;
				
				$setArray['spamBlocker_redirectPath'] = $val;
				updateSettings($setArray);			
				$modSettings['spamBlocker_redirectPath'] = $val;		
				continue 2;
			case 'type':
				$honeypotType = array_unique(explode(',', $value));
				sort($honeypotType);
				$types = '';
				foreach ($honeypotType as $honeypot)
				{
					if ((int)$honeypot < 0 || (int)$honeypot > 255)
						continue;
				
					$types .= (int)$honeypot . ', ';	
				}
				
				$types = rtrim($types, ', ');
				$setArray['spamBlocker_honeypotType'] = $types;			
				updateSettings($setArray);			
				$modSettings['spamBlocker_honeypotType'] = $types;
				continue 2;
			case 'text':
				$words = cleanSpamBlockerInput($value);				
				$setArray['spamBlocker_filteredText'] = $words;			
				updateSettings($setArray);			
				$modSettings['spamBlocker_filteredText'] = $words;
				continue 2;
		}			
	}	
				
	/* START - Read database entries for Spam Blocker settings */	
	if (!checkFieldSB($tableName,'reference')) 
		fatal_lang_error('SpamBlocker_ErrorMessageDB', false);	

	$result1 = $smcFunc['db_query']('', "SELECT user_message, error_message, enable_errorlog, enable_sfs, enable_akismet, enable_honeypot, enable_spamhaus, enable_email,
						enable_pass, enable_reset, ban_option, ban_full, ban_post, ban_register, ban_login, expiration, expire_time
						FROM {db_prefix}spamblocker_settings
						WHERE reference = 1");
	while ($val = $smcFunc['db_fetch_assoc']($result1))
	{
		foreach ($setting_types as $key => $setting_type)
		{
			if ($key === 'enable_expired' || $key === 'enable_emend')
			{
				$context['spamblocker'][$key] = 0;
				continue;
			}	
									
			if (empty($val[$key])) 
				$val[$key] = false;
				
			$context['spamblocker'][$key] = $val[$key];
		}			
					
	}
	$smcFunc['db_free_result']($result1);
	
	/*  Set the $context variables for the display template  */
	
	/* If Akismet is enabled, check that the current key is valid */
	$context['spamblocker']['akismet_key'] = !empty($modSettings['spamBlocker_akismetKey']) ? $modSettings['spamBlocker_akismetKey'] : false;
	if ((int)$context['spamblocker']['enable_akismet'] == 1 && $context['spamblocker']['akismet_key'])
	{		
		$akismet = new Akismet($boardurl ,$context['spamblocker']['akismet_key']);
		if ($akismet->isKeyValid())
			$_SESSION['spamBlockerConfig_Key'] = $txt['spamBlockerAkismetKeyCheckValid'];
		else
			$_SESSION['spamBlockerConfig_Key'] = $txt['spamBlockerAkismetKeyCheckInvalid'];
	}
	else
		$_SESSION['spamBlockerConfig_Key'] = '&nbsp;';
		
	/* If permanently deleting members is enabled, these must be disabled */ 
	if ((!empty($modSettings['spamBlocker_deleteMembers']) ? (int)$modSettings['spamBlocker_deleteMembers'] : 1) == 1)
	{
		$context['spamblocker']['ban_post'] = 0;
		$context['spamblocker']['ban_login'] = 0;
	}
	
	/* If all ban options are disabled then full ban must be true */
	if (((int)$context['spamblocker']['ban_post'] + (int)$context['spamblocker']['ban_login'] + (int)$context['spamblocker']['ban_register'] + (int)$context['spamblocker']['ban_full']) == 0)
		$context['spamblocker']['ban_full'] = 1;		
	
	/* Gather data from configurations stored in the settings table */
	$context['spamblocker']['text_filter'] = !empty($modSettings['spamBlocker_filteredText']) ? $modSettings['spamBlocker_filteredText'] : false;
	$context['spamblocker']['honeypot_key'] = !empty($modSettings['spamBlocker_honeypotKey']) ? $modSettings['spamBlocker_honeypotKey'] : false;
	$context['spamblocker']['redirect_path'] = urldecode(!empty($modSettings['spamBlocker_redirectPath']) ? $modSettings['spamBlocker_redirectPath'] : false);
	$context['spamblocker']['enable_redirect'] = !empty($modSettings['spamBlocker_enableRedirect']) ? (int)$modSettings['spamBlocker_enableRedirect'] : 2;
	$context['spamblocker']['enable_postFilter'] = !empty($modSettings['spamBlocker_PostFilter']) ? (int)$modSettings['spamBlocker_PostFilter'] : 2;
	$context['spamblocker']['sfs_key'] = !empty($modSettings['spamBlocker_sfsKey']) ? $modSettings['spamBlocker_sfsKey'] : false;
	$context['spamblocker']['honeypot_type'] = !empty($modSettings['spamBlocker_honeypotType']) ? $modSettings['spamBlocker_honeypotType'] : '0';
	$context['spamblocker']['honeypot_threat'] = !empty($modSettings['spamBlocker_honeypotThreat']) ? (int)$modSettings['spamBlocker_honeypotThreat'] : 0;
	$context['spamblocker']['enable_mod'] = !empty($modSettings['spamBlocker_enable']) ? (int)$modSettings['spamBlocker_enable'] : 1;
	$context['spamblocker']['smf_error'] = !empty($modSettings['spamBlocker_smfError']) ? (int)$modSettings['spamBlocker_smfError'] : 2;
	$context['spamblocker']['enable_errs'] = !empty($modSettings['spamBlocker_conn_errs']) ? (int)$modSettings['spamBlocker_conn_errs'] : 1;
	$context['spamblocker']['report_errs'] = !empty($modSettings['spamBlocker_report_errs']) ? (int)$modSettings['spamBlocker_report_errs'] : 1;
	$context['spamblocker']['enable_post'] = !empty($modSettings['spamBlocker_akismetPost']) ? (int)$modSettings['spamBlocker_akismetPost'] : 2;
	$context['spamblocker']['enable_postSFS'] = !empty($modSettings['spamBlocker_PostSFS']) ? (int)$modSettings['spamBlocker_PostSFS'] : 2;
	$context['spamblocker']['enable_postDisplay'] = !empty($modSettings['spamBlocker_PostDisplay']) ? (int)$modSettings['spamBlocker_PostDisplay'] : 2;	
	$context['spamblocker']['akismet_count'] = !empty($modSettings['spamBlocker_postCount']) ? (int)$modSettings['spamBlocker_postCount'] : 0;
	$context['spamblocker']['links_count'] = !empty($modSettings['spamBlocker_linksCount']) ? (int)$modSettings['spamBlocker_linksCount'] : 0;
	$context['spamblocker']['images_count'] = !empty($modSettings['spamBlocker_imagesCount']) ? (int)$modSettings['spamBlocker_imagesCount'] : 0;
	$context['spamblocker']['chars_count'] = !empty($modSettings['spamBlocker_charsCount']) ? (int)$modSettings['spamBlocker_charsCount'] : 300;
	$context['spamblocker']['word_count'] = !empty($modSettings['spamBlocker_wordCount']) ? (int)$modSettings['spamBlocker_wordCount'] : 1;
	$context['spamblocker']['chars_low_count'] = !empty($modSettings['spamBlocker_charsLowCount']) ? (int)$modSettings['spamBlocker_charsLowCount'] : 1;
	$context['spamblocker']['delete_members'] = !empty($modSettings['spamBlocker_deleteMembers']) ? (int)$modSettings['spamBlocker_deleteMembers'] : 1;
	$context['spamblocker']['hide_members'] = !empty($modSettings['spamBlocker_hideMembers']) ? (int)$modSettings['spamBlocker_hideMembers'] : 2;	
	$context['settings_title'] = $txt['spamBlockerSettings'];
	$context['post_url'] = $scripturl . '?action=admin;area=spamBlocker;sa=spamBlockerSettings;' . $context['session_var'] . '=' . $context['session_id'] . ';save';	
	$context['sub_template'] = 'spamBlocker_settings_page';	
	$_SESSION['spamBlockerConfigSFS_Key'] = checkSFS_APIkey($context['spamblocker']['sfs_key']);
	
	if (!(!empty($modSettings['spamBlocker_filteredText']) ? $modSettings['spamBlocker_filteredText'] : false))
	{
		$words = cleanSpamBlockerInput($txt['spamBlocker_textFilter']);		
		$setArray['spamBlocker_filteredText'] = $words;			
		updateSettings($setArray);			
		$modSettings['spamBlocker_filteredText'] = $words;
		$context['spamblocker']['text_filter'] = $words;
	}
	
	foreach ($toggles as $key => $display)
	{	
		$context['spamblocker'][$key] = $txt['spamBlocker_disabled'];
		if ($context['spamblocker'][$display] == 1)
			$context['spamblocker'][$key] = $txt['spamBlocker_enabled'];			
		
	}

	$context['spamblocker']['status'] = 'never';
	
	if ($context['spamblocker']['expiration'] == 1)
		$context['spamblocker']['status'] = 'active';	
	
	/* Javascript functions for all the enable/disable toggles */
	$context['toggle'] = '
	<script type="text/javascript"><!-- // --><![CDATA[	
		function changeModA(){
			document.getElementById("updateMod").innerHTML = spamblocker_enabled;
		}
		function changeModB(){
			document.getElementById("updateMod").innerHTML = spamblocker_disabled;
		}
		function changeSfsA(){
			document.getElementById("updateSfs").innerHTML = spamblocker_enabled;
		}
		function changeSfsB(){
			document.getElementById("updateSfs").innerHTML = spamblocker_disabled;
		}
		function changeAkismetA(){
			document.getElementById("updateAkismet").innerHTML = spamblocker_enabled;
		}
		function changeAkismetB(){
			document.getElementById("updateAkismet").innerHTML = spamblocker_disabled;
		}
		function changeHoneypotA(){
			document.getElementById("updateHoneypot").innerHTML = spamblocker_enabled;
		}
		function changeHoneypotB(){
			document.getElementById("updateHoneypot").innerHTML = spamblocker_disabled;
		}
		function changeSpamhausA(){
			document.getElementById("updateSpamhaus").innerHTML = spamblocker_enabled;
		}
		function changeSpamhausB(){
			document.getElementById("updateSpamhaus").innerHTML = spamblocker_disabled;
		}		
		function changeEmailA(){
			document.getElementById("updateEmail").innerHTML = spamblocker_enabled;
		}
		function changeEmailB(){
			document.getElementById("updateEmail").innerHTML = spamblocker_disabled;
		}	
		function changePassA(){
			document.getElementById("updatePass").innerHTML = spamblocker_enabled;
		}
		function changePassB(){
			document.getElementById("updatePass").innerHTML = spamblocker_disabled;
		}
		function changeResetA(){
			document.getElementById("updateReset").innerHTML = spamblocker_enabled;
		}
		function changeResetB(){
			document.getElementById("updateReset").innerHTML = spamblocker_disabled;
		}
		function changeBanA(){
			document.getElementById("updateBan").innerHTML = spamblocker_enabled;
		}
		function changeBanB(){
			document.getElementById("updateBan").innerHTML = spamblocker_disabled;
		}	
		function changeErrA(){
			document.getElementById("updateErr").innerHTML = spamblocker_enabled;
		}
		function changeErrB(){
			document.getElementById("updateErr").innerHTML = spamblocker_disabled;
		}
		function changeReportErrsA(){
			document.getElementById("updateReportErrs").innerHTML = spamblocker_enabled;
		}
		function changeReportErrsB(){
			document.getElementById("updateReportErrs").innerHTML = spamblocker_disabled;
		}
		function changeSmfErrA(){
			document.getElementById("updateSmfErr").innerHTML = spamblocker_enabled;
		}
		function changeSmfErrB(){
			document.getElementById("updateSmfErr").innerHTML = spamblocker_disabled;
		}
		function changeHideA(){
			document.getElementById("updateHide").innerHTML = spamblocker_enabled;
		}
		function changeHideB(){
			document.getElementById("updateHide").innerHTML = spamblocker_disabled;
		}
		function changeErrsA(){
			document.getElementById("updateErrs").innerHTML = spamblocker_enabled;
		}
		function changeErrsB(){
			document.getElementById("updateErrs").innerHTML = spamblocker_disabled;
		}
		function changePostA(){
			document.getElementById("updatePost").innerHTML = spamblocker_enabled;
		}
		function changePostB(){
			document.getElementById("updatePost").innerHTML = spamblocker_disabled;
		}
		function changePostSFSA(){
			document.getElementById("updatePostSFS").innerHTML = spamblocker_enabled;
		}
		function changePostSFSB(){
			document.getElementById("updatePostSFS").innerHTML = spamblocker_disabled;
		}
		function changePostDisplayA(){
			document.getElementById("updatePostDisplay").innerHTML = spamblocker_enabled;
		}
		function changePostDisplayB(){
			document.getElementById("updatePostDisplay").innerHTML = spamblocker_disabled;
		}
		function changePostFilterA(){
			document.getElementById("updatePostFilter").innerHTML = spamblocker_enabled;
		}
		function changePostFilterB(){
			document.getElementById("updatePostFilter").innerHTML = spamblocker_disabled;
		}
		function changeDeleteA(){
			document.getElementById("updateDelete").innerHTML = spamblocker_enabled;
		}
		function changeDeleteB(){
			document.getElementById("updateDelete").innerHTML = spamblocker_disabled;
		}
		function changeRedirectA(){
			document.getElementById("updateRedirect").innerHTML = spamblocker_enabled;
		}
		function changeRedirectB(){
			document.getElementById("updateRedirect").innerHTML = spamblocker_disabled;
		}	
	// ]]></script>	
	<script type="text/javascript"><!-- // --><![CDATA[
		var zUpdateStatus = function ()
		{	
			document.getElementById("expire_date").disabled = !document.getElementById("expires").checked;		
			document.getElementById("ban_post").disabled = document.getElementById("ban_full").checked;
			document.getElementById("ban_register").disabled = document.getElementById("ban_full").checked;
			document.getElementById("ban_login").disabled = document.getElementById("ban_full").checked;
		}
		addLoadEvent(zUpdateStatus);
	// ]]></script>'; 	
	$context['spamBlocker_confirm'] = '
	<script type="text/javascript"><!-- // --><![CDATA[
		function confirmSubmit()
		{
			var agree=confirm("'.$txt['spamBlocker_confirm'].'");
			if (agree)
				return true ;
			else
				return false ;
		}
		function uncheckAll(field)
		{
			for (i = 0; i < field.length; i++)
				field[i].checked = false ;
		}
	// ]]></script>';		
		
	loadTemplate('SpamBlockerAdmin');	
}

function WhitelistSpamBlocker()
{
	global $txt, $scripturl, $context, $smcFunc, $sourcedir, $modSettings, $settings;
	loadLanguage('SpamBlocker');	
	
	if (!AllowedTo('spamBlocker_settings'))
		fatal_lang_error('spamBlocker_ErrorMessage',false);	
	
	$context['robot_no_index'] = true;	
	$context[$context['admin_menu_name']]['tab_data']['description'] = $txt['spamBlockerWhitelist'];
	$_SESSION['spamBlockerIP_Error'] = !empty($_SESSION['spamBlockerIP_Error']) ? $_SESSION['spamBlockerIP_Error'] : '&nbsp;';	
	if ((empty($context['spamblocker']['user_ip'])) || !$context['spamblocker']['user_ip'])
		$_SESSION['spamBlockerIP_Error'] = false;
				
	$tableName = 'spamblocker_whitelist';	
	$whitelist = array('user_ip', 'delete');	
	$whitelistTable = array('reference', 'iplow1', 'iphigh1', 'iplow2', 'iphigh2', 'iplow3', 'iphigh3', 'iplow4', 'iphigh4');	
	$context['spamblocker_list'] = array();
		
	/*  Check for new settings values and save to database if necessary */
	if (isset($_GET['save']))
		checkSession('request');
		
	foreach ($whitelist as $user => $data)
	{		
		if (empty($context['spamblocker'][$data]))
		{
			$context['spamblocker'][$data] = false;
			continue;	
		}		
		
		if ((isset($_REQUEST['save'])) && $data == 'delete' && $context['spamblocker']['delete'][0])
		{	
			foreach ($context['spamblocker']['delete'] as $ref)
			{	
				$ref = (int)$ref;
				$request = $smcFunc['db_query']('', "DELETE FROM `{db_prefix}$tableName` WHERE `{db_prefix}$tableName`.`reference` = '{$ref}'");	
			}	
			continue;		 	
		}
		elseif ((isset($_REQUEST['save'])) && $data == 'user_ip' && $context['spamblocker']['user_ip'])
		{
			$ip = spamBlockerIP_filter($context['spamblocker']['user_ip']);	
			if (spamBlockerIP_Exists(trim($context['spamblocker']['user_ip'])) == 'banned')
			{
				$_SESSION['spamBlockerIP_Error'] = $txt['spamBlockerIP_BanError'];	
				break;
			}
			elseif (spamBlockerIP_Exists(trim($context['spamblocker']['user_ip'])) == 'whitelist')
			{
				$_SESSION['spamBlockerIP_Error'] = $txt['spamBlockerIP_ExistsError'];	
				break;
			}		
			elseif (count($ip) == 8)
			{
				list($ip_low1, $ip_high1, $ip_low2, $ip_high2, $ip_low3, $ip_high3, $ip_low4, $ip_high4) = $ip;				
				$request = $smcFunc['db_query']('', "INSERT INTO {db_prefix}spamblocker_whitelist (`ip_low1`, `ip_high1`, `ip_low2`, `ip_high2`, `ip_low3`, `ip_high3`, `ip_low4`, `ip_high4`) VALUES ('{$ip_low1}', '{$ip_high1}', '{$ip_low2}', '{$ip_high2}', '{$ip_low3}', '{$ip_high3}', '{$ip_low4}', '{$ip_high4}')");	
				$context['spamblocker']['user_ip'] = false;	
				$_SESSION['spamBlockerIP_Error'] = false;	
				continue;
			}
			else
			{			
				$_SESSION['spamBlockerIP_Error'] = $txt['spamBlockerIP_Error'];
				$context['spamblocker']['user_ip'] = '';
				break;	
			}			 	
		}	
		else
			$_SESSION['spamBlockerIP_Error'] = false;	
	}
				
	/* Read database entries for Spam Blocker whitelist settings */	
	if (!checkFieldSB($tableName,'reference')) 
		fatal_lang_error('SpamBlocker_ErrorMessageDB', false);
	
	$result1 = $smcFunc['db_query']('', "SELECT reference, ip_low1, ip_high1, ip_low2, ip_high2, ip_low3, ip_high3, ip_low4, ip_high4 FROM {db_prefix}spamblocker_whitelist WHERE reference ORDER BY ip_low1, ip_low2, ip_low3, ip_low4");
	while ($val = $smcFunc['db_fetch_assoc']($result1))
	{
		foreach ($whitelistTable as $user_ip)
		{					
			if (empty($val[$user_ip])) 
				$val[$user_ip] = false;						
		}		
		
		$context['spamblocker_list'][] = array('delete' => $val['reference'], 'ip_low1' => $val['ip_low1'], 'ip_high1' => $val['ip_high1'], 'ip_low2' => $val['ip_low2'], 'ip_high2' => $val['ip_high2'], 'ip_low3' => $val['ip_low3'], 'ip_high3' => $val['ip_high3'], 'ip_low4' => $val['ip_low4'], 'ip_high4' => $val['ip_high4']);					
	}
	$smcFunc['db_free_result']($result1);	
		
	/*  Set the $context variables for the display template  */
	$context['settings_title'] = $txt['spamBlockerSettings'];
	$context['post_url'] = $scripturl . '?action=admin;area=spamBlocker;sa=spamBlockerWhitelist;' . $context['session_var'] . '=' . $context['session_id'] . ';save';	
	$context['sub_template'] = 'spamBlocker_whitelist_page';	
	$context['spamBlocker_confirm'] = '
	<script type="text/javascript"><!-- // --><![CDATA[
		function confirmSubmit()
		{
			var agree=confirm("'.$txt['spamBlocker_confirm'].'");
			if (agree)
				return true ;
			else
				return false ;
		}
		function uncheckAll(field)
		{
			for (i = 0; i < field.length; i++)
				field[i].checked = false ;
		}
	// ]]></script>';			
	
	$context['spamBlockerAdmin_pages'] = '
	<script type="text/javascript"><!--
		var pager = new Pager("spamblocker_whitelist", 20); 
		pager.init(); 
		pager.showPageNav("pager", "WhitelistPagePosition"); 
		pager.showPage(1);
  	  //--></script>';	
	$context['spamBlockerCheckAll'] = '
	<script type="text/javascript"><!--
		function checkUncheckAll(theElement)
		{             
			var myName = theElement.name +"ete[]";
			for( i=0; i<theElement.form.elements.length;i++)
			{
				var e = theElement.form.elements[i];
				if( e.name == myName)
					e.checked = theElement.checked;
			}
		}
	// ]]></script>';
	  
	loadTemplate('SpamBlockerWhitelist');	
}

function BlacklistSpamBlocker()
{
	global $txt, $scripturl, $context, $smcFunc, $sourcedir, $modSettings, $settings;
	loadLanguage('SpamBlocker');	
	
	if (!AllowedTo('spamBlocker_settings'))
		fatal_lang_error('spamBlocker_ErrorMessage',false);	
	
	$context['robot_no_index'] = true;	
	$context[$context['admin_menu_name']]['tab_data']['description'] = $txt['spamBlockerBlacklist'];
	$_SESSION['spamBlockerIP_Msg'] = !empty($_SESSION['spamBlockerIP_Msg']) ? $_SESSION['spamBlockerIP_Msg'] : '&nbsp;';	
	if ((empty($context['spamblocker']['user_ip'])) || !$context['spamblocker']['user_ip'])
		$_SESSION['spamBlockerIP_Msg'] = false;
				
	$tableName = 'spamblocker_blacklist';	
	$blacklist = array('user_ip', 'delete', 'id_member', 'delete_member');	
	$blacklistTable = array('reference', 'iplow1', 'iphigh1', 'iplow2', 'iphigh2', 'iplow3', 'iphigh3', 'iplow4', 'iphigh4', 'id_member', 'expire_time');	
	$context['spamblocker_list'] = array();
	$context['spamblocker_whole_list'] = array();
	$key = 0;
	$track_keys = array();
	$context['spamblocker_today'] = 0;
	$context['spamBlocker_showResults'] = array(((int)$context['SB_blacklist_page'] * 1000), (((int)$context['SB_blacklist_page'] + 1) * 1000) - 1);
		
	if ((empty($_REQUEST['save'])) || !isset($_REQUEST['save']))
		$_SESSION['spamBlockerIP_Msg'] = false;	
	
	/*  Check for new setting values and save to database if necessary */
	if (isset($_GET['save']))
		checkSession('request');
		
	foreach ($blacklist as $user => $data)
	{		
		if (empty($context['spamblocker'][$data]))
		{
			$context['spamblocker'][$data] = false;
			continue;	
		}	
		
		if ((isset($_REQUEST['save'])) && $data == 'delete' && $context['spamblocker']['delete'][0])
		{					
			foreach ($context['spamblocker']['delete'] as $ban_id)
			{				
				$user_id = array();
				// $ban_count = spamBlockerBanCheck($ban_id);
				
				if ($context['spamblocker']['delete_member'] == 1)
				{					
					$result = $smcFunc['db_query']('', "SELECT black.reference, black.id_ban_group, black.id_member
										FROM {db_prefix}spamblocker_blacklist AS black										
										WHERE black.id_ban_group = {int:ban}", array('ban' => (int)$ban_id));
					while ($val = $smcFunc['db_fetch_assoc']($result))
						$user_id[] = (int)$val['id_member'];
								
					$smcFunc['db_free_result']($result);									
				}				
				
				$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}spamblocker_blacklist WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ban_id));
				$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_groups WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ban_id));
				$request = $smcFunc['db_query']('', "DELETE FROM {db_prefix}ban_items WHERE id_ban_group LIKE {int:ban}",array('ban' => (int)$ban_id));	
				
				if (count($user_id) > 0)
				{
					foreach ($user_id as $id_user)
						spamBlockerDeleteMember((int)$id_user);	
				}
			}	
						
			$_SESSION['spamBlockerIP_Msg'] = $txt['spamBlockerIP_BlacklistRemove']; 				
			continue;		 	
		}			
		else
			$_SESSION['spamBlockerIP_Msg'] = false;	
	}
				
	/* Read database entries for Spam Blocker blacklist settings */	
	if (!checkFieldSB($tableName,'reference')) 
		fatal_lang_error('SpamBlocker_ErrorMessageDB', false);
	
	if ($context['SB_blacklist_sort'] == 'ip')
		$order = 'ban.ip_low1, ban.ip_low2, ban.ip_low3, ban.ip_low4';
	elseif ($context['SB_blacklist_sort'] == 'date')
		$order = 'groups.ban_time DESC';
	else
		$order = 'groups.name ASC';	
	
	
	$result1 = $smcFunc['db_query']('', "SELECT black.reference, black.id_ban_group, black.id_member, ban.ip_low1, ban.ip_high1, ban.ip_low2,
						ban.ip_high2, ban.ip_low3, ban.ip_high3, ban.ip_low4, ban.ip_high4,
						groups.ban_time, groups.name, groups.expire_time						
						FROM {db_prefix}spamblocker_blacklist AS black						
						LEFT JOIN {db_prefix}ban_items AS ban ON (ban.id_ban_group = black.id_ban_group)
						LEFT JOIN {db_prefix}ban_groups AS groups ON (groups.id_ban_group = black.id_ban_group)	
						WHERE black.reference AND ban.ip_low1 
						ORDER BY {$order}");
	while ($val = $smcFunc['db_fetch_assoc']($result1))
	{
		if ((empty($val['reference'])) || (int)$val['reference'] == 0)
			continue;
		
		foreach ($blacklistTable as $user_ip)
		{					
			if (empty($val[$user_ip])) 
				$val[$user_ip] = false;				
		}		
		
                $date_now = date('Y-d', time());
                $date_db = date('Y-d', $val['ban_time']);
                $check_date_now = explode('-',$date_now);
                $check_date_db = explode('-', $date_db);		 
		if ((int)$check_date_now[0] == (int)$check_date_db[0] && (int)$check_date_now[1] == (int)$check_date_db[1])
			$context['spamblocker_today']++;	
		$context['spamblocker_whole_list'][] = array('delete' => $val['id_ban_group'], 'ip_low1' => $val['ip_low1'], 'ip_high1' => $val['ip_high1'], 'ip_low2' => $val['ip_low2'], 'ip_high2' => $val['ip_high2'], 'ip_low3' => $val['ip_low3'], 'ip_high3' => $val['ip_high3'], 'ip_low4' => $val['ip_low4'], 'ip_high4' => $val['ip_high4'], 'id_member' => $val['id_member'], 'ban_time' => $val['ban_time'], 'name' => $val['name'], 'expire_time' => $val['expire_time']);					
	}
	$smcFunc['db_free_result']($result1);
	
	/*  Set the $context variables for the display template  */
	$context['spamblocker_blacklist_count'] = count($context['spamblocker_whole_list']);
	$context['spamBlocker_Pages'] = (int)($context['spamblocker_blacklist_count'] / 1000) + 1;
        
        if ((int)$context['SB_blacklist_page']+1 == (int)$context['spamBlocker_Pages'])
            $context['spamBlocker_showResults'][1] = count($context['spamblocker_whole_list']);
        else
            $context['spamBlocker_showResults'][1] = (int)$context['SB_blacklist_page']*1000 + 999;         
	
	foreach($context['spamblocker_whole_list'] as $key => $value)
	{
		if ((int)$key >= (int)$context['spamBlocker_showResults'][0] && (int)$key <= (int)$context['spamBlocker_showResults'][1])
			$context['spamblocker_list'][] = $value;                       
	}        
	
	$context['spamBlocker_showResults'][1] = ((int)$context['SB_blacklist_page']*1000) + count($context['spamblocker_list']);		
	$context['settings_title'] = $txt['spamBlockerSettings'];
	$context['post_url'] = $scripturl . '?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;' . $context['session_var'] . '=' . $context['session_id'] . ';save';	
	$context['sub_template'] = 'spamBlocker_blacklist_page';	
	$context['spamBlocker_confirm'] = '
	<script type="text/javascript"><!-- // --><![CDATA[
		function confirmSubmit()
		{
			var agree=confirm("'.$txt['spamBlocker_confirm'].'");
			if (agree)
				return true ;
			else
				return false ;
		}
		function uncheckAll(field)
		{
			for (i = 0; i < field.length; i++)
				field[i].checked = false ;
		}
	// ]]></script>';		
	
	$context['spamBlockerAdmin_pages'] = '
	<script type="text/javascript"><!--
		var pager = new Pager("spamblocker_blacklist", 10); 
		pager.init(); 
		pager.showPageNav("pager", "BlacklistPagePosition"); 
		pager.showPage(1);
	//--></script>';	
	  
	$context['spamBlockerCheckAll'] = '
	<script type="text/javascript"><!-- // --><![CDATA[
		function checkUncheckAll(theElement)
		{             
			var myName = theElement.name +"ete[]";
			for( i=0; i<theElement.form.elements.length;i++)
			{
				var e = theElement.form.elements[i];
				if( e.name == myName)
					e.checked = theElement.checked;
			}
		}
	//--></script>';	    
	  
	loadTemplate('SpamBlockerBlacklist');	
}

function LookupSpamBlocker()
{
	global $context, $settings, $sourcedir, $txt, $scripturl;
	if (!AllowedTo('spamBlocker_settings'))
		fatal_lang_error('spamBlocker_ErrorMessage',false);
	
	loadLanguage('SpamBlocker');
	require_once($sourcedir .'/SpamBlocker.php');	
	
	$name = 'testing_user';		
	$data = true;	
	$context['spamBlockerIP_Error'] = false;
	$ip = !empty($context['spamblocker']['input_ip']) ? trim($context['spamblocker']['input_ip']) : $txt['spamBlocker_defaultIP'];
	$email = !empty($context['spamblocker']['input_email']) ? $context['spamblocker']['input_email'] : $txt['spamBlocker_defaultEmail'];		
	$ip_array = explode('.', $ip);
	
	if (count($ip_array) != 4)
	{
		$context['spamBlockerIP_Error'] = $txt['spamBlockerIP_InquiryErrorShort'];
		$ip_array=array();
	}
	foreach ($ip_array as $key => $checkIP)
	{
		if ((int)$checkIP < 0 || (int)$checkIP > 255)
		{
			$context['spamBlockerIP_Error'] = $txt['spamBlockerIP_InquiryErrorShort']; 
			$ip_array =  array();		 
			break;
		}      
		$ip_array[$key] = (int)$checkIP; 
	}
	
	$ip = implode('.', $ip_array);
	$email = filter_var($email, FILTER_SANITIZE_EMAIL);
	
	/*  Set the $context variables for the display template  */
	if (!filter_var($email, FILTER_VALIDATE_EMAIL ))
	{
		if ($context['spamBlockerIP_Error'])							
			$context['spamBlockerIP_Error'] .= ' ' . $txt['spamBlockerEmail_InquiryErrorAndShort'];
		else
			$context['spamBlockerIP_Error'] = $txt['spamBlockerEmail_InquiryErrorShort'];
		
		$email = false;	
	}
	
	if (count($ip_array) != 4)
		$context['spamBlocker']['ip_message'] = $txt['spamBlockerIP_InquiryError'];
	elseif (!spamBlockerRegister($name, $txt['spamBlocker_defaultEmail'], $ip, $data))
		$context['spamBlocker']['ip_message'] = str_replace('%#&$@', $ip, $txt['spamBlocker_IpLookupMessageNeg']);
	else
		$context['spamBlocker']['ip_message'] = str_replace('%#&$@', $ip, $txt['spamBlocker_IpLookupMessagePos']);

	if (!$email)
		$context['spamBlocker']['email_message'] = $txt['spamBlockerEmail_InquiryError'];
	elseif (!spamBlockerRegister($name, $email, $txt['spamBlocker_defaultIP'], $data))
		$context['spamBlocker']['email_message'] = str_replace('%#&$@', $email, $txt['spamBlocker_EmailLookupMessageNeg']);
	else
		$context['spamBlocker']['email_message'] = str_replace('%#&$@', $email, $txt['spamBlocker_EmailLookupMessagePos']);	
	
	$context['robot_no_index'] = true;	
	$context[$context['admin_menu_name']]['tab_data']['description'] = $txt['spamBlockerLookup'];	
	$context['spamblocker']['input_ip'] = $ip;
	$context['spamblocker']['input_email'] = $email;
	$context['post_url'] = $scripturl . '?action=admin;area=spamBlocker;sa=spamBlockerLookup;' . $context['session_var'] . '=' . $context['session_id'] . ';save';	
	$context['sub_template'] = 'spamBlocker_lookup_page';
	
	loadTemplate('SpamBlockerLookup');	
	
}
/* Display the Spam Blocker license */
function LicenseSpamBlocker()
{
	global $settings;
	if (!AllowedTo('spamBlocker_settings'))
		fatal_lang_error('spamBlocker_ErrorMessage',false);
		
	redirectexit($settings['default_images_url']. '/admin/spamBlocker-license.pdf'); 
	return;
}

/* Display the Spam Blocker guide */
function GuideSpamBlocker()
{
	global $settings;
	if (!AllowedTo('spamBlocker_settings'))
		fatal_lang_error('spamBlocker_ErrorMessage',false);
		
	redirectexit($settings['default_images_url']. '/admin/spamBlocker-guide.pdf'); 
	return;
}
?>
