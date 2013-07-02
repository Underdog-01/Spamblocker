<?php
// SMF Version: 2.0.4; Spam Blocker
/*
 * Spam Blocker was developed for SMF forums c/o Underdog @ http://webdevelop.comli.com
 * Copyright 2013 underdog@webdevelop.comli.com
 * This software package is distributed under the terms of its Freeware License
 * http://webdevelop.comli.com/index.php/page=spamblocker_license
*/
global $helptxt;

/* Spam Blocker general text variables */
$txt['spamBlocker_tabtitle'] = 'Spam Blocker';
$txt['spamBlocker_tabtitle6'] = 'Spam Blocker';
$txt['spamBlocker_license'] = 'License';
$txt['spamBlocker_guide'] = 'Guide';
$txt['spamBlocker'] = 'Spam Blocker';
$txt['spamBlockerSettings'] = 'Spam Blocker Settings';
$txt['spamBlockerWhitelist'] = 'IP Whitelist';
$txt['spamBlockerIpWhitelist'] = 'Spam Blocker IP Whitelist';
$txt['spamBlockerIpWhitelistAdd'] = 'Add IPv4 to Whitelist (e.g. 192.168.10-20.*)';
$txt['spamBlockerBlacklist'] = 'IP Blacklist';
$txt['spamBlockerLookup'] = 'IP/Email Lookup';
$txt['spamBlocker_lookup'] = 'Spam Blocker IP/Email Lookup';
$txt['spamBlockerIpBlacklist'] = 'Spam Blocker IP Blacklist';
$txt['spamBlocker_enabled'] = 'Enabled';
$txt['spamBlocker_disabled'] = 'Disabled';
$txt['spamBlocker_enable'] = 'Enable/Disable';
$txt['spamBlocker_settings'] = 'Configuration';
$txt['spamBlocker_general'] = 'Spam Blocker Configuration';
$txt['spamBlocker_user_message'] = 'Registration Denied Message';
$txt['spamBlocker_error_message'] = 'Errorlog Message';
$txt['spamBlocker_enableMod'] = 'Enable/Disable Modification';
$txt['spamBlocker_pass'] = '3+ Months Exemption (where applicable)';
$txt['spamBlocker_expired'] = 'Delete Expired Blacklist IP\'s';
$txt['spamBlocker_reset'] = 'Auto Reset Expired Blacklist IP\'s';
$txt['spamBlocker_emend'] = 'Optimize Blacklist';
$txt['spamBlocker_error'] = 'Log General Spam Blocker Messages';
$txt['spamBlocker_smf_error'] = 'Log General SMF Ban Messages';
$txt['spamBlocker_banned'] = 'Ban Flagged IP\'s';
$txt['spamblocker_submit'] = 'Submit';
$txt['spamBlockerBan'] = 'SpamBlocker IP Ban';
$txt['spamBlockerSpam'] = 'spam';
$txt['spamBlockerHello'] = 'Hello, my name is ';
$txt['spamBlocker_confirm'] = 'Do you confirm this action?';
$txt['spamBlockerIP_BlacklistRemove'] = 'IP\'s were removed from your blacklist & ban list';
$txt['spamBlockerIP_BlacklistNotExpired'] = 'No expired ban IP\'s were found';
$txt['spamBlockerIP_BlacklistEmend'] = 'Blacklist has been optimized/emended';
$txt['spamBlockerClearBlacklistMsg'] = 'Blacklist has been erased';
$txt['spamBlockerIP_BlacklistExpired'] = 'Expired ban IP\'s were removed from your blacklist & ban list';
$txt['spamBlocker_banlink'] = 'Ban List View';
$txt['spamBlockerCheckAll'] = 'check all';
$txt['spamBlockerPage'] = 'Browse';
$txt['spamBlockerPhpPage'] = 'Page';
$txt['spamBlockerPhpEntities'] = 'Entities';
$txt['spamBlockerOf'] = 'of';
$txt['spamBlockerDelMembers'] = 'delete subjoined member id\'s';
$txt['spamBlockerSortIP'] = 'Sort list by IP';
$txt['spamBlockerSortDate'] = 'Sort list by date';
$txt['spamBlockerSortUser'] = 'Sort list by user name';
$txt['spamBlockerEmail'] = 'Stop Forum Spam Email Analysis';
$txt['spamBlocker_enableSFS'] = 'Stop Forum Spam IP Analysis';
$txt['spamBlocker_enableAkismet'] = 'Akismet Email Analysis';
$txt['spamBlocker_enableHoneypot'] = 'Project Honeypot IP Analysis';
$txt['spamBlocker_enableSpamhaus'] = 'Spamhaus IP Analysis';
$txt['spamBlocker_blockedToday'] = 'Blocked today:';
$txt['spamBlocker_blockedTotal'] = 'Total Blocked:';
$txt['spamBlocker_BlacklistBanned'] = 'Banned:';
$txt['spamBlocker_BlacklistExpires'] = 'Expires:';
$txt['spamBlocker_BlacklistExpiresNA'] = 'N/A';
$txt['spamBlocker_ViewBan'] = 'View ban data for';
$txt['spamBlocker_ViewProfile'] = 'View Profile of';
$txt['spamBlockerID'] = 'Identification';
$txt['spamBlockerDelete'] = 'Delete';
$txt['spamBlockerOff'] = 'off';
$txt['spamBlockerLogs'] = 'Logs';
$txt['spamBlockerPostFiltering'] = 'Post Filtering';
$txt['spamBlockerRegFiltering'] = 'Registration Filtering';
$txt['spamBlockerMainConfig'] = 'Main Configuration';
$txt['spamBlockerMessaging'] = 'Messaging';
$txt['spamBlockerMaintenance'] = 'Maintenance';
$txt['spamBlockerBanConfig'] = 'Ban Configuration';
$txt['spamBlocker_hide_members'] = 'Display blacklisted members in the member list';
$txt['spamBlocker_delete_members'] = 'Permanently delete flagged member ID\'s at registration';
$txt['spamBlockerAkismetKey'] = '<a href="https://akismet.com/signup/">Akismet</a> API Key';
$txt['spamBlockerKeySFS'] = '<a href="http://www.stopforumspam.com/forum/">Stop Forum Spam</a> API Key';
$txt['spamBlockerAkismetKeyCheckValid'] = 'Valid Akismet Key'; 
$txt['spamBlocker_enableAkismetPost'] = 'Akismet Post Filtering';
$txt['spamBlocker_enablePostSFS'] = 'Stop Forum Spam Post Reporting';
$txt['spamBlocker_enablePostDisplay'] = 'Spam Reporting Link For Display Template';
$txt['spamBlocker_enableConnErrs'] = 'Log API Connection Errors';
$txt['spamBlockerAkismetCount'] = 'Post Limit Integer For Filtering (0 for no limit)';
$txt['spamBlocker_RemoveDB'] = 'Would you prefer to remove all Spam Blocker tables and columns from the database?';
$txt['spamBlocker_EmailInput'] = 'Enter Email Address:';
$txt['spamBlocker_IpInput'] = 'Enter IPv4 Address:';
$txt['spamBlocker_LookupTitle'] = 'IPv4 and Email Spam/Security Check';
$txt['spamBlocker_LookupInquiry'] = 'Please enter a valid public IPv4 address and/or Email address to run a spam check';
$txt['spamBlocker_IpLookupMessageNeg'] = 'IP address <b>%#&$@</b> is NOT listed in SFS or Honeypot spam blacklist - [PASSED]';
$txt['spamBlocker_IpLookupMessagePos'] = 'IP address <b>%#&$@</b> is listed in SFS or Honeypot spam blacklist - [FAILED]';
$txt['spamBlocker_EmailLookupMessageNeg'] = 'Email: <b>%#&$@</b> is NOT listed in SFS or Akismet spam blacklist - [PASSED]';
$txt['spamBlocker_EmailLookupMessagePos'] = 'Email: <b>%#&$@</b> is listed in SFS or Akismet spam blacklist - [FAILED]';
$txt['spamBlockerHoneypotConfig'] = 'Honeypot API Configuration';
$txt['spamBlockerHoneypotKey'] = '<a href="https://www.projecthoneypot.org/">Honeypot</a> API Key';
$txt['spamBlockerHoneypotThreat'] = 'Ignore Threat Score (0-255)';
$txt['spamBlockerHoneypotType'] = 'Ignore Visitor type (0-255 Separated by comma)';
$txt['spamBlocker_delBlacklist']= 'Delete the entire blacklist and related data';
$txt['spamBlocker_delBlacklistWarningX'] = 'WARNING! Anything related to this modification\'s blacklist will be deleted from your database after confirming this option.';
$txt['spamBlocker_delBlacklistWarningY'] = 'This maintenance function will clear the entire blacklist of data, delete any related ban data and also wipe any subjoined member id data from your database.';
$txt['spamBlocker_default'] = 'Default Configuration';
$txt['spamBlocker_defaultConfig'] = 'Default Configuration Completed';
$txt['spamBlocker_enablePostBan'] = 'Ban Entities From Reported Topics/Posts';
$txt['spamBlocker_defaultIP'] = '1.0.0.0';
$txt['spamBlocker_defaultEmail'] = 'helloman@gmail.com';
$txt['spamBlocker_enableReportErrs'] = 'Log Reported Topics/Posts';
$txt['spamBlockerRedirectUrl'] = 'URL redirection path';
$txt['spamBlocker_enableRedirect'] = 'Redirect Spam Entities';
$txt['spamBlocker_enablePostFilter'] = 'Rule Based Filtering';
$txt['spamBlocker_configPostFilter'] = 'Rule Based Filtering Configuration';
$txt['spamBlockerLinksCount'] = 'Maximum Allowed Links';
$txt['spamBlockerImagesCount'] = 'Maximum Allowed Images';
$txt['spamBlocker_wordsFilter'] = 'Disallowed Text';
$txt['spamBlockerCharsCount'] = 'Minimum/Maximum Character Input';

