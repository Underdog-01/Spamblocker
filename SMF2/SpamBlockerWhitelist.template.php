<?php
// Version: 1.0; spamblocker

/*
 *	Main admin whitelist template file for the Spam Blocker Mod	
 *	c/o Underdog @ http://webdevelop.comli.com			
*/

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://webdevelop.comli.com	
 * Copyright 2013 underdog@webdevelop.comli.com
 * This software package is distributed under the terms of its Freeware License
 * http://webdevelop.comli.com/index.php/page=spamblocker_license
*/

/* Spam Blocker whitelist display  */
function template_spamBlocker_whitelist_page()
{
	global $txt, $scripturl, $context, $settings, $boardurl;
	isAllowedTo('spamBlocker_settings');
	loadLanguage('SpamBlocker');
	$column = 1;	
	$rows = 0;
	$row_switch = 0;
	$selected = array(5,10,50,100);
	$dir = $context['spamblocker_styles']['bg'];
	$color = $context['spamblocker_styles']['color'];
	$stylex = '';
	if ($settings['name'] == 'Core Theme')
		$stylex = 'position:relative;right:1px;';
		
	echo $context['spamBlocker_confirm'], $context['spamBlockerCheckAll'];
	echo '
	<script type="text/javascript"><!-- // --><![CDATA[
		var spamblocker_prev = ', json_encode(iconv($context['character_set'], 'UTF-8', $context['spamblocker_prev'])),'
		var spamblocker_next = ', json_encode(iconv($context['character_set'], 'UTF-8', $context['spamblocker_next'])),'
		var spamblocker_prev_plus = ', json_encode(iconv($context['character_set'], 'UTF-8', $context['spamblocker_prev_plus'])),'
		var spamblocker_next_plus = ', json_encode(iconv($context['character_set'], 'UTF-8', $context['spamblocker_next_plus'])),'
		var vertical_bar_y = ', json_encode(iconv($context['character_set'], 'UTF-8', $context['vertical_bar_y'])),'
		var vertical_bar_x = ', json_encode(iconv($context['character_set'], 'UTF-8', $context['vertical_bar_x'])),'		
		var default_theme_sb = ', json_encode(iconv($context['character_set'], 'UTF-8', $settings['default_theme_url'])),'
		var spamblocker_page = ', json_encode(iconv($context['character_set'], 'UTF-8', $txt['spamBlockerPage'])),'
		var spamblocker_of = ', json_encode(iconv($context['character_set'], 'UTF-8', $txt['spamBlockerOf'])),'
		var Xincrement = ', json_encode(iconv($context['character_set'], 'UTF-8', $context['SB_increment'])),'		
	// ]]></script>
	<script type="text/javascript" src="'.$boardurl.'/Themes/default/scripts/spamblocker-pages.js"></script>
	<form action="'. $context['post_url']. '" method="post" accept-charset="'. $context['character_set']. '" name="spamblocker">
		<div class="cat_bar"><h4 class="catbg" style="text-align:center;">' . $txt['spamBlockerIpWhitelist'] . '</h4></div>
		<br /><br />	
		<table border="0" cellspacing="0" cellpadding="4" width="99%">	
			<tr style="font-size:x-small;" class="catbg3">
				<td style="height:15px;border:0px;background: url(',$dir,') no-repeat 1% -160px;border-top-left-radius:10px;-webkit-border-top-left-radius:10px;-moz-border-top-left-radius:10px;">
					<span style="float:left;text-indent:1px;',$color,'">' . $txt['spamBlockerWhitelist'] . '</span>
				</td>
				<td style="height:15px;border:0px;background: url(',$dir,') no-repeat 99% -160px;border-top-right-radius:10px;-webkit-border-top-right-radius:10px;-moz-border-top-right-radius:10px;">
					<span class="alert" style="float:right;position:relative;right:10px;text-shadow:0 0 3px">', $_SESSION['spamBlockerIP_Error'], '</span>
				</td> 
			</tr>		
			<tr>
				<td class="windowbg" style="text-align:left;text-indent:5px;">
					<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbWhitelistHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
						<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
					</a>
					', $txt['spamBlockerIpWhitelistAdd'], '
				</td>
				<td class="windowbg" style="text-align:left;line-height:20px;">
					<input type="text" id="user_ip" name="user_ip[]" size="50" onkeydown="if (event.keyCode == 13) {onclick=\'confirmation()\';}" value="'.$context['spamblocker']['user_ip'].'" />
				</td>
			</tr>';
							
	echo '
			<tr class="catbg3">
				<td style="height:2px;border:0px;background: url(',$settings['actual_theme_url'],'/images/theme/main_block.png) no-repeat 1% -173px;"></td>
				<td style="height:2px;border:0px;background: url(',$settings['actual_theme_url'],'/images/theme/main_block.png) no-repeat 99% -173px;"></td>
			</tr>			
		</table>
		<table border="0" cellspacing="0" cellpadding="7" width="99%" class="windowbg">		
			<tr style="font-size:x-small;">
				<td class="windowbg" style="line-height:0px;" colspan="6">&nbsp;</td>				
			</tr>
			<tr class="titlebg" style="font-size:x-small;">
				<td class="windowbg" style="width:28%;border-bottom: thin solid;">
					<span style="position:relative;left:1px;">',$txt['spamBlockerID'],'</span>
				</td>
				<td class="windowbg" style="width:5%;height:8%;line-height:20px;border-bottom: thin solid;">
					<span style="position:relative;right:5%;">',$txt['spamBlockerDelete'],'</span>
				</td>
				<td class="windowbg" style="width:28%;border-left: 1px solid;border-bottom: thin solid;">
					<span style="position:relative;left:1px;">',$txt['spamBlockerID'],'</span>
				</td>
				<td class="windowbg" style="width:5%;height:8%;line-height:20px;border-bottom: thin solid;">
					<span style="position:relative;right:5%;">',$txt['spamBlockerDelete'],'</span>
				</td>
				<td class="windowbg" style="',$stylex,'width:28%;border-left: 1px solid;border-bottom: thin solid;">
					<span style="position:relative;left:1px;">',$txt['spamBlockerID'],'</span>
				</td>
				<td class="windowbg" style="width:5%;height:8%;line-height:20px;border-bottom: thin solid;">
					<span style="position:relative;right:5%;">',$txt['spamBlockerDelete'],'</span>
				</td>			
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="7" width="99%" id="spamblocker_whitelist" class="windowbg2">		
			<tr>								
				<td style="line-height:2px;position:absolute;font-size:x-small;" colspan="6">
					<span></span>										
				</td>			
			</tr>		
			<tr class="windowbg2" style="font-size:x-small;">';
			
	foreach ($context['spamblocker_list'] as $user => $data)
	{		
		$ipLow = trim(implode('.', array($data['ip_low1'], $data['ip_low2'], $data['ip_low3'], $data['ip_low4'])));
		$ipHigh = trim(implode('.', array($data['ip_high1'], $data['ip_high2'], $data['ip_high3'], $data['ip_high4'])));		
		if ($column != 1)
			$style = 'border-left: 1px solid;';
		else
			$style = '';	
				
		if ($ipLow == $ipHigh)				
			echo '			
				<td class="windowbg2" style="width:28%;height:8%;line-height:20px;'.$style.'">',$ipLow,'</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;" class="windowbg2" type="checkbox" id="delete'.$data['delete'].'" name="delete[]" value="'.$data['delete'].'" />
				</td>';
		else
			echo '			
				<td class="windowbg2" style="width:28%;height:8%;line-height:20px;'.$style.'">',$ipLow.' - '.$ipHigh,'</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;" class="windowbg2" type="checkbox" id="delete'.$data['delete'].'" name="delete[]" value="'.$data['delete'].'" />
				</td>';
				
		if ($column == 3)
		{
			echo '
			</tr>			
			<tr style="font-size:x-small;z-index:-1;height:0px;line-height:0px;" class="windowbg2">
				<td class="windowbg2" style="border-top: thin dotted;height:0px;line-height:0px;padding:0%;display:hidden;" colspan="6">
					<span style="display:none;">%nbsp;</span>
				</td>
			</tr>
			<tr style="font-size:x-small;" class="windowbg2">';
			$column = 0;			
		}			
		$column++;	
		$rows++;
	}
	
	if ($rows == 0)
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;" colspan="6">
					<span class="windowbg2"></span>
				</td>';
		
	if ($column == 1)
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;visibility:hidden;" colspan="6">
					<span class="windowbg2"></span>
				</td>				
			</tr>';		
	elseif ($column == 2)
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;width:28%;border-left: 1px solid;">&nbsp;</td>				
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;visibility:hidden;" class="windowbg2" type="checkbox" name="xfill[]" value="" disabled=true />
				</td>
				<td class="windowbg2" style="height:8%;line-height:20px;width:28%;border-left: 1px solid;">&nbsp;</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;visibility:hidden;" class="windowbg2" type="checkbox" name="yfill[]" value="" disabled=true />
				</td>					
			</tr>';
	else
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;width:28%;border-left: 1px solid;">&nbsp;</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;visibility:hidden;" class="windowbg2" type="checkbox" name="yfill[]" value="" disabled=true />
				</td>				
			</tr>';			
					 		
	echo '		
		</table>	
		<table border="0" cellspacing="0" cellpadding="4" width="99%">		
			<tr class="catbg3">
				<td style="position:relative;border:0px;background: url(',$dir,') no-repeat 1% -173px;height:10px;border-bottom-left-radius:10px;-webkit-border-bottom-left-radius:10px;-moz-border-bottom-left-radius:10px;"></td>
				<td style="position:relative;border:0px;background: url(',$dir,') no-repeat 99% -173px;height:10px;border-bottom-right-radius:10px;-webkit-border-bottom-right-radius:10px;-moz-border-bottom-right-radius:10px;"></td>
			</tr>		
		</table>		
		<br /><br />
		<h4 style="text-align:center;">			
			<span style="float:right;">
				<input type="submit" value="'. $txt['spamblocker_submit']. '"'. (!empty($context['save_disabled']) ? ' disabled="disabled"' : ''). ' onclick="var confirm = confirmSubmit(); if (confirm) {this.form.submit();} else {uncheckAll(document.spamblocker); return false;}" />
			</span>
			<span style="float:right;font-size:9px;font-family:gadget;">
				',$txt['spamBlockerCheckAll'],'
				<input style="position:relative;top:2px;" type="checkbox" name="del" onclick="checkUncheckAll(this)" />&nbsp;&nbsp;
			</span>		
		</h4>			
		<span id="WhitelistPagePosition" style="line-height:0px;">&nbsp;</span>', $context['spamBlockerAdmin_pages'],'
		<select style="vertical-align:top;" name="spamBlockerIncrement" onchange="uncheckAll(document.spamblocker); this.form.submit();">';
	foreach ($selected as $select)
	{
		if ((int)$select == (int)$context['SB_increment'])
			echo '<option selected>',$select,'</option>';
		else
			echo '<option>', $select, '</option>';	
	}
	echo '
		</select>
		<input type="hidden" name="sc" value="'. $context['session_id']. '" />	
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />	
	</form>
	<script type="text/javascript"><!-- // --><![CDATA[
	function confirmation()
	{
		var xconfirmx = confirmSubmit();
		if (xconfirmx)
		{
			this.form.submit();
			return true;
		}
		else
		{
			uncheckAll(document.spamblocker);
			return false;
		}
	}				
	// ]]></script>';
}
?>
