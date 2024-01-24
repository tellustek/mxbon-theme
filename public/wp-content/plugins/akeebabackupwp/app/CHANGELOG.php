<?php die();?>
Akeeba Solo 7.7.6
================================================================================
# [HIGH] Some password managers prevent successful submission of the Site Setup page (you get an error about a missing email address)

Akeeba Solo 7.7.5
================================================================================
# [HIGH] BackBlaze B2 single file uploads were broken

Akeeba Solo 7.7.4
================================================================================
# [MEDIUM] Restoration. Administrator email appears as "undefined" in the Site Setup page
# [LOW] Test FTP Connection button in the Configuration page does not work, configuration still saved correctly
# [LOW] Restoration: Wrong message about the emial address when the administrator passwords don't match
# [LOW] Folder and file exclusions had very bad color contrast in Dark Mode

Akeeba Solo 7.7.3
================================================================================
+ Option about including the latest backup in remote quotas
- Removed PHP version warnings
# [LOW] ZIP Archiver, invalid CRC32 calculated for some small files in the installation folder

Akeeba Solo 7.7.2
================================================================================
~ Better warnings about CRC32 for ZIP files on 32-bit versions of PHP
# [HIGH] Quota settings and emails are not processed at the end of the backup process

Akeeba Solo 7.7.1
================================================================================
+ ANGIE for Joomla: reset session and cache options in Site Setup
+ Support for ShowOn to conditionally show options in the Configuration page
# [HIGH] Single part uploads to Azure stopped working
# [LOW] If you are shown a message that the Akeeba Backup tables are broken it will persist even after the error is fixed (e.g. with an update).

Akeeba Solo 7.7.0
================================================================================
+ Upload to Swift: Support for Keystone v3
# [LOW] "Test FTP connection" button was not correctly applying the passive mode
# [LOW] PHP 8: Fixed fatal error while working on the filesystem if the maximum_execution_time is not set

Akeeba Solo 7.6.10
================================================================================
+ More informative error messages for database connection issues during restoration
~ Workaround for utf8_encode and _decode being deprecated in PHP 8.2
# [LOW] Restoration: You were shown separate port and socket options which were not taken into account
# [MEDIUM] Restoration: Using a custom port or socket might result in the wrong hostname being written in the restored site's configuration file
# [MEDIUM] Possible infinite loop on PHP 8 during DB restoration if a SQL file is missing
# [LOW] Invalid SQL dump if we cannot get the create commands for a function, procedure or trigger

Akeeba Solo 7.6.9
================================================================================
+ Improved FTP/SFTP test connection feature
+ Restoration: Warn about missing mysqli / PDO MySQL and REFUSE to proceed
# [HIGH] Cannot download file from Amazon S3
# [LOW] PHP Warning when backing up a database (purely cosmetic issue)

Akeeba Solo 7.6.6
================================================================================
# [HIGH] Pagination in the Manage Backups page was broken
# [HIGH] Cannot connect to databases on localhost using the default named pipe
# [MEDIUM] Custom Amazon S3 regions would not work with custom endpoints

Akeeba Solo 7.6.5
================================================================================
+ Support for custom Amazon S3 regions
+ Support for MySQL SSL connections for Akeeba Solo's database
+ Support for MySQL SSL/TLS connections for backed up sites
~ More detailed error information in Debug Mode
# [LOW] Fix wrong lang string for “Set a large memory limit”
# [LOW] PHP warnings in the application setup page, proxy fields

Akeeba Solo 7.6.4
================================================================================
+ Much improved FTP functions for uploading backup archives and transferring sites
+ Upload to Azure BLOB Storage now supports chunked uploads, files up to 190.7TB (up from 64Mb)
+ OneDrive for Business: you can now use Drives other than your personal
~ Stricter conditions for determining when to show the “Manage remotely stored files” button in Manage Backups
# [MEDIUM] OneDrive: Uploads may fail if they are between 4Mb and 100Mb
# [LOW] Wrong error handler when installing updates makes the update look hanged when there's an error
# [LOW] Downloading the Pro version upgrade may fail with a cURL error