/* Spam Blocker Default Filtered Words */
$txt['spamBlocker_textFilter'] = 'baccarrat, blackjack, casinos, gambling, onlinegambling, penis, poker, pussy, roulette, shemale, slot, texas, holdem, viagra';

/* Spam Blocker Reporting Tab */
$txt['spamBlockerSpamTitle'] = 'Report post as spam';
$txt['spamBlockerSpamText'] = 'Report Spam';
$txt['spamBlockerSpamDetail'] = 'This post has not been reported to Akismet or SFS. Are you opting to report this post as spam?';
$txt['spamBlockerSafeTitle'] = 'Report post as safe';
$txt['spamBlockerSafeText'] = 'Report Safe';
$txt['spamBlockerSafeDetail'] = 'This post has been flagged as spam by Akismet. Are you opting to report this post as safe?';
$txt['spamBlockerNoSpamTitle'] = 'Already reported as spam';
$txt['spamBlockerNoSpamText'] = 'Reported Spam';
$txt['spamBlockerNoSpamDetail'] = 'This post has already been reported as spam';
$txt['spamBlockerNoSafeTitle'] = 'Already reported as safe';
$txt['spamBlockerNoSafeText'] = 'Reported Safe';
$txt['spamBlockerNoSafeDetail'] = 'This post has already been reported as safe';
$txt['spamBlockerReportMsg'] = 'Message Reporting';
$txt['spamBlockerReport'] = 'Spam Post Logged';
$txt['spamBlockerSpamDelete'] = '\r\nThis action can not be reversed and should only be performed when you are certain that the post contains spam content.';

