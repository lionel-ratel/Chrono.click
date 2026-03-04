<?php
/**
 * J!Dump
 * @version      $Id$
 * @package      com_jdump
 * @copyright    Copyright (C) 2007 - 2018 Mathias Verraes. All rights reserved.
 * @license      GNU/GPL
 * @link         https://github.com/mathiasverraes/jdump
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<!DOCTYPE html>
<html lang="en-gb" dir="ltr">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>J!Dump - <?php echo $this->application?></title>
  <link rel="stylesheet" href="<?php echo $this->mediaUri?>css/jdump.min.css" type="text/css" />

  <script type="text/javascript" src="<?php echo $this->mediaUri?>---js/mootools.min.js"></script>
  <script type="text/javascript" src="<?php echo $this->mediaUri?>js/folder-tree-static.min.js"></script>
  <script type="text/javascript" src="<?php echo $this->mediaUri?>js/jdump.min.js"></script>

  <script type="text/javascript">
		var imageFolderTree  =  '<?php echo $this->mediaUri; ?>images/';
		var dumpCloseTimeout =  '<?php echo $this->autoclose; ?>';
  </script>
</head>
<body>
   <fieldset class="dumpContainer">
      <legend>Application: <?php echo $this->application?></legend>
      <div class="dumpActions row">
         <a href="#" id="dumpLocked" class="dumpLocked dumpButton"><span class="dumpIcon"> </span>Window is locked</a>
         <a href="#" id="dumpLock" class="dumpLock dumpButton"><span class="dumpIcon"> </span>Lock Window</a>
         <a href="#" id="dumpRefresh" class="dumpRefresh dumpButton"><span class="dumpIcon"> </span>Refresh</a>

      <?php if( $this->tree=='' ) { ?>
            <p class="dumpNothing">No dumped variables found.</p>
      </div>
      <?php
      }
      else
      { ?>
         <a href="#" id="dumpExpandAll" class="dumpExpandAll dumpButton"><span class="dumpIcon"> </span>Expand all</a>
         <a href="#" id="dumpCollapseAll" class="dumpCollapseAll dumpButton"><span class="dumpIcon"> </span>Collapse all</a>
      </div>
      <div class="dumpTree">
         <ul id="dhtmlgoodies_tree" class="dhtmlgoodies_tree">
            <?php echo $this->tree; ?>
         </ul>
      </div>
      <?php } ?>
     <a class="dumpFooter" href="https://github.com/mathiasverraes/jdump" target="_blank">
        J!Dump v <?php echo $this->version; ?>
     </a>
   </fieldset>
	<div class="notify-alerts">
		<div id="system-message-container">
			<div id="system-message"></div>
		</div>
	</div>
</body>
</html>