Akeeba Solo 7.6.3
================================================================================
+ Restoration: ANGIE now applies very high memory and execution time limits to prevent some timeout / memory outage issues on most hosts.
+ Restoration: Data replacement will skip over entire tables with no data in need of replacement.
+ Restoration: Data replacement uses transactions for better performance.
+ Restoration: ANGIE now warns you if you leave the database connection information empty
+ Option to set a really large PHP memory limit during backup
# [LOW] The JPS archiver would show warnings about unreadable files when archiving directories without any files in them.

Akeeba Solo 7.6.2.1
================================================================================
# [HIGH] Version 7.6.2 was missing files from its packages

Akeeba Solo 7.6.2
================================================================================
# [HIGH] Uploading to OVH is broken on many servers not using a proxy

Akeeba Solo 7.6.1
================================================================================
+ Auto-disable browser downloads when PHP error disable is not positively disabled
+ Add support for AKEEBADEBUG_ERROR_DISPLAY (mapped to WP_DEBUG_DISPLAY on WordPress)
~ PHP 8.1 compatibility improvements in the restoration script
# [HIGH] Wrong RewriteBase set up in the .htaccess Maker when restoring a Joomla site with Admin Tools Professional installed
# [MEDIUM] Configuration Wizard could not autodetect Joomla 4 sites
# [LOW] Error page was unreadable in dark mode

Akeeba Solo 7.6.0
================================================================================
+ Allow using [REMOTESTATUS] in the email subject, not just the body
+ Joomla restoration: modify domains in the Admin Tools' Allowed Domains and server config maker features if necessary
~ Fixed table alignment in Manage Files
# [HIGH] Problems restoring if a table name ends in 0 when another table with an identical name EXCEPT the trailing zero is also being backed up
# [MEDIUM] Log Priorities global configuration option got mangled restoring a Joomla 4 site
# [LOW] RackSpace CloudFiles: some hosts change the case of HTTP headers

Akeeba Solo 7.5.16
================================================================================
+ Support for MySQL 8 invisible columns
# [LOW] Rare type error under PHP 8 during restoration

Akeeba Solo 7.5.15
================================================================================
- Remove piecon (pie graph favicon showing the backup progress)
~ JSON API: Forcibly use the ‘json’ origin everywhere
~ JSON API: Throw an error if the backup ID sent to stepBackup does not exist
~ JSON API: Improved backup IDs prevent a number of JSON API issues
# [LOW] Some rare hosts do not register $_REQUEST at all

Akeeba Solo 7.5.14
================================================================================
- Removed iDriveSync; the service has been discontinued by the provider.
- Removed the “Archive integrity check” feature.
~ Dropbox connector updated to require TLS v1.2
~ Work around servers messing up the AJAX responses
~ Improved the display of the files and folders filters page
# [MEDIUM] Possible exception on Files and Directories Filters page under PHP 8

Akeeba Solo 7.5.13
================================================================================
+ Increase the maximum Size Quota limit to 1Pb
+ Support for outbound proxy configuration
~ Remove dash from automatically generated random values for archive naming
# [MEDIUM] Cannot restore on PHP 8 if Two Factor Authentication is enabled in any user account
# [HIGH] Backing up to Box, Dropbox, Google Drive or OneDrive may not be possible if you are using an add-on Download ID

Akeeba Solo 7.5.12
================================================================================
# [HIGH] Regression: fixed integrated restoration

Akeeba Solo 7.5.11
================================================================================
# [HIGH] Regression: integrated restoration no longer works
# [HIGH] JSON API: Changing the wrapper from ### broke the integration of third party services and Akeeba Remote CLI
# [LOW] Warning in Manage Backups page if you have deleted the backup profile used to take a backup listed there