/* Spam Blocker permission language variables */ 
$txt['permissiongroup_simple_spamBlocker_perms'] = 'Spam Blocker';
$txt['permissiongroup_spamBlocker_perms'] = 'Spam Blocker';
$txt['permissionname_spamBlocker_settings'] = 'Access Spam Blocker Admin Configuration';
$txt['permissionhelp_spamBlocker_settings'] = 'This will allow the opted usergroup to moderate Spam Blocker Configuration';
$txt['permissionname_spamBlocker_postCount'] = 'Enable Post Filtering';
$txt['permissionhelp_spamBlocker_postCount'] = 'This will force post filtering for this membergroup. The amount of initial posts are set in Spam Blocker Configuration.';

/* Spam Blocker error messages */
$txt['cannot_spamBlocker_settings'] = 'You do not have permission to use this option.';
$txt['spamBlocker_ErrorMessage'] = 'You do not have permission to use this option.';
$txt['spamBlocker_ErrorMessageDB'] = 'Spam Blocker Database Error.';
$txt['cannot_spamBlocker_ErrorMessageDB'] = 'Spam Blocker Database Error.';
$txt['spamBlockerIP_Error'] = 'Error: IPv4 address is invalid';  
$txt['spamBlockerIP_BanError'] = 'Error: IPv4 address currently in ban list'; 
$txt['spamBlockerIP_ExistsError'] = 'Error: IPv4 address currently in whitelist'; 
$txt['spamBlockerSource_NoneError'] = 'Error: At least one type of analysis must be selected - default enabled';
$txt['spamBlockerAkismetKeyCheckInvalid'] = 'Warning - Invalid Akismet Key';
$txt['spamBlockerIP_InquiryError'] = 'The queried IPv4 address is invalid. - [ERROR!]';
$txt['spamBlockerEmail_InquiryError'] = 'The queried Email address is invalid. - [ERROR!]';
$txt['spamBlockerIP_InquiryErrorShort'] = 'Invalid IPv4 Address';
$txt['spamBlockerEmail_InquiryErrorShort'] = 'Invalid Email Address';
$txt['spamBlockerEmail_InquiryErrorAndShort'] = 'and Invalid Email Address';
$txt['spamBlocker_akismetError'] = 'Spam Blocker - Akismet connection failure [Email]';
$txt['spamBlocker_sfsErrorEmail'] = 'Spam Blocker - SFS connection failure [Email]';
$txt['spamBlocker_sfsErrorIp'] = 'Spam Blocker - SFS connection failure [IP]';
$txt['spamBlocker_sfsErrorIpEmail'] = 'Spam Blocker - SFS connection failure [IP/Email]';
$txt['spamBlocker_honeypotError'] = 'Spam Blocker - Honeypot connection failure [IP]';
$txt['spamBlocker_spamhausError'] = 'Spam Blocker - Spamhaus connection failure [IP]';
$txt['spamBlocker_sfsErrorLimit'] = 'Spam Blocker - SFS rate limit exceeded';
$txt['spamBlocker_registerError'] = 'Spam Blocker - Key not found for ban_groups table';
$txt['spamBlocker_reportingError'] = 'Spam Blocker Post Reporting - Key not found for ban_groups table';
$txt['error_spamBlocker_postText'] = 'Some text you entered is not permitted for new users';
$txt['error_spamBlocker_postLinks'] = 'New users are not permitted to post that amount of links';
$txt['error_spamBlocker_postImages'] = 'New users are not permitted to post that amount of images';
$txt['error_spamBlocker_postChars'] = 'New users are limited to posts containing no more than %#&$@ characters';
$txt['error_spamBlocker_postLowChars'] = 'New users are limited to posts containing no less than %#&$@ characters';
$txt['spamBlockerWordCount'] = 'Forbidden Text Occurrence Threshold';

