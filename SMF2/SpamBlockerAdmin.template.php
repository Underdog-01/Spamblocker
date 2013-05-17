<?php
// Version: 1.0; spamblocker  

/*
 *	Main admin configuration template file for the Spam Blocker Mod		
 *	c/o Underdog @ http://askusaquestion.net				  
*/

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://askusaquestion.net	
 * Copyright 2013 Underdog@askusaquestion.net
 * This software package is distributed under the terms of its Freeware License
 * http://askusaquestion.net/index.php/page=spamblocker_license
*/

/* Spam Blocker configuration display  */
function template_spamBlocker_settings_page()
{
	global $txt, $scripturl, $context, $settings, $modSettings;
	isAllowedTo('spamBlocker_settings');	
	loadLanguage('SpamBlocker');
	
	$dir = $context['spamblocker_styles']['bg'];
	$color = $context['spamblocker_styles']['color'];
	$selected = array($txt['spamBlockerOff'],'50','100','250');
	$optimize = !empty($modSettings['spamBlocker_optimizeInt']) ? $modSettings['spamBlocker_optimizeInt'] : '50';
	
	/* Onclick javascript actions  */
	echo $context['toggle'], $context['spamBlocker_confirm'], $context['spamblocker_enable_disable'];
 
	/*   Display Spam Blocker Settings */	
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[		
		var spamblocker_delBlacklistWarningX = ', json_encode(iconv($context['character_set'], 'UTF-8', $txt['spamBlocker_delBlacklistWarningX'])),'
		var spamblocker_delBlacklistWarningY = ', json_encode(iconv($context['character_set'], 'UTF-8', $txt['spamBlocker_delBlacklistWarningY'])),'
	// ]]></script>
	<form action="'. $context['post_url']. '" method="post" accept-charset="'. $context['character_set']. '" name="spamblocker_config">
		<div class="cat_bar"><h4 class="catbg" style="text-align:center;">' . $txt['spamBlocker_general'] . '</h4></div>
		<br /><br />	
		<table border="0" cellspacing="0" cellpadding="4" width="100%">		
			<tr style="font-size:x-small;" class="catbg3">
				<td style="height:15px;border:0px;background: url(',$dir,') no-repeat 1% -160px;border-top-left-radius:10px;-webkit-border-top-left-radius:10px;-moz-border-top-left-radius:10px;">
					<span style="float:left;text-indent:1px;',$color,'">' . $txt['spamBlocker_settings'] . '</span>
				</td>
				<td style="height:15px;border:0px;background: url(',$dir,') no-repeat 99% -160px;border-top-right-radius:10px;-webkit-border-top-right-radius:10px;-moz-border-top-right-radius:10px;">
					<span class="error" style="float:right;position:relative;right:10px;text-shadow:0 0 3px">' .$_SESSION['spamBlockerConfig_Msg']. '</span>
				</td>
			</tr>';
			
	/* Main configuration */		
	echo '		
			<tr>
				<td class="titlebg" style="font-size:small;border-top: thin solid;text-align:center" colspan="2">
					<span>',$txt['spamBlockerMainConfig'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_mod'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_enableModHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableMod'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateModA" name="enable_mod[]" value="2" onclick="changeModA();" checked="checked" />
					<input type="radio" id="updateModB" name="enable_mod[]" value="1" onclick="changeModB();" />&nbsp;&nbsp;
					<span id="updateMod">',$context['spamblocker']['sb_mod'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_enableModHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableMod'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateModA" name="enable_mod[]" value="2" onclick="changeModA();" />
					<input type="radio" id="updateModB" name="enable_mod[]" value="1" onclick="changeModB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateMod">',$context['spamblocker']['sb_mod'],'</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_pass'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbPassHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_pass'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePassA" name="enable_pass[]" value="2" onclick="changePassA();" checked="checked" />
					<input type="radio" id="updatePassB" name="enable_pass[]" value="1" onclick="changePassB();" />&nbsp;&nbsp;
					<span id="updatePass">',$context['spamblocker']['sb_pass'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbPassHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_pass'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePassA" name="enable_pass[]" value="2" onclick="changePassA();" />
					<input type="radio" id="updatePassB" name="enable_pass[]" value="1" onclick="changePassB();" checked="checked" />&nbsp;&nbsp;
					<span id="updatePass">',$context['spamblocker']['sb_pass'],'</span>
				</td>
			</tr>';	

	if ($context['spamblocker']['sb_reset'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbResetHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_reset'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateResetA" name="enable_reset[]" value="2" onclick="changeResetA();" checked="checked" />
					<input type="radio" id="updateResetB" name="enable_reset[]" value="1" onclick="changeResetB();" />&nbsp;&nbsp;
					<span id="updateReset">',$context['spamblocker']['sb_reset'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbResetHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_reset'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateResetA" name="enable_reset[]" value="2" onclick="changeResetA();" />
					<input type="radio" id="updateResetB" name="enable_reset[]" value="1" onclick="changeResetB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateReset">',$context['spamblocker']['sb_reset'],'</span>
				</td>
			</tr>';			
	if ($context['spamblocker']['sb_ban'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;vertical-align:top;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbBanHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_banned'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateBanA" name="ban_option[]" value="2" onclick="changeBanA();" checked="checked" />
					<input type="radio" id="updateBanB" name="ban_option[]" value="1" onclick="changeBanB();" />&nbsp;&nbsp;
					<span id="updateBan">',$context['spamblocker']['sb_ban'],'</span>			
				</td>				
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;vertical-align:top;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbBanHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_banned'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateBanA" name="ban_option[]" value="2" onclick="changeBanA();" />
					<input type="radio" id="updateBanB" name="ban_option[]" value="1" onclick="changeBanB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateBan">',$context['spamblocker']['sb_ban'],'</span>					
				</td>				
			</tr>';
	if ($context['spamblocker']['sb_hide_members'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;vertical-align:top;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbHideHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_hide_members'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateHideA" name="hide_members[]" value="2" onclick="changeHideA();" checked="checked" />
					<input type="radio" id="updateHideB" name="hide_members[]" value="1" onclick="changeHideB();" />&nbsp;&nbsp;
					<span id="updateHide">',$context['spamblocker']['sb_hide_members'],'</span>			
				</td>				
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;vertical-align:top;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbHideHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_hide_members'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateHideA" name="hide_members[]" value="2" onclick="changeHideA();" />
					<input type="radio" id="updateHideB" name="hide_members[]" value="1" onclick="changeHideB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateHide">',$context['spamblocker']['sb_hide_members'],'</span>					
				</td>				
			</tr>';
	if ($context['spamblocker']['sb_delete_members'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;vertical-align:top;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbDeleteHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_delete_members'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateDeleteA" name="delete_members[]" value="2" onclick="changeDeleteA();" checked="checked" />
					<input type="radio" id="updateDeleteB" name="delete_members[]" value="1" onclick="changeDeleteB();" />&nbsp;&nbsp;
					<span id="updateDelete">',$context['spamblocker']['sb_delete_members'],'</span>			
				</td>				
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;vertical-align:top;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbDeleteHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_delete_members'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateDeleteA" name="delete_members[]" value="2" onclick="changeDeleteA();" />
					<input type="radio" id="updateDeleteB" name="delete_members[]" value="1" onclick="changeDeleteB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateDelete">',$context['spamblocker']['sb_delete_members'],'</span>					
				</td>				
			</tr>';
			
	/* Registration filtering */ 
	echo '
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlockerRegFiltering'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_akismet'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbAkismetEnableHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableAkismet'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateAkismetA" name="enable_akismet[]" value="2" onclick="changeAkismetA();" checked="checked" />
					<input type="radio" id="updateAkismetB" name="enable_akismet[]" value="1" onclick="changeAkismetB();" />&nbsp;&nbsp;
					<span id="updateAkismet">',$context['spamblocker']['sb_akismet'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbAkismetEnableHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableAkismet'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateAkismetA" name="enable_akismet[]" value="2" onclick="changeAkismetA();" />
					<input type="radio" id="updateAkismetB" name="enable_akismet[]" value="1" onclick="changeAkismetB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateAkismet">',$context['spamblocker']['sb_akismet'],'</span>
				</td>
			</tr>';			
	if ($context['spamblocker']['sb_email'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSFSEnableEmailHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerEmail'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateEmailA" name="enable_email[]" value="2" onclick="changeEmailA();" checked="checked" />
					<input type="radio" id="updateEmailB" name="enable_email[]" value="1" onclick="changeEmailB();" />&nbsp;&nbsp;
					<span id="updateEmail">',$context['spamblocker']['sb_email'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSFSEnableEmailHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerEmail'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateEmailA" name="enable_email[]" value="2" onclick="changeEmailA();" />
					<input type="radio" id="updateEmailB" name="enable_email[]" value="1" onclick="changeEmailB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateEmail">',$context['spamblocker']['sb_email'],'</span>
				</td>
			</tr>';	
	if ($context['spamblocker']['sb_sfs'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSFSEnableIpHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableSFS'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateSfsA" name="enable_sfs[]" value="2" onclick="changeSfsA();" checked="checked" />
					<input type="radio" id="updateSfsB" name="enable_sfs[]" value="1" onclick="changeSfsB();" />&nbsp;&nbsp;
					<span id="updateSfs">',$context['spamblocker']['sb_sfs'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSFSEnableIpHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableSFS'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateSfsA" name="enable_sfs[]" value="2" onclick="changeSfsA();" />
					<input type="radio" id="updateSfsB" name="enable_sfs[]" value="1" onclick="changeSfsB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateSfs">',$context['spamblocker']['sb_sfs'],'</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_honeypot'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbHoneypotEnableIpHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableHoneypot'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateHoneypotA" name="enable_honeypot[]" value="2" onclick="changeHoneypotA();" checked="checked" />
					<input type="radio" id="updateHoneypotB" name="enable_honeypot[]" value="1" onclick="changeHoneypotB();" />&nbsp;&nbsp;
					<span id="updateHoneypot">',$context['spamblocker']['sb_honeypot'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;"> 
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbHoneypotEnableIpHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableHoneypot'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateHoneypotA" name="enable_honeypot[]" value="2" onclick="changeHoneypotA();" />
					<input type="radio" id="updateHoneypotB" name="enable_honeypot[]" value="1" onclick="changeHoneypotB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateHoneypot">',$context['spamblocker']['sb_honeypot'],'</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_spamhaus'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSpamhausEnableIpHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableSpamhaus'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateSpamhausA" name="enable_spamhaus[]" value="2" onclick="changeSpamhausA();" checked="checked" />
					<input type="radio" id="updateSpamhausB" name="enable_spamhaus[]" value="1" onclick="changeSpamhausB();" />&nbsp;&nbsp;
					<span id="updateSpamhaus">',$context['spamblocker']['sb_spamhaus'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;"> 
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSpamhausEnableIpHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableSpamhaus'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateSpamhausA" name="enable_spamhaus[]" value="2" onclick="changeSpamhausA();" />
					<input type="radio" id="updateSpamhausB" name="enable_spamhaus[]" value="1" onclick="changeSpamhausB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateSpamhaus">',$context['spamblocker']['sb_spamhaus'],'</span>
				</td>
			</tr>';			
	if ($context['spamblocker']['sb_enableRedirect'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbEnableRedirectHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableRedirect'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateRedirectA" name="enable_redirect[]" value="2" onclick="changeRedirectA();" checked="checked" />
					<input type="radio" id="updateRedirectB" name="enable_redirect[]" value="1" onclick="changeRedirectB();" />&nbsp;&nbsp;
					<span id="updateRedirect">',$context['spamblocker']['sb_enableRedirect'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;"> 
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbEnableRedirectHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableRedirect'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateRedirectA" name="enable_redirect[]" value="2" onclick="changeRedirectA();" />
					<input type="radio" id="updateRedirectB" name="enable_redirect[]" value="1" onclick="changeRedirectB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateRedirect">',$context['spamblocker']['sb_enableRedirect'],'</span>
				</td>
			</tr>';			
	echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbRedirectUrlHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerRedirectUrl'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="redirect_path" name="redirect_path[]" size="50" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['redirect_path'].'" />
				</td>
			</tr>';
			
	/* Honeypot configuration */
	if ($context['spamblocker']['sb_honeypot'] == $txt['spamBlocker_enabled'])
	{
		echo '
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlockerHoneypotConfig'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbHoneypotThreatHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerHoneypotThreat'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="honeypot_threat" name="honeypot_threat[]" size="4" maxlength="3" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['honeypot_threat'].'" />
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbHoneypotTypeHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerHoneypotType'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="honeypot_type" name="honeypot_type[]" size="50" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['honeypot_type'].'" />
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbHoneypotKeyHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerHoneypotKey'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="honeypot_key" name="honeypot_key[]" size="50" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['honeypot_key'].'" />
				</td>
			</tr>';
			
			
	}		
	/* Post filtering */		
	echo '
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlockerPostFiltering'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>';				
	if ($context['spamblocker']['sb_akismet_post'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbAkismetPostHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableAkismetPost'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostA" name="enable_post[]" value="2" onclick="changePostA();" checked="checked" />
					<input type="radio" id="updatePostB" name="enable_post[]" value="1" onclick="changePostB();" />&nbsp;&nbsp;
					<span id="updatePost">',$context['spamblocker']['sb_akismet_post'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbAkismetPostHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableAkismetPost'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostA" name="enable_post[]" value="2" onclick="changePostA();" />
					<input type="radio" id="updatePostB" name="enable_post[]" value="1" onclick="changePostB();" checked="checked" />&nbsp;&nbsp;
					<span id="updatePost">',$context['spamblocker']['sb_akismet_post'],'</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_postFilter'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbPostFilterEnableHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enablePostFilter'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostFilterA" name="enable_postFilter[]" value="2" onclick="changePostFilterA();" checked="checked" />
					<input type="radio" id="updatePostFilterB" name="enable_postFilter[]" value="1" onclick="changePostFilterB();" />&nbsp;&nbsp;
					<span id="updatePostFilter">',$context['spamblocker']['sb_postFilter'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbPostFilterEnableHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enablePostFilter'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostFilterA" name="enable_postFilter[]" value="2" onclick="changePostFilterA();" />
					<input type="radio" id="updatePostFilterB" name="enable_postFilter[]" value="1" onclick="changePostFilterB();" checked="checked" />&nbsp;&nbsp;
					<span id="updatePostFilter">',$context['spamblocker']['sb_postFilter'],'</span>
				</td>
			</tr>';				
	if ($context['spamblocker']['sb_postSFS'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_PostSFSHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enablePostSFS'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostSFSA" name="enable_postSFS[]" value="2" onclick="changePostSFSA();" checked="checked" />
					<input type="radio" id="updatePostSFSB" name="enable_postSFS[]" value="1" onclick="changePostSFSB();" />&nbsp;&nbsp;
					<span id="updatePostSFS">',$context['spamblocker']['sb_postSFS'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_PostSFSHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enablePostSFS'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostSFSA" name="enable_postSFS[]" value="2" onclick="changePostSFSA();" />
					<input type="radio" id="updatePostSFSB" name="enable_postSFS[]" value="1" onclick="changePostSFSB();" checked="checked" />&nbsp;&nbsp;
					<span id="updatePostSFS">',$context['spamblocker']['sb_postSFS'],'</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_postDisplay'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_PostDisplayHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enablePostDisplay'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostDisplayA" name="enable_postDisplay[]" value="2" onclick="changePostDisplayA();" checked="checked" />
					<input type="radio" id="updatePostDisplayB" name="enable_postDisplay[]" value="1" onclick="changePostDisplayB();" />&nbsp;&nbsp;
					<span id="updatePostDisplay">',$context['spamblocker']['sb_postDisplay'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_PostDisplayHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enablePostDisplay'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updatePostDisplayA" name="enable_postDisplay[]" value="2" onclick="changePostDisplayA();" />
					<input type="radio" id="updatePostDisplayB" name="enable_postDisplay[]" value="1" onclick="changePostDisplayB();" checked="checked" />&nbsp;&nbsp;
					<span id="updatePostDisplay">',$context['spamblocker']['sb_postDisplay'],'</span>
				</td>
			</tr>';
	echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbAkismetLimitHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerAkismetCount'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="akismet_count" name="akismet_count[]" size="4" maxlength="3" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['akismet_count'].'" />
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbAkismetKeyHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerAkismetKey'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="akismet_key" name="akismet_key[]" size="50" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['akismet_key'].'" />
					<span class="alert" style="vertical-align:middle;float:right;position:relative;right:10px;text-shadow:0 0 3px">' .$_SESSION['spamBlockerConfig_Key']. '</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_KeySFSHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerKeySFS'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="sfs_key" name="sfs_key[]" size="50" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['sfs_key'].'" />
					<span class="alert" style="vertical-align:middle;float:right;position:relative;right:10px;text-shadow:0 0 3px">' .$_SESSION['spamBlockerConfigSFS_Key']. '</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_postFilter'] == $txt['spamBlocker_enabled'])
	{
		echo '
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlocker_configPostFilter'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbLinksCountHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerLinksCount'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="links_count" name="links_count[]" size="4" maxlength="3" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['links_count'].'" />
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbImagesCountHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerImagesCount'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="text" id="images_count" name="images_count[]" size="4" maxlength="3" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['images_count'].'" />
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbCharsCountHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerCharsCount'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<span>
						<input type="text" id="chars_lowCount" name="chars_low_count[]" size="4" maxlength="3" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['chars_low_count'].'" />
					</span>
					<span style="position:relative;left:10px;">
						<input type="text" id="chars_count" name="chars_count[]" size="4" maxlength="3" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['chars_count'].'" />
					</span>					
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbWordCountHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerWordCount'], '
				</td>
				<td class="windowbg" style="text-align:left;">					
					<input type="text" id="word_count" name="word_count[]" size="4" maxlength="3" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['word_count'].'" />
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<span style="position:relative;visibility:visible;">
						<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbTextFilterHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
							<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
						</a>
					</span>	
					', $txt['spamBlocker_wordsFilter'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<textarea style="white-space:pre-wrap;" id="text_filter" name="text_filter[]" rows="5" cols="50" tabindex="5">',$context['spamblocker']['text_filter'],'</textarea>
				</td>
			</tr>';
	}
	/* Logging configuration */		
	echo '
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlockerLogs'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>';		
	if ($context['spamblocker']['sb_err'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbErrHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_error'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateErrA" name="enable_errorlog[]" value="2" onclick="changeErrA();" checked="checked" />
					<input type="radio" id="updateErrB" name="enable_errorlog[]" value="1" onclick="changeErrB();" />&nbsp;&nbsp;
					<span id="updateErr">',$context['spamblocker']['sb_err'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbErrHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_error'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateErrA" name="enable_errorlog[]" value="2" onclick="changeErrA();" />
					<input type="radio" id="updateErrB" name="enable_errorlog[]" value="1" onclick="changeErrB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateErr">',$context['spamblocker']['sb_err'],'</span>
				</td>
			</tr>';

	if ($context['spamblocker']['sb_smf_err'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSmfErrHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_smf_error'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateSmfErrA" name="smf_error[]" value="2" onclick="changeSmfErrA();" checked="checked" />
					<input type="radio" id="updateSmfErrB" name="smf_error[]" value="1" onclick="changeSmfErrB();" />&nbsp;&nbsp;
					<span id="updateSmfErr">',$context['spamblocker']['sb_smf_err'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSmfErrHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_smf_error'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateSmfErrA" name="smf_error[]" value="2" onclick="changeSmfErrA();" />
					<input type="radio" id="updateSmfErrB" name="smf_error[]" value="1" onclick="changeSmfErrB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateSmfErr">',$context['spamblocker']['sb_smf_err'],'</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_connection_errs'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbConnErrsHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableConnErrs'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateErrsA" name="enable_errs[]" value="2" onclick="changeErrsA();" checked="checked" />
					<input type="radio" id="updateErrsB" name="enable_errs[]" value="1" onclick="changeErrsB();" />&nbsp;&nbsp;
					<span id="updateErrs">',$context['spamblocker']['sb_connection_errs'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbConnErrsHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableConnErrs'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateErrsA" name="enable_errs[]" value="2" onclick="changeErrsA();" />
					<input type="radio" id="updateErrsB" name="enable_errs[]" value="1" onclick="changeErrsB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateErrs">',$context['spamblocker']['sb_connection_errs'],'</span>
				</td>
			</tr>';
	if ($context['spamblocker']['sb_reporting_errs'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbReportErrsHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableReportErrs'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateReportErrsA" name="report_errs[]" value="2" onclick="changeReportErrsA();" checked="checked" />
					<input type="radio" id="updateReportErrsB" name="report_errs[]" value="1" onclick="changeReportErrsB();" />&nbsp;&nbsp;
					<span id="updateReportErrs">',$context['spamblocker']['sb_reporting_errs'],'</span>
				</td>
			</tr>';
	else
		echo '
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbReportErrsHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_enableReportErrs'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<input type="radio" id="updateReportErrsA" name="report_errs[]" value="2" onclick="changeReportErrsA();" />
					<input type="radio" id="updateReportErrsB" name="report_errs[]" value="1" onclick="changeReportErrsB();" checked="checked" />&nbsp;&nbsp;
					<span id="updateReportErrs">',$context['spamblocker']['sb_reporting_errs'],'</span>
				</td>
			</tr>';	
	/* Messaging configuration */		
	echo '
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlockerMessaging'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>';				
	echo '		
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbUserMsgHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_user_message'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<textarea id="user_message" name="user_message[]" rows="3" cols="50" tabindex="5">',$context['spamblocker']['user_message'],'</textarea>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbLogMsgHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_error_message'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<textarea id="error_message" name="error_message[]" rows="3" cols="50" tabindex="5">',$context['spamblocker']['error_message'],'</textarea>
				</td>
			</tr>';
	
	/* Maintenance configuration */		
	echo '		
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlockerMaintenance'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbAutoOptimizeHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_optimize'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<select style="vertical-align:top;" id="optimize" name="optimize[]" onchange="uncheckAll(document.spamblocker_config); this.form.submit();">';
	foreach ($selected as $select)
	{		
		if ($select == $optimize)
			echo '
						<option selected>',$select,'</option>';
		else
			echo '
						<option>', $select, '</option>';	
	}
	echo '
					</select>
				</td>
			</tr>';
	
		
	echo '
			<tr>				
				<td class="windowbg" style="text-align:left;">
					<br />
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbExpiredHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_expired'], '					
				</td>
				<td class="windowbg" style="text-align:left;">
					<br />
					<input style="position:relative;top:2px;" type="checkbox" id="updateExpiredA" name="enable_expired[]" value="2" />&nbsp;&nbsp;
					<span style="font-size:9px;font-family:gadget;text-align:left;position:relative;border:1px dotted;padding:3px;">						
						<input style="position:relative;top:2px;" type="checkbox" name="delete_member[]" checked="checked" value="1" />
						',$txt['spamBlockerDelMembers'],'
					</span>											
				</td>
			</tr>';

	echo '
			<tr>
				<td class="windowbg" style="text-align:left;"><br />
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbDelBlacklistHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_delBlacklist'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<br />
					<input onclick="if (document.getElementById(\'delBlacklist\').checked){var check=confirm(spamblocker_delBlacklistWarningX+\'\n\n\'+spamblocker_delBlacklistWarningY); if(check==false){document.getElementById(\'delBlacklist\').checked = false;}}" type="checkbox" id="delBlacklist" name="enable_delBlacklist[]" value="2" />					
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;"><br />
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_DefaultHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_default'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<br />
					<input type="checkbox" id="updateDefaultA" name="enable_default[]" value="2" />					
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="text-align:left;"><br />
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbEmendHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlocker_emend'], '
				</td>
				<td class="windowbg" style="text-align:left;">
					<br />
					<input type="checkbox" id="updateEmendA" name="enable_emend[]" value="2" />					
				</td>
			</tr>';
	
	/* Ban configuration */															
	if ($context['spamblocker']['sb_ban'] == $txt['spamBlocker_enabled'])		
		echo '
			<tr>
				<td class="windowbg" style="line-height:0px;border-bottom: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>			
			<tr>
				<td class="titlebg" style="font-size:small;text-align:center" colspan="2">
					<span>',$txt['spamBlockerBanConfig'],'</span>
				</td>
			</tr>
			<tr>
				<td class="windowbg" style="line-height:0px;border-top: thin solid;" colspan="2">
					<span>&nbsp;</span>
				</td>
			</tr>
			<tr>				
			<td class="windowbg" style="text-align:left;vertical-align:bottom;" colspan="2">						
					<span class="windowbg" style="text-align:left;">
						<br /><br />
						<span class="ban_settings floatleft" style="border-style:dotted;border-width:1px;width:inherit;">
							<legend>
								<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbSetExpireHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
									<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
								</a>
								', $txt['ban_expiration'], '
							</legend>
							<input type="radio" name="expiration[]" value="never" id="never_expires" onclick="zUpdateStatus();"', $context['spamblocker']['status'] == 'never' ? ' checked="checked"' : '', ' class="input_radio" /> <label for="never_expires">', $txt['never'], '</label><br />
							<input type="radio" name="expiration[]" value="one_day" id="expires" onclick="zUpdateStatus();"', $context['spamblocker']['status'] == 'active' ? ' checked="checked"' : '', ' class="input_radio" /> <label for="expires_one_day">', $txt['ban_will_expire_within'], '</label>: <input type="text" name="expire_time[]" id="expire_date" size="3" value="', $context['spamblocker']['expire_time'], '" class="input_text" /> ', $txt['ban_days'], '<br />						
						</span>
						<span class="ban_settings floatright" style="border-style:dotted;border-width:1px;">
							<legend>
								<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbRestrictHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
									<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
								</a>
								', $txt['ban_restriction'], '
							</legend>
							<input type="radio" name="ban_full" id="ban_full" value="1" onclick="zUpdateStatus();"', $context['spamblocker']['ban_full'] ? ' checked="checked"' : '', ' class="input_radio" /> <label for="ban_full">', $txt['ban_full_ban'], '</label><br />
							<input type="radio" name="ban_full" id="ban_partial" value="0" onclick="zUpdateStatus();"', !$context['spamblocker']['ban_full'] ? ' checked="checked"' : '', ' class="input_radio" /> <label for="ban_partial">', $txt['ban_partial_ban'], '</label><br />
							<input type="checkbox" name="ban_post" id="ban_post" value="1"', $context['spamblocker']['ban_post'] ? ' checked="checked"' : '', ' class="ban_restriction input_radio" /> <label for="ban_post">', $txt['ban_cannot_post'], '</label> (<a href="', $scripturl, '?action=helpadmin;help=ban_cannot_post" onclick="return reqWin(this.href);">?</a>)<br />
							<input type="checkbox" name="ban_register" id="ban_register" value="1"', $context['spamblocker']['ban_register'] ? ' checked="checked"' : '', ' class="ban_restriction input_radio" /> <label for="ban_register">', $txt['ban_cannot_register'], '</label><br />
							<input type="checkbox" name="ban_login" id="ban_login" value="1"', $context['spamblocker']['ban_login'] ? ' checked="checked"' : '', ' class="ban_restriction input_radio" /> <label for="ban_login">', $txt['ban_cannot_login'], '</label><br />
						</span>
					</span>	
				</td>			
			</tr>';
										
	echo '
			<tr class="catbg3">
				<td style="position:relative;border:0px;background: url(',$dir,') no-repeat 1% -173px;height:15px;border-bottom-left-radius:15px;-webkit-border-bottom-left-radius:15px;-moz-border-bottom-left-radius:15px;"></td>
				<td style="position:relative;border:0px;background: url(',$dir,') no-repeat 99% -173px;height:15px;border-bottom-right-radius:15px;-webkit-border-bottom-right-radius:15px;-moz-border-bottom-right-radius:15px;"></td>
			</tr>
		</table>
		<br /><br />
		<h4 style="text-align:center;">			
			<span style="float:right;">
				<input type="submit" value="'. $txt['spamblocker_submit']. '"'. (!empty($context['save_disabled']) ? ' disabled="disabled"' : ''). ' onclick="var confirm = confirmSubmit(); if (confirm) {this.form.submit();} else {uncheckAll(document.spamblocker); return false;}" />
			</span>
		</h4>				
		<input type="hidden" name="sc" value="'. $context['session_id']. '" />
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
	</form>';
}
?>