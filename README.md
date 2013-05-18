Spamblocker
===========

Anti-spam modification to restrict or limit access

SMF 2.0x - Spam Blocker 

Developed for SMF forums c/o Underdog 
Copyright 2013 Underdog@askusaquestion.net
Beta testers: Skhilled & TinMan

Purpose and/or usage of this software package:

The purpose of this anti-spam modification software package is to detect unsolicited web traffic (a.k.a. Spam) and restrict and/or limit its access from registering as users and/or participating on your Simple Machines Forum website.

This software package is distributed under the terms of its Freeware License (http://askusaquestion.net/index.php/page=spamblocker_license) whereas all of its condtional terms are noted within its license link from your SMF Administraion Panel and/or the link provided in this paragraph. If you do not agree to the terms shown in the license, do not download and/or use this software package.  


Spam Blocker Features:

+ User IP's and/or Email's are checked externally on anti-spam source sites
+ IP's/Emails that are flagged/reported as spam can be blocked from registering on your forum 
+ Flagged IP's/Emails can be banned upon registration
+ Flagged IP/Email can be redirected to a specific URL during the registration process (ie. Honeypot script)
+ Topics/Replies can be filtered through the Akismet database
+ Topics/Replies can be reported to the Stop Forum Spam database
+ Topics/Replies can be subject to rule based filtering
+ Specific membergroup(s) and preset number of initial posts can be opted for post filtering
+ Options for specific ban restrictions
+ Custom user & error messages  
+ Whitelist that allows specific IP's/Ranges to bypass the IP/Email check 
+ Blacklist of IP's/Ranges added to the ban list by Spam Blocker  
+ License and guide for usage are provided on the Administration page


Current anti-spam resources:

Registration:
+ Akismet Email Analysis
+ Stop Forum Spam Email Analysis
+ Stop Forum Spam IP Analysis
+ Project Honeypot IP Analysis
+ Spamhaus IP Analysis (sbl-xbl block list)


Posts/Topics:
+ Akismet Filtering
+ Akismet Reporting
+ Stop Forum Spam Reporting
+ Custom Rule Based Filtering Options


Annotations:
+ Do not edit the note text from entries added to your ban list from Spam Blocker. They are used as a reference for when this modification omits blacklist/ban list entities. That textarea input will be disabled for ban enitities added by Spam Blocker.
+ If an IP is somehow on the spam reporting source sites in error, it can be added to this modifications white list. It will not filter those whitelisted IP's during the registration process.   


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
+ License and guide provided on Admin page


Recommended minimal requirements:
Server: PHP 5.2+ with libxml, cURL, socket connections & DOM enabled
        MYSQL 5.0+ using MyISAM or InnoDB engine
Browser Add-Ons (for admin): Adobe Flashplayer 11.5+, JRE 7.10+, HTML5 capability
SMF Version: 2.0.4+


Disclaimers:

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Please read all other license agreements contained within this package. 