/* Spam Blocker Help Variables */
$helptxt['spamBlocker_enableModHelp'] = 'This allows you to enable or disable the entire Spam Blocker modification.';
$helptxt['spamBlocker_sbPassHelp'] = 'Enabling this option will ignore any spam reports beyond 90 days.';
$helptxt['spamBlocker_sbResetHelp'] = 'Enabling this option will automatically omit expired ban/blacklist entities at time of login.<br />This check is done at time of login, registration or every 3-6 hours.<br /><br />Login and registration are limited to one query matching the ip, email or username.<br /><br />The 3-6 hour interval is limited to 500 database queries every 3 hours. Both the 3-6 hour expired check interval and registration expired check are always enabled while the login expired check is dependant on this setting.';
$helptxt['spamBlocker_sbBanHelp'] = 'Enabling this feature will ensure both the email and IP are banned and blacklisted.<br /><br />Disabling this feature will not allow the flagged IP/Email to register but will not add any entries to the ban/blacklist.';
$helptxt['spamBlocker_sbHideHelp'] = 'This option only applies when <strong>Permanently delete flagged member ID\'s at registration</strong> is disabled.<br /><br />Enabling this option will allow you to view blacklisted members in the member list.<br /><br />Disabling this option will not include blacklisted members in your member list.';
$helptxt['spamBlocker_sbDeleteHelp'] = 'Enabling this option will ensure entities flagged as spam are not added as members. It will also disable restrictions for login & posting as they are not necessary when this is enabled.<br /><br />Disabling this option will add those spam entities as members while still banning and blacklisting them.';
$helptxt['spamBlocker_sbAkismetEnableHelp'] = 'An Akismet API key is required to use this feature.<br /><br />Enabling this option will filter a users email through the Akismet anti-spam database at time of registration.<br />Emails flagged as spam will be banned/blacklisted.';
$helptxt['spamBlocker_sbSFSEnableEmailHelp'] = 'Enabling this option will filter a users email through the Stop Forum Spam anti-spam database at time of registration.<br />Emails flagged as spam will be banned/blacklisted.';
$helptxt['spamBlocker_sbSFSEnableIpHelp'] = 'Enabling this option will filter a users IP through the Stop Forum Spam anti-spam database at time of registration.<br />IP\'s flagged as spam will be banned/blacklisted.';
$helptxt['spamBlocker_sbHoneypotEnableIpHelp'] = 'A Honeypot API key is required to use this feature.<br /><br />Enabling this option will filter a users IP through the Honeypot anti-spam database at time of registration.<br />IP\'s flagged as spam will be banned/blacklisted.';
$helptxt['spamBlocker_sbSpamhausEnableIpHelp'] = 'Enabling this option will filter a users IP through the Spamhaus (xbl-sbl) anti-spam database during the registration process.<br />IP\'s flagged as spam will be banned/blacklisted.<br /><br />Note: The 3 month (90 day) exemption does not apply to their database resource.';
$helptxt['spamBlocker_sbHoneypotThreatHelp'] = 'This setting applies when Honeypot IP Analysis is enabled.<br /><br />Any threat score below the value entered here will be ignored at time of registration.<br /><br />See the Spam Blocker Guide for more details.';
$helptxt['spamBlocker_sbHoneypotTypeHelp'] = 'This setting applies when Honeypot IP Analysis is enabled.<br /><br />The integer value(s) entered here will be ignored at time of registration.<br />Multiple values can be entered and must be separated by commas.<br /><br />See the Spam Blocker Guide to view the chart with more details.';
$helptxt['spamBlocker_sbHoneypotKeyHelp'] = 'This setting applies when Honeypot IP Analysis is enabled.<br /><br />A key is required to use the Honeypot blacklist API.<br />To acquire a key, you must become a member on the Honeypot website and apply for it.<br /><br />At this time, Project Honeypot only provides API Key validity on their website when you are logged in to your account. You must manually check if the key you entered is valid.';
$helptxt['spamBlocker_sbAkismetPostHelp'] = 'An Akismet API key is required and <strong>Post Moderation</strong> must be enabled to use this feature.<br /><br />Enabling this option will allow you to filter posts and/or topics through the Akismet anti-spam database.<br /><br />The users name, email and the text of the posts/topics will be filtered for possible spam.<br /><br />Any posts/topics flagged as spam will be shown in the unapproved posts of your moderation center.';
$helptxt['spamBlocker_PostSFSHelp'] = 'A Stop Forum Spam API key is required and <strong>Post Moderation</strong> must be enabled to use this feature.<br /><br />Enabling this option will allow you to report spam posts/topics to the Stop Forum Spam database.<br /><br />This option only applies when you pool new users into a usergroup and set their posts/topics as unapproved. These posts/topics will appear in your Moderation Center pending approval where you have the option of reporting them as spam to the Stop Forum Spam database from an available link.<br /><br />Please view the wiki for <a target="_blank" href="http://wiki.simplemachines.org/smf/Permissions" title="Post Moderation">Post Moderation</a> for more details on setting up your user/membergroup permissions appropriately.';
$helptxt['spamBlocker_KeySFSHelp'] = 'An API key is required to report spam topics/posts to the Stop Forum Spam database.<br />To acquire a key, you must become a member of the Stop Forum Spam (forum) website to apply for it.';
$helptxt['spamBlocker_sbAkismetLimitHelp'] = 'This allows you to set a limit on the number of initial user\'s posts to be filtered and/or possibly reported.';
$helptxt['spamBlocker_sbAkismetKeyHelp'] = 'A key is required to use the Akismet Email blacklist API.<br />To acquire a key, you must become a member on both the Wordpress and Akismet websites to apply for it.';
$helptxt['spamBlocker_sbErrHelp'] = 'Enabling this option will report every Spam Blocker banned entity at the time of registration.';
$helptxt['spamBlocker_sbSmfErrHelp'] = 'Enabling this option will report every SMF banned entity when attempting to access your forum which is the default behavior.<br /><br />Disabling this option will not allow the logging of banned entities attempting to access your forum. This applies to any banned entity and is not restricted to ones that were added by this modification.';
$helptxt['spamBlocker_sbConnErrsHelp'] = 'Enabling this option will log any connection errors that occured while attempting to access any anti-spam source databases.';
$helptxt['spamBlocker_sbUserMsgHelp'] = 'The text entered here will be displayed to the user when their access is denied due to being blacklisted.';
$helptxt['spamBlocker_sbLogMsgHelp'] = 'This feature only applies when Spam Blocker error messages is enabled.<br ><br >The text entered here will be displayed in your error log when a banned entity attempts to access your forum.';
$helptxt['spamBlocker_sbExpiredHelp'] = 'This option will manually delete any expired ban/blacklist entities. The related member ID\'s will be deleted if that secondary option is also checked.';
$helptxt['spamBlocker_sbEmendHelp'] = 'This option will cross reference the Spam Blocker blacklist to your ban entities. Any data in the blacklist that does not have a corresponding entry in your ban list will be omitted.';
$helptxt['spamBlocker_sbSetExpireHelp'] = 'Set the time interval (in days) for how long your banned entities will transpire.<br /><br />Expired ban/blacklist entities including user id\'s will automatically be omitted every 6 hours. Expired entities are also omitted at the time of registration if that feature was opted.';
$helptxt['spamBlocker_sbRestrictHelp'] = 'Choose how you will restrict the banned entites for access to your forum. A full ban will disallow a banned entity from browsing your forum.<br />Other options will allow the entiies to browse your forum but limit their access accordingly.';
$helptxt['spamBlocker_sbWhitelistHelp'] = 'Enter an IP or IP range to add to your whitelist in the input provided at the top of this template.<br /><br />These will not be subject to anti-spam filtering during the registration process.<br /><br />The javascript pagination is located at the bottom left of this template and will allow easy browsing of the available entities of the current page. The inner arrows control the list in increments of one. The outer arrows will control the list in increments of the number selected within the drop-down located to the right of these arrows.<br /><br />The check-all will enable/disable all deletion checkboxes from the entire whitelist.<br /><br />The submit button will commence all opted deletions where a confirmation is required prior to performing the task(s)';
$helptxt['spamBlocker_sbBlacklistHelp'] = 'These are Ipv4 addresses that were added from user registrations and were flagged as spam from one of the opted anti-spam data sources. There is a checkbox available which
 will allow deletion of each IP from the blacklist and ban list. The subjoined user id will also be deleted from the database if the option is checked in the bottom right corner.<br /><br />Located at the bottom of the display are the amount of flagged entities that were 
 blacklisted from the current day, the blacklisted entities being displayed on the current page and also the overall total amount in your blacklist.<br /><br >This blacklist uses both PHP pagination and javascript pagination.<br /><br />The PHP pagination is displayed in the bottom center of this template and will allow viewing a maximum of 1000 blacklist entities per page.<br /><br />The javascript pagination is located at the bottom left of this template and will allow easy browsing of the available entities of the current PHP page. The inner arrows control the list in increments of one. The outer arrows will control the list in increments of the number
 selected within the drop-down located to the right of these arrows.<br /><br />There are also three buttons located here that will allow ordering of the list by IP, date or name.<br /><br />The checkbox titled delete subjoined member id\'s will delete the user id associated with 
 the IP address(es) being deleted.<br /><br />The check-all will enable/disable all deletion checkboxes from the current (PHP) page of the blacklist.<br /><br />The submit button will commence all opted deletions where a
 confirmation is required prior to performing the task(s)';