Akeeba Solo 7.5.10
================================================================================
~ Change the way AJAX requests work to work around sites with ### in their name

Akeeba Solo 7.5.9
================================================================================
+ Restoration: information about disabling the password protection.
~ Remove ROW_FORMAT during backup and restoration, makes it easier restoring sites using InnoDB across different MySQL server versions
# [HIGH] Remote JSON API v2 fails on PHP 8
# [MEDIUM] Tables with only numbers in their names cause the backup to fail
# [LOW] Removed PostgreSQL from available drivers in the Setup page
# [LOW] Fixed cosmetic issue on User preferences page

Akeeba Solo 7.5.8
================================================================================
! Encrypted settings could not be read

Akeeba Solo 7.5.7
================================================================================
- Removed Upload to pCloud
+ Only show failed backups' log files in ALICE
+ Stealth Mode support in integrated restoration
# [MEDIUM] Cannot download multipart backups from the popup in the Manage Backups page.
# [LOW] MySQL spatial data might be impossible to restore if there is a collation mismatch between the origin and target server.
# [LOW] Missing file in Core version would prevent auto-updates
# [LOW] PHP 8 could still throw an error while backing up under some rare circumstances
# [LOW] Fixed fatal error while sending backup email notification under certain server configuration
# [LOW] PHP warning when the site's root is in an absolute root subdirectory (e.g. /site instead)

Akeeba Solo 7.5.6
================================================================================
~ WordPress restoration: Home URL and Site name are now required
~ Converted all tables to InnoDB for better performance
# [HIGH] Cannot take split archive backups under PHP 8
# [LOW] Cannot list an open_basedir restriction root

Akeeba Solo 7.5.5.1
================================================================================
# [MEDIUM] Update failure if plugin installed in folder named other than akeebabackupwp
# [HIGH] WordPress restoration: the replace data page does not display

Akeeba Solo 7.5.5
================================================================================
# [HIGH] Tabs not working in the System Configuration page
# [HIGH] WordPress restoration: the replace data page does not display

Akeeba Solo 7.5.4
================================================================================
+ You can select the file permissions of backup archives
# [LOW] WordPress restoration: no data replacement when restoring to same URL but different filesystem site root
# [HIGH] Dropbox for Business wouldn't work with the new scoped access tokens
# [HIGH] Dropbox refresh token would disappear after the first refresh, making it impossible to use Dropbox reliably

Akeeba Solo 7.5.3
================================================================================
~ Releasing the previous version without changes, due to server and CDN issues which resulted in inconsistent updates
~ Improved CHANGELOG layout in the Control Panel page
# [HIGH] Import from S3: you cannot select .jps files
# [LOW] Recommended PHP version was shown as 7.3 instead of 7.4

Akeeba Solo 7.5.2
================================================================================
~ Improved CHANGELOG layout in the Control Panel page
# [HIGH] Import from S3: you cannot select .jps files
# [LOW] Recommended PHP version was shown as 7.3 instead of 7.4

Akeeba Solo 7.5.1
================================================================================
+ Post-backup emails can now display the total backup size and the approximate size of each part file
# [LOW] No list of backup files in the post-backup email when using a post-processing engine
# [HIGH] Backup failure to S3 with a PHP Type Error when the Dual Stack option has no value
# [HIGH] Uploading to Dropbox would fail if you linked your Dropbox account after December 2020
# [LOW] Manage Remotely Stored Files actions could fail on Box, Dropbox, OneDrive and Google Drive if the access token had expired in the meantime.

Akeeba Solo 7.5.0
================================================================================
+ Minimum required PHP version is now 7.2.0
+ Dropbox is now using the scoped API access.
+ Amazon S3: Added support for Dual Stack option (use of IPv6 when available)
~ Add PHP 8.0 in the list of known PHP versions, recommend PHP 7.4 or later
~ Remove the JPS and ANGIE password fields from the Backup Now page. You can still configure these features in the backup profile's Configuration page.
# [MEDIUM] PHP 8: fatal error uploading to Amazon S3, CloudFiles
# [LOW] Using [SITENAME] in a backup archive name resulted in a single dash being output.
# [LOW] UI elements in the the Files and Folders Exclusion pages would still show native tooltips with HTML tags in them.

