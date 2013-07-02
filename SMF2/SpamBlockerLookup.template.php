<?php
// Version: 1.0; spamblocker 

/*
 *	Main admin lookup template file for the Spam Blocker Mod		
 *	c/o Underdog @ http://webdevelop.comli.com			
*/

/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://webdevelop.comli.com	
 * Copyright 2013 underdog@webdevelop.comli.com
 * This software package is distributed under the terms of its Freeware License
 * http://webdevelop.comli.com/index.php/page=spamblocker_license
*/

/* Spam Blocker ip/email lookup display  */
function template_spamBlocker_lookup_page()
{
	global $context, $txt, $settings, $scripturl;
	
	echo '
<form action="',$context['post_url'],'" method="post" accept-charset="'. $context['character_set']. '" name="spamblocker_lookup">
	<div class="cat_bar">
		<h4 class="catbg" style="text-align:center;">',$txt['spamBlocker_LookupTitle'],'</h4>
	</div>
	<br /><br />
	<div style="display:box;border-radius:8px 8px 0px 0px;width:100%;" class="titlebg">
		<span class="titlebg" style="font-size:small;position:relative;border-radius:8px 0px 0px 0px;padding-left:5px;">
			',$txt['spamBlocker_LookupInquiry'],'
		</span>
		<span class="titlebg" style="font-size:small;position:relative;border-radius:0px 8px 0px 0px;float:right;padding-right:5px;text-shadow:0 0 3px orange;">',
			$context['spamBlockerIP_Error'], '&nbsp;
		</span>
	</div>	
	<table border="0" cellspacing="0" cellpadding="4" width="100%">			
		<tr>
			<td class="windowbg" style="text-align:left;">
				<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbLookupIpHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
					<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
				</a>
				',$txt['spamBlocker_IpInput'],'
			</td>
			<td class="windowbg" style="text-align:left;line-height:20px;">
				<input type="text" id="ip" name="input_ip[]" size="50" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['input_ip'].'" />
			</td>
		</tr>
		<tr>
			<td class="windowbg" colspan="2"><br /></td>
		</tr>
		<tr>
			<td class="windowbg" style="text-align:left;">
				<a href="',$scripturl,'?action=helpadmin;help=spamBlocker_sbLookupEmailHelp" onclick="return reqWin(this.href);" style="text-decoration:none;">
					<img style="vertical-align:middle;position:relative;bottom:1px;width:12px;height:12px;" src="'.$settings['default_theme_url'].'/images/admin/spamBlocker-help.gif" alt="?" />
				</a>
				',$txt['spamBlocker_EmailInput'],'
			</td>
			<td class="windowbg" style="text-align:left;line-height:20px;">
				<input type="text" id="email_check" name="input_email[]" size="50" onkeydown="if (event.keyCode == 13) { this.form.submit(); return false; }" value="'.$context['spamblocker']['input_email'].'" />
			</td>
		</tr>					
	</table>
	<div style="display:box;border-radius:0px 0px 8px 8px;padding-left:10px;padding-right:10px;" class="windowbg">&nbsp;</div>
	<span style="visibility:hidden;"><input type="submit" value="Submit"'. (!empty($context['save_disabled']) ? ' disabled="disabled"' : ''). ' /></span>
	<input type="hidden" name="sc" value="'. $context['session_id']. '" />
</form>';

echo '
<div style="display:box;border-radius:8px 8px 0px 0px;text-align:center;" class="windowbg2">Results</div>	
<div style="display:box;" class="windowbg">
	<span style="display:inherit;padding-left:5px;" class="windowbg">';

echo $context['spamBlocker']['ip_message'], '<br /><br />', $context['spamBlocker']['email_message'];

echo '
	</span>
</div>
<div style="display:box;border-radius:0px 0px 8px 8px;text-align:center;padding-left:10px;padding-right:10px;" class="windowbg">&nbsp;</div>';
}
?>
