<?php
/**
* @version 			SEBLOD Exporter 1.x
* @package			SEBLOD Exporter Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>

CHANGELOG:

Legend:

* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note

@ID is the ID on SEBLOD Tracker.

-------------------- 1.7.1 Upgrade Release [30-May-2017] ------------------

# Ajax capabilities issue fixed.

-------------------- 1.7.0 Upgrade Release [3-May-2017] -------------------

* Security issue.
  >> Making sure we have a valid task to allow the request.

! SEBLOD 3.11 ready.
! Copyright Updated.

+ "onCckPreBeforeImport", "onCckPostBeforeImport" events added.
+ "onCckPreBeforeImports", "onCckPostBeforeImports" events added.

+ Ajax capabilities added (when triggered from a Submit Button).
+ Limit paramter added.

# Minor issue fixed.

-------------------- 1.6.0 Upgrade Release [26-Aug-2016] ------------------

+ Extensive cleaning performed.
  >> Deprecated Jquery stuff removed.
  >> Deprecated SEBLOD stuff removed.
  >> Joomla! 2.5 not supported anymore. :)

-------------------- 1.5.1 Upgrade Release [6-Jun-2016] -------------------

^ Icon added on "Export" Button.
^ Icon for "Session" updated.

-------------------- 1.5.0 Upgrade Release [25-May-2016] ------------------

^ Default separator is now ";"

# Conditional States fixed.
# User Export ("All Fields") issue fixed.

-------------------- 1.4.2 Upgrade Release [25-Apr-2016] ------------------

! Copyright Updated.
! Language constant updated for Updater Add-on.

-------------------- 1.4.1 Upgrade Release [7-Sep-2015] -------------------

! Language constant updated for Updater Add-on.

-------------------- 1.4.0 Upgrade Release [17-Jul-2015] ------------------

! Ability to manage Sessions >> SEBLOD 3.7.0 required. (!)

+ Session Manager button added in the toolbar.
+ Session Dropdown Menu updated.

# Javascript issues fixed.

-------------------- 1.3.0 Upgrade Release [7-May-2015] ------------------

! Joomla! 3.4 ready.

^ Implement JCckExporterVersion class.
+ "Prepare Output" added.

-------------------- 1.2.2 Upgrade Release [29-Apr-2014] ------------------

! Download URL updated for SEBLOD 3.3.4
# Labels used as header.. (from component back-end) but it shouldn't! (regression)

-------------------- 1.2.1 Upgrade Release [24-Feb-2014] ------------------

! Export (from SEBLOD List) is now based on the current query/filters. 
! Export (from SEBLOD List) does not require JGrid Typography plug-in anymore. 

# Labels used as header instead of Field names. (inherited from List)
# Ordering of columns fixed. (inherited from List)
# Ordering of items fixed. (inherited from List)

-------------------- 1.2.0 Upgrade Release [24-Dec-2013] ------------------

! Joomla! 3.2 ready.
+ Allows to export items (Articles, Categories, Users) from any SEBLOD Lists.

-------------------- 1.1.0 Upgrade Release [31-May-2013] -------------

! Joomla! 3 ready.
+ Sessions added.

-------------------- 1.0.0 Initial Release [18-Jan-2012] ------------------

+ Initial Release