Akeeba Solo 7.4.0.1
================================================================================
! Akeeba Solo Core: cannot access the application or take a backup because of a PHP error due to an incorrect reference to a Pro-only class.

Akeeba Solo 7.4.0
================================================================================
+ Files and Directories Exclusion: mark folder and file symlinks as such
+ Automatically rewrite the Output Directory using site path variables such as [SITEROOT] for portability
+ Automatically rewrite the Off-site Folders Inclusion using site path variables for portability
+ Remote backup JSON API version 2
+ ANGIE: Added feature to resume restoring the database if an error occurs
~ Deprecated Upload to pCloud
~ Removed tooltips from Database Tables Exclusion and Files and Folders Exclusion pages to clean up the UI
~ Replace zero datetime with nullable datetime
# [MEDIUM] Recent Chrome and Chromium-based browsers open OAuth2 windows without opener information, making linking to Google Drive, Dropbox etc impossible without manually copying the tokens (the button causes you to log out of the site)
# [LOW] Files and Directories Exclusion: the folder up is not clickable / doesn't do anything

Akeeba Solo 7.3.2.1
================================================================================
~ Manual updates by uploading files DO NOT update the database, causing subsequent scheduled and remote backups to fail

Akeeba Solo 7.3.2
================================================================================
# [HIGH] CLI and remote backups could end up running forever if the MySQL connection was closed by the host at an inopportune moment
# [MEDIUM] Remote backup failure on hosts which prevent creation of .php files

Akeeba Solo 7.3.1
================================================================================
~ Normalized the default backup description under all backup methods (backend, frontend, CLI, JSON API, WP-CLI)
+ Massive speedup in data replacement of heavily nested serialised tables with thousands of elements
+ You can select a faster algorithm for data replacement of really big serialised data during restoration
# [HIGH] WebDAV fails to upload because of the wrong absolute URL being calculated
# [LOW] pCloud was erroneously listed in the free of charge Core version (it requires a paid subscription and was thus unusable)
# [LOW] Frontend backup URL does not work if the secret key contains the plus sign (+) character due to a PHP bug.

Akeeba Solo 7.3.0
================================================================================
+ S3: Add support for Cape Town and Milan regions
+ You can now change the base font size in Akeeba Solo
+ Added feature to "freeze" some backup records to keep them indefinitely
- Removed support for Internet Explorer
~ Improve default header and body fonts for similar cross-platform "feel" without the need to use custom fonts.
~ Rendering improvements
~ Adjust size of control panel icons
# [HIGH] Replacing (not just removing) AddHandler/SetHandler lines would fail during restoration
# [MEDIUM] Fetching back to server the archives from these provides would result in invalid archives: Amazon S3, Backblaze, Cloudfiles, OVH, Swift
# [MEDIUM] Greedy RegEx match in database dump could mess up views containing the literal ' view ' (word "view" surrounded by spaces) in their definition.
# [LOW] The akeeba_common table is not automatically excluded when you choose not to include Solo in the backup
# [LOW] Fixed fatal error when trying to use a non-existent profile
# [LOW] Fixed filtering by Profile in Manage Backups view
# [LOW] Fixed timestamp in default backup description

Akeeba Solo 7.2.2.1
================================================================================
# [MEDIUM] The warning about the default directory being in use was not visible on the Control Panel page

Akeeba Solo 7.2.2
================================================================================
+ Automatic UTF8MB4 character encoding downgrades from MySQL 8 to 5.7/5.6/5.5 on restoration.
# [MEDIUM] Missing declaration for cacert.pem path

Akeeba Solo 7.2.0.1
================================================================================
! The version file was missing from the package, causing the update to always show up as being available

