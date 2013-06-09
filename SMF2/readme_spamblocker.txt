SMF 2.0x - Spam Blocker 

Developed for SMF forums c/o Underdog @ [url=http://askusaquestion.net]askusaquestion.net[/url]
Copyright 2013 Underdog@askusaquestion.net
Beta testers: Skhilled & TinMan

Purpose and/or usage of this software package:

The purpose of this anti-spam modification software package is to detect unsolicited web traffic (a.k.a. Spam) and restrict and/or limit its access from registering as users and/or participating on your Simple Machines Forum website.

This software package is distributed under the terms of its [url=http://askusaquestion.net/index.php/page=spamblocker_license]Freeware License[/url] whereas all of its condtional terms are noted within its license link from your SMF Administraion Panel and/or the link provided in this paragraph. If you do not agree to the terms shown in the license, do not download and/or use this software package.  

If you commend this software package and/or any other contributions that [url=http://askusaquestion.net]underdog@askusaquestion.net[/url] develops for the SMF community,
please feel free to make a donation to paypal using the image/link provided below. Thank you for opting to use this software package.


[url=http://askusaquestion.net/index.php/page=underdog_donation][img]https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif[/img][/url]


Spam Blocker Features:

[list]
[li]User IP's and/or Email's are checked externally on anti-spam source sites[/li]
[li]IP's/Emails that are flagged/reported as spam can be blocked from registering on your forum[/li] 
[li]Flagged IP's/Emails can be banned upon registration[/li]
[li]Flagged IP/Email can be redirected to a specific URL during the registration process (ie. Honeypot script)[/li]
[li]Topics/Replies can be filtered through the Akismet database[/li]
[li]Topics/Replies can be reported to the Stop Forum Spam database[/li]
[li]Topics/Replies can be subject to rule based filtering[/li]
[li]Specific membergroup(s) and preset number of initial posts can be opted for post filtering[/li]
[li]Options for specific ban restrictions[/li]
[li]Custom user & error messages[/li]  
[li]Whitelist that allows specific IP's/Ranges to bypass the IP/Email check[/li] 
[li]Blacklist of IP's/Ranges added to the ban list by Spam Blocker[/li]
[li]1 hour cache of data to limit resource usage[/li]
[li]License and guide for usage are provided on the Administration page[/li]
[/list]

[hr]

Current anti-spam resources:

Registration:
[list]
[li]Akismet Email Analysis[/li]
[li]Stop Forum Spam Email Analysis[/li]
[li]Stop Forum Spam IP Analysis[/li]
[li]Project Honeypot IP Analysis[/li]
[li]Spamhaus IP Analysis (sbl-xbl block list)[/li]
[/list]

Posts/Topics:
[list]
[li]Akismet Filtering[/li]
[li]Akismet Reporting[/li]
[li]Stop Forum Spam Reporting[/li]
[li]Custom Rule Based Filtering Options[/li]
[/list]

[hr]

Annotations:
[list]
[li]Do not edit the note text from entries added to your ban list from Spam Blocker. They are used as a reference for when this modification omits blacklist/ban list entities. That textarea input will be disabled for ban enitities added by Spam Blocker.[/li]
[li]If an IP is somehow on the spam reporting source sites in error, it can be added to this modifications white list. It will not filter those whitelisted IP's during the registration process.[/li]   
[/list]

Changelog:

[Version 1.0] 
+ Initial release
+ User IP's and/or Email Addresses can be checked & denied upon registration 
+ Option to ban reported IP's and/or Email Addresses
+ Option to redirect flagged entities to a specific URL during the registration process
+ Specific ban restrictions
+ Topics/Replies can be filtered through the Akismet database
+ Topics/Replies can be reported to the Stop Forum Spam database
+ Topics/Replies can be subject to rule based filtering
+ Editable message to be displayed to denied IP/Email (attempting registry)
+ Editable error log message
+ Whitelist to bypass IP check
+ Blacklist to display IP's added by Spam Blocker
+ Blacklist Optimization (Ban list comparison)
+ Option to delete expired Blacklist IP's
+ Option to allow Spamblocker to auto delete expired ban's
+ 1 hour cache (ip, time, pass/fail) to limit resource usage
+ License and guide provided on Admin page
[hr]

Recommended minimal requirements:
Server: PHP 5.2+ with libxml, cURL, socket connections & DOM enabled
        MYSQL 5.0+ using MyISAM or InnoDB engine
Browser Add-Ons (for admin): Adobe Flashplayer 11.5+, JRE 7.10+, HTML5 capability
SMF Version: 2.0.4+

[hr] 

Disclaimers:

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Please read all other license agreements contained within this package. 
