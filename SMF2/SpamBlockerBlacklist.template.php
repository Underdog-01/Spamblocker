<?php
// Version: 1.0; spamblocker  

/*
/*	Main admin blacklist template file for the Spam Blocker Mod	
/*	c/o Underdog @ http://askusaquestion.net			
*/

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://askusaquestion.net	
 * Copyright 2013 Underdog@askusaquestion.net
 * This software package is distributed under the terms of its Freeware License
 * http://askusaquestion.net/index.php/page=spamblocker_license
*/

/* Spam Blocker blacklist display  */
function template_spamBlocker_blacklist_page()
{
	global $txt, $scripturl, $context, $settings, $boardurl;
	isAllowedTo('spamBlocker_settings');
	loadLanguage('SpamBlocker');
	$column = 1;	
	$rows = 0;
	$pageCount = 1;	
	$selected = array(5,10,25,50);	 
	$dir = $context['spamblocker_styles']['bg'];
	$color = $context['spamblocker_styles']['color'];
	$stylex = '';
	if ($settings['name'] == 'Core Theme')
		$stylex = 'right:1px;';
		
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
		<div class="cat_bar"><h4 class="catbg" style="text-align:center;">' . $txt['spamBlockerIpBlacklist'] . '</h4></div>
		<br /><br />	
		<table border="0" cellspacing="0" cellpadding="4" width="99%">		
			<tr style="font-size:x-small;" class="catbg3">				
				<td style="height:15px;border:0px;background: url(',$dir,') no-repeat 1% -160px;border-top-left-radius:10px;-webkit-border-top-left-radius:10px;-moz-border-top-left-radius:10px;">
					<span style="float:left;text-indent:1px;',$color,'">
						<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbBlacklistHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
							<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
						</a>
						' . $txt['spamBlockerBlacklist'] . '
					</span>					
				</td>
				<td style="height:15px;border:0px;background: url(',$dir,') no-repeat 99% -160px;border-top-right-radius:10px;-webkit-border-top-right-radius:10px;-moz-border-top-right-radius:10px;">
					<span class="alert" style="float:right;position:relative;right:10px;text-shadow:0 0 3px;">', $_SESSION['spamBlockerIP_Error'], '</span>
				</td>
			</tr>		
		</table>
		<table border="0" cellspacing="0" cellpadding="4" width="99%" class="windowbg">		
			<tr style="font-size:x-small;">
				<td class="windowbg" style="line-height:1px;" colspan="6">&nbsp;</td>				
			</tr>
			<tr class="titlebg" style="font-size:x-small;">
				<td class="windowbg" style="width:28%;border-bottom: thin solid;">
					<span style="position:relative;margin-left:1px;">',$txt['spamBlockerID'],'</span>
				</td>
				<td class="windowbg" style="width:5%;height:8%;line-height:20px;border-bottom: thin solid;">
					<span style="position:relative;right:5%;">',$txt['spamBlockerDelete'],'</span>
				</td>
				<td class="windowbg" style="position:relative;width:28%;border-left: thin solid;border-bottom: thin solid;">
					<span style="position:relative;margin-left:1px;">',$txt['spamBlockerID'],'</span>
				</td>
				<td class="windowbg" style="width:5%;height:8%;line-height:20px;border-bottom: thin solid;">
					<span style="position:relative;right:5%;">',$txt['spamBlockerDelete'],'</span>
				</td>
				<td class="windowbg" style="position:relative;',$stylex,'width:28%;border-left: thin solid;border-bottom: thin solid;">
					<span style="position:relative;margin-left:1px;">',$txt['spamBlockerID'],'</span>
				</td>
				<td class="windowbg" style="width:5%;height:8%;line-height:20px;border-bottom: thin solid;">
					<span style="position:relative;right:5%;">',$txt['spamBlockerDelete'],'</span>
				</td>			
			</tr>
		</table>
		<table border="0" cellspacing="0" cellpadding="7" width="99%" id="spamblocker_blacklist" class="windowbg">		
			<tr>								
				<td style="line-height:2px;position:absolute;font-size:x-small;" colspan="6">
					<span></span>										
				</td>			
			</tr>		
			<tr class="windowbg2" style="font-size:x-small;">';
			
	foreach ($context['spamblocker_list'] as $user => $data)
	{
		$user_link = $scripturl .'?action=profile;u=' . $data['id_member'];
		$link = $scripturl . '?action=admin;area=ban;sa=edit;bg='.$data['delete'];
		if ((int)$data['id_member'] == 0)
			$user_link = $link;
		$ipLow = trim(implode('.', array($data['ip_low1'], $data['ip_low2'], $data['ip_low3'], $data['ip_low4'])));
		$ipHigh = trim(implode('.', array($data['ip_high1'], $data['ip_high2'], $data['ip_high3'], $data['ip_high4'])));
		$date = $txt['spamBlocker_BlacklistBanned'] . '&nbsp;' . date('m/d/Y', $data['ban_time']);
		if ($data['expire_time'])
			$expire = $txt['spamBlocker_BlacklistExpires'] . '&nbsp;' . date('m/d/Y', $data['expire_time']);
		else
			$expire = $txt['spamBlocker_BlacklistExpires'] . '&nbsp;'. $txt['spamBlocker_BlacklistExpiresNA'];
		if ($column != 1)
			$style = 'border-left: thin solid;';
		else
			$style = '';	
				
		if ($ipLow == $ipHigh)				
			echo '			
				<td class="windowbg2" style="width:28%;height:8%;line-height:20px;padding-bottom:5px;'.$style.'">
					<span style="float:left;"><a href="'.$user_link.'" title="'.$data['name'].'">',$data['name'],'</a></span>					
					<span style="font-size:8px;position:relative;width:33%;float:right;">',$date,'</span>
					<br />					
					<span style="float:left;"><a href="'.$link.'" title="'.$data['name'].'">',$ipLow,'</a></span>
					<span style="font-size:8px;position:relative;width:33%;float:right;">',$expire,'</span>
				</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;padding-bottom:5px;">
					<input style="position:relative;left:15%;" class="windowbg2" type="checkbox" id="delete'.$rows.'" name="delete[]" value="'.$data['delete'].'" />
					<input type="hidden" id="id'.$rows.'" name="id_member[]" value="'.$data['id_member'].'" />
				</td>';
		else
			echo '			
				<td class="windowbg2" style="width:28%;height:8%;line-height:20px;padding-bottom:5px;'.$style.'">
					<span style="float:left;"><a href="'.$user_link.'" title="'.$data['name'].'">',$data['name'],'</a></span>					
					<span style="font-size:8px;position:relative;width:33%;float:right;">',$date,'</span>
					<br />
					<span style="float:left;"><a href="'.$link.'" title="'.$data['name'].'">',$ipLow,'</a></span>					
					<span style="font-size:8px;position:relative;width:33%;float:right;">',$expire,'</span>
				</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;padding-bottom:5px;">
					<input style="position:relative;left:15%;" class="windowbg2" type="checkbox" id="delete'.$rows.'" name="delete[]" value="'.$data['delete'].'" />
					<input type="hidden" id="id'.$rows.'" name="id_member[]" value="'.$data['id_member'].'" />
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
			<tr class="windowbg2" style="font-size:x-small;">';
			$column = 0;
		}
				
		$column++;	
		$rows++;
	}
	
	if ($rows == 0)
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;" colspan="6">
					<span class="windowbg"></span>					
				</td>';
				
	if ($column == 1)
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;visibility:hidden;" colspan="6">
					<span class="windowbg2"></span>
				</td>				
			</tr>';		
	elseif ($column == 2)
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;width:28%;position:relative;border-left: thin solid;">
					<span class="windowbg2"></span>
					<br />
					<span class="windowbg2"></span>
				</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;visibility:hidden;" class="windowbg2" type="checkbox" name="xfill[]" value="" disabled=true />
				</td>
				<td class="windowbg2" style="height:8%;line-height:20px;width:28%;position:relative;border-left: thin solid;">
					<span class="windowbg2"></span>
					<br />
					<span class="windowbg2"></span>
				</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;visibility:hidden;" class="windowbg2" type="checkbox" name="yfill[]" value="" disabled=true />
				</td>					
			</tr>';
	else
		echo '
				<td class="windowbg2" style="height:8%;line-height:20px;width:28%;border-left: thin solid;">
					<span class="windowbg2"></span>
					<br />
					<span class="windowbg2"></span>
				</td>
				<td class="windowbg2" style="position:relative;width:5%;height:8%;line-height:20px;">
					<input style="position:relative;left:15%;visibility:hidden;" class="windowbg2" type="checkbox" name="yfill[]" value="" disabled=true />
				</td>				
			</tr>';			
					 		
	echo '
		</table>	
		<table border="0" cellspacing="0" cellpadding="4" width="99%">		
			<tr class="catbg3">
				<td style="border:0px;background: url(',$dir,') no-repeat 10% -173px;height:15px;border-bottom-left-radius:15px;-webkit-border-bottom-left-radius:15px;-moz-border-bottom-left-radius:15px;">
					<span style="position:relative;float:left;top:-4px;left:3px;',$color,'">',$txt['spamBlocker_blockedToday'],'&nbsp;',$context['spamblocker_today'],'</span>
				</td>				
				<td style="border:0px;background: url(',$dir,') no-repeat 80% -173px;height:15px;text-align:center;">';
				
	if ((int)$context['spamblocker_blacklist_count'] > 0)
		echo '
					<span style="position:relative;top:-4px;',$color,'">
						',$txt['spamBlockerPhpEntities'],' ',(int)$context['spamBlocker_showResults'][0]+1,' - ',(int)$context['spamBlocker_showResults'][1],'
					</span>';	
	else
		echo '&nbsp;';
		
	echo '	
				</td>			
				<td style="border:0px;background: url(',$dir,') no-repeat 10% -173px;height:15px;border-bottom-right-radius:15px;-webkit-border-bottom-right-radius:15px;-moz-border-bottom-right-radius:15px;">			
					<span style="position:relative;float:right;top:-4px;right:3px;',$color,'">',$txt['spamBlocker_blockedTotal'],'&nbsp;',$context['spamblocker_blacklist_count'],'</span>
				</td>
			</tr>		
		</table>';		
	
	/* PHP pagination - max 7 visible integers and 6 preiods (all links) - current page encircled with square brackets */
	if ((int)$context['spamBlocker_Pages'] > 1)
	{
		echo '		
		<span style="text-align:center;position:relative;width:99%;display:inline-block;">
			',$txt['spamBlockerPhpPage'],'<br />';
		
		while ($pageCount < (int)$context['spamBlocker_Pages']+1)
		{
			$current_page = (int)$context['SB_blacklist_page']+1;
			$total = (int)$context['spamBlocker_Pages'];
			
			if ($pageCount == 1 || $pageCount == $total || $pageCount == $current_page || $pageCount == $current_page+1 ||
			    $pageCount == $current_page+2 || $pageCount == $current_page-1 || $pageCount == $current_page-2)
			{				
				if ((int)$pageCount == (int)$context['SB_blacklist_page']+1)
					echo '<a onclick="this.href=\'javascript: void(0)\';" onmouseout="changeColorBack('.$pageCount.')" onmouseover="changeColor('.$pageCount.')" id="link'.$pageCount.'" style="color:blue;text-decoration:none;" href="',$scripturl,'?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;SB_blacklist_sort=',$context['SB_blacklist_sort'],';blacklist_page=',$pageCount,'">[', $pageCount, ']</a> ';
				else
					echo '<a onmouseout="changeColorBack('.$pageCount.')" onmouseover="changeColor('.$pageCount.')" id="link'.$pageCount.'" style="color:blue;text-decoration:none;" href="',$scripturl,'?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;SB_blacklist_sort=',$context['SB_blacklist_sort'],';blacklist_page=',$pageCount,'">', $pageCount, '</a> ';
			}
			elseif ($pageCount < $current_page-2 && $pageCount > $current_page-6)
				echo '<a onmouseout="changeColorBack('.$pageCount.')" onmouseover="changeColor('.$pageCount.')" id="link'.$pageCount.'" style="color:blue;text-decoration:none;" href="',$scripturl,'?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;SB_blacklist_sort=',$context['SB_blacklist_sort'],';blacklist_page=',$pageCount,'">.</a> ';
			elseif ($pageCount > $current_page+2 && $pageCount < $current_page+6)
				echo '<a onmouseout="changeColorBack('.$pageCount.')" onmouseover="changeColor('.$pageCount.')" id="link'.$pageCount.'" style="color:blue;text-decoration:none;" href="',$scripturl,'?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;SB_blacklist_sort=',$context['SB_blacklist_sort'],';blacklist_page=',$pageCount,'">.</a> ';
				
			$pageCount++;
		}
		
		echo '
		</span>';		
		
	}
	echo '	
		<br /><br />
		<h4 style="text-align:center;">			
			<span style="float:right;">
				<input type="submit" value="'. $txt['spamblocker_submit']. '"'. (!empty($context['save_disabled']) ? ' disabled="disabled"' : ''). ' onclick="var confirm = confirmSubmit(); if (confirm) {this.form.submit();} else {uncheckAll(document.spamblocker); return false;}" />
			</span>
			<span style="float:right;font-size:9px;font-family:gadget;">
				',$txt['spamBlockerCheckAll'],'
				<input style="position:relative;top:2px;" type="checkbox" name="del" onclick="checkUncheckAll(this)" />&nbsp;&nbsp;
			</span>	
			<span style="float:right;font-size:9px;font-family:gadget;">				
				',$txt['spamBlockerDelMembers'],'
				<input style="position:relative;top:2px;" type="checkbox" name="delete_member[]" value="1" checked="checked" />&nbsp;&nbsp;
			</span>	
		</h4>			
		<span id="BlacklistPagePosition" style="line-height:0px;">&nbsp;</span>', $context['spamBlockerAdmin_pages'],'
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
		<span style="display:inline-block;position:relative;vertical-align:top;text-indent:20px;">
			<a style="text-decoration:none;" href="'.$scripturl.'?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;SB_blacklist_sort=ip;blacklist_page=',(int)$context['SB_blacklist_page']+1,'">
				<img alt="" onClick="uncheckAll(document.spamblocker);" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-sort_ip.gif" title="'.$txt['spamBlockerSortIP'].'" class="windowbg" style="vertical-align:top;width:15px;height:15px;border:0px;" />
			</a>
			<img alt="" style="height:15px;width:1px;border:0px;" src="'.$settings['default_theme_url'].'/images/admin/spamblocker_vertical_bar-x.gif" />
			<a style="text-decoration:none;" href="'.$scripturl.'?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;SB_blacklist_sort=date;blacklist_page=',(int)$context['SB_blacklist_page']+1,'">
				<img alt="" onClick="uncheckAll(document.spamblocker);" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-sort_date.jpg" title="'.$txt['spamBlockerSortDate'].'" class="windowbg" style="vertical-align:top;width:15px;height:15px;border:0px;" />
			</a>
			<img alt="" style="height:15px;width:1px;border:0px;" src="'.$settings['default_theme_url'].'/images/admin/spamblocker_vertical_bar-x.gif" />
			<a style="text-decoration:none;" href="'.$scripturl.'?action=admin;area=spamBlocker;sa=spamBlockerBlacklist;SB_blacklist_sort=name;blacklist_page=',(int)$context['SB_blacklist_page']+1,'">
				<img alt="" onClick="uncheckAll(document.spamblocker);" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-sort_user.png" title="'.$txt['spamBlockerSortUser'].'" class="windowbg" style="vertical-align:top;width:15px;height:15px;border:0px;" />
			</a>			
		</span>
		<input type="hidden" name="sc" value="'. $context['session_id']. '" />
		<input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
		<input type="hidden" name="blacklist_page" value="',(int)$context['SB_blacklist_page']+1,'" />
		<input type="hidden" name="SB_blacklist_sort" value="',$context['SB_blacklist_sort'],'" />
	</form>
	<script type="text/javascript"><!-- // --><![CDATA[
		function changeColor(s)
		{
			for (var i=1; i<=3; i++) 
				document.getElementById("link"+i).style.color = i==s ? "red" : "blue";
		}
		function changeColorBack(s)
		{
			for (var i=1; i<=3; i++) 
				document.getElementById("link"+i).style.color = i==s ? "blue" : "blue";
		}		
	// ]]></script>';
}
?>