Akeeba Solo 7.2.0
================================================================================
+ Minimum required PHP version is now 7.1.0
~ Remove multiple, unnecessary copies of the cacert.pem file
# [LOW] Very rare backup failures with a JS error

Akeeba Solo 7.1.4
================================================================================
# [LOW] Multipart upload to BackBlaze B2 might fail due to a silent B2 behavior change
# [LOW] OneDrive upload failure if a part upload starts >3600s after token issuance

Akeeba Solo 7.1.3
================================================================================
~ Reserved version number to maintain continuity with Akeeba Backup for Joomla versioning

Akeeba Solo 7.1.2
================================================================================
+ Improved error handling allows reporting PHP fatal errors (only available on sites using PHP 7)
# [LOW] Fixed typos that could create issues with servers using very restrictive security rules
# [LOW] (S)FTP connection test would report "false" instead of the reason of failure
# [LOW] Fixed archive download using the browser under some circustances

Akeeba Solo 7.1.1
================================================================================
~ Possible exception when the user has erroneously put their backup output directory to the site's root with open_basedir restrictions restricting access to its parent folder.
# [MEDIUM] OneDrive for Business is not working at all in Akeeba Solo

Akeeba Solo 7.1.0
================================================================================
+ Automatic security check of the backup output directory
+ Option to change post GUIDs on restoration
+ Yes/No toggles are now colorful instead of plain teal
~ Renamed helper functions
~ Improved storage of temporary data during backup [akeeba/engine#114]
~ Log files now have a .php extension to prevent unauthorized access in very rare cases
~ Enforce the recommended, sensible security measures when using the default backup output directory
~ Ongoing JavaScript refactoring
~ Google Drive: fetch up to 100 shared drives (previously: up to 10)
# [MEDIUM] CloudFiles post-processing engine: Fixed file uploads
# [MEDIUM] Swift post-processing engine: Fixed file uploads
# [LOW] Send by Email reported a successful email sent as a warning
# [LOW] Too many (removed and no longer working) options for site types in the Configuration Wizard.
# [LOW] Extra greater-than sign in the Configuration icon's URL in the Control Panel page
# [LOW] The tooltips visual style was slightly broken
# [LOW] Database dump: foreign keys' (constraints) and local indices' names did not get their prefix replaced like tables, views etc do

Akeeba Solo 7.0.2
================================================================================
~ Log the full path to the computed site's root, without <root> replacement
# [HIGH] Core (free of charge) version only: the PayPal donation link included a tracking pixel. Changed to donation link, without tracking.
# [MEDIUM] Integrated restoration: sanity checks were not applied, resulting in extraction errors
# [MEDIUM] WebDav post-processing engine: first backup archive was always uploaded on the remote root, ignoring any directory settings
# [HIGH] Restoration will fail if a table's name is a superset of another table's name e.g. foo_example_2020 being a superset of foo_example_2.

Akeeba Solo 7.0.1
================================================================================
- pCloud: removing download to browser (cannot work properly due to undocumented API restrictions)
# [HIGH] An error about not being able to open a file with an empty name occurs when taking a SQL-only backup but there's a row over 1MB big
# [LOW] Notice in Control Panel when maximum error reporting is enabled
# [LOW] Backup log file did not appear correctly (but you could still download it)
# [LOW] Redirections for the legacy frontend backup method should be to remote.php, not index.php
# [LOW] Dark Mode: modal close icon was invisible both in the backup software and during restoration
# [LOW] Dark Mode: some interface elements (e.g. footer, modal chrome) would always appear in their Light Mode scheme
# [LOW] Fixed automatically filling DropBox tokens after OAuth authentication

Akeeba Solo 7.0.0
================================================================================
+ Remove TABLESPACE and DATA|INDEX DIRECTORY table options during backup
# [MEDIUM] Fetching update information could result in an exception if the latest available version cannot be installed on this site
# [LOW] FTP and SFTP connection tests were always failing
# [LOW] Fixed applying quotas for obsolete backups

Akeeba Solo 7.0.0.rc1
================================================================================
+ Upload to OVH now supports Keystone v3 authentication, mandatory starting mid-January 2020
- Remove obsolete "Use IFRAMEs instead of AJAX" option
# [HIGH] An error in an early backup domain could result in a forever-running backup
# [HIGH] DB connection errors wouldn't result in the backup failing, as it should be doing
# [HIGH] Manage remotely stored files: Fetch to server would fail after the first batch of downloads

Akeeba Solo 7.0.0.b3
================================================================================
+ Common PHP version warning scripts
+ Reinstated support for pCloud after they fixed their OAuth2 server
~ Improved Dark Mode
~ Improved PHP 7.4 compatibility
~ Clearer message when setting decryption fails in CLI backup script
~ Replace JavaScript eval() with JSON.parse()
# [HIGH] The database dump was broken with some versions of PCRE (e.g. the one distributed with Ubuntu 18.04)
# [HIGH] The integrated restoration was broken

Akeeba Solo 7.0.0.b2
================================================================================
- Removed pCloud support
+ ANGIE: Options to remove AddHandler lines on restoration
# [MEDIUM] Fixed OAuth authentication flow
# [LOW] Configuration wizard will always prompt to the user

Akeeba Solo 7.0.0.b1
================================================================================
+ Amazon S3 now supports Bahrain and Stockholm regions
+ Amazon S3 now supports Intelligent Tiering, Glacier and Deep Archive storage classes
+ Google Storage now supports the nearline and coldline storage classes
+ Manage Backups: Improved performance of the Transfer (re-upload to remote storage) feature.
+ Windows Azure BLOB Storage: download back to server and download to browser are now supported
+ New OneDrive integration supports both regular OneDrive and OneDrive for Business
+ pCloud support
+ Support for Dropbox for Business
+ Minimum required PHP version is now 5.6.0
~ Common version numbering among all of our backup products means this version is 7, not 4
~ All views have been converted to Blade for easier development and better future-proofing
~ The integrated restoration feature is now only available in the Professional version
~ The front-end legacy backup API and the Remote JSON API are now available only in the Professional version
~ The Site Transfer Wizard is now only available in the Professional version
~ SugarSync integration: you now need to provide your own access keys following the documentation instructions
~ Backup error handling and reporting (to the log and to the interface) during backup has been improved.
~ The Test FTP/SFTP Connection buttons now return much more informative error messages.
~ Manage Backups: much more informative error messages if the Transfer to remote storage process fails.
~ The backup and log IDs will follow the numbering you see in the left hand column of the Manage Backups page.
~ Manage Backups: The Remote File Management page is now giving better, more accurate information.
~ Manage Backups: Fetch Back To Server was rewritten to gracefully deal with more problematic cases.
~ Removed AES encapsulations from the JSON API for security reasons. We recommend you always use HTTPS with the JSON API.
# [HIGH] Changing the database prefix would not change it in the referenced tables inside PROCEDUREs, FUNCTIONs and TRIGGERs
# [HIGH] Backing up PROCEDUREs, FUNCTIONs and TRIGGERs was broken
# [HIGH] Manage Backups: would not show Transfer Archive for qualifying backups not fully uploaded to remote storage.
# [MEDIUM] Database only backup of PROCEDUREs, FUNCTIONs and TRIGGERs does not output the necessary DELIMITER commands to allow direct import
# [MEDIUM] BackBlaze B2: upload error when chunk size is higher than the backup archive's file size
# [LOW] Manage Backups: the Remote Files Management dialog's size was off by several pixels
# [LOW] Manage Backups: downloading a part file from S3 beginning with text data would result in inline display of the file instead of download.
# [LOW] Disabled menu items (e.g. Backup Now) page confused people; removed them to prevent confusion