$helptxt['spamBlocker_sbLookupIpHelp'] = 'Enter a valid Ipv4 address to run a manual check.<br /><br />Results will be posted below the inputs.';
$helptxt['spamBlocker_sbLookupEmailHelp'] = 'Enter a valid Email address to run a manual check.<br /><br />Results will be posted below the inputs.';
$helptxt['spamBlocker_sbDelBlacklistHelp'] = '<span class="alert">WARNING! Anything related to this modification\'s blacklist will be deleted from your database after confirming this option.</span><br /><br />This maintenance function will clear the entire blacklist of data, delete any related ban data and also wipe any subjoined member id data from your database.';
$helptxt['spamBlocker_PostDisplayHelp'] = 'Akismet post filtering or SFS post reporting must be enabled to use this feature.<br /><br />Enabling this will add a link to your post/topic display template that will allow you to report spam to Akismet and/or Stop Forum Spam.<br /><br />The Moderation Center should be enabled to allow this feature to function properly.';
$helptxt['spamBlocker_DefaultHelp'] = 'This option will reset all your configuration settings to their default recommended values. These settings will reflect whether or not you have entered API keys for the various anti-spam resources. If you do not have an API key entered for a source that requires one, that source will be disabled.';
$helptxt['spamBlocker_PostBanHelp'] = 'Enabling this option will automatically ban entities that have been reported as posting spam within topics or posts.<br /><br />This applies to both Akismet topic/post spam filtering and manually reported topics/posts.';
$helptxt['spamBlocker_sbReportErrsHelp'] = 'Enabling this option will log any topic/post that was reported as spam.';
$helptxt['spamBlocker_sbRedirectUrlHelp'] = 'Having the registration redirect option enabled is required for this setting to take effect.<br /><br />All spam entities will be redirected to this url after attempting to register.<br /><br />You are required to enter a full valid url path else this setting will be ignored.';
$helptxt['spamBlocker_sbEnableRedirectHelp'] = 'Enabling this option will allow you to redirect entities flagged as spam to the url that is entered into the input directly below.<br /><br />Disabling this option will redirect spam entities to an error message.<br /><br />This option only applies during registration.';
$helptxt['spamBlocker_sbPostFilterEnableHelp'] = 'Enabling this option will allow you to filter a users initial posts for the maximum amount of links, maximum amount of bbc images,  minimum/maximum amount of characters and to disallow occurrences of specific detected text.';
$helptxt['spamBlocker_sbLinksCountHelp'] = 'This is the amount of links that are allowed for filtered topics/posts.<br /><br />If the user attempts to post more than this amount of links, they will receive an error message and will not be able to save their post.';
$helptxt['spamBlocker_sbImagesCountHelp'] = 'This is the amount of images that are allowed for filtered topics/posts.<br /><br />If the user attempts to post more than this amount of images, they will receive an error message and will not be able to save their post.';
$helptxt['spamBlocker_sbTextFilterHelp'] = 'These are words that will be disallowed in filtered topic/posts.<br /><br />If the user attempts to post the words shown here, they will receive an error message and will not be able to save their post.<br /><br />These words should be greater than two characters each and separated by commas.';
$helptxt['spamBlocker_sbCharsCountHelp'] = 'This is the amount of characters that are allowed for filtered topics/posts.<br /><br />If the user attempts to post less than the amount of characters shown in the first input or more than the amount of characters shown in the second input, they will receive an error message and will not be able to save their post.<br /><br />Note: These settings have a minimum value of 1 for the first input and 60 for the second input.';
$helptxt['spamBlocker_sbWordCountHelp'] = 'This is the amount of forbidden word occurences that will be permitted prior to disallowing a topic/post to be saved.<br />One occurrence is the minimum setting for this feature.';
?>
