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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

HTMLHelper::stylesheet('com_jdump/jdump.min.css', array('relative' => true));

?>
<div class="jdump-about row">
<div class="card offset-2 col-md-8 mb-4">
  <div class="card-body">
    <h2 class="card-title">Using J!Dump</h2>
		<ul>
		<li>If you have deactivated the plugin, start your "debugging" session by activating
		it here in the components backend. It's recommended to turn it off when you are done.
		Remove any shortcodes from the code before deactivating the plugin!
		<li>Anywhere in your code, type:
			<p><code>dumpVar($variable, 'Variable Name');</code></p>
			<p>Save the code and run/rerun the browser. 'Variable Name' is optional and can be anything you like.
			If you use a lot of dumps, you'll want to use some descriptive names. e.g. copy the variable
			within single quotation marks.</p>
		</li>
		</ul>
	<h2 class="card-title">Other shortcuts</h2>
		<ul>
		<li><code>dumpMessage('Your message');</code>
			<p>Displays a custom message. Very handy to check if a function or a loop is executed,
			checking the programs flow etc...</p>
		</li>
		<li><code>dumpSysinfo();</code>
			<p>Displays a whole bunch of system information.</p>
		</li>
		<li><code>dumpTemplate($this);</code>
			<p>Use inside a template's index.php to dump the parameters.</p>
		</li>
		<li><code>dumpTrace();</code>
			<p>Displays the backtrace.</p>
		</li>
		</ul>
	</div>
</div>
<div class="card col-md-6 mb-4">
  <div class="card-body">
    <h2 class="card-title">Features</h2>
		<p>An advanced print_r and var_dump replacer with object tree display.
		and a couple of other useful functions.</p>
		<p>This utility makes life easy for developers and template
		designers. You use it to see what's inside a variable, an array or an
		object. Instead of using print_r() or var_dump(), you can now use
		dumpVar(). You do not have to stop the program execution as in many cases with print_r and var_dump.
		A popup will open in a window with a nice expandable DHTML tree,
		showing the contents of the variable or the other possible informations. It will even show a list of
		available methods for each object. You can use dumpVar() in your extensions,
		in the core, in libraries and even in templates.</p>
  </div>
</div>
<div class="card col-md-6 mb-4">
  <div class="card-body">
    <h2 class="card-title">Installation</h2>
    <h4 card-subtitle mb-2>After the normal package installation</h4>
		<ul>
		<li>The package installs the component and a plugin. To activate and use the plugin for a "debug" session
		you click on the Activate button.</li>
		<li>If you don't want the dump popup window to appear automatically,
		you can disable it in the configuration. You then have to display the results manually.</li>
		<li>To display the dump window	manually:
			<ul>
				<li>Administrator: Go to Components -> J!Dump and click Popup.</li>
				<li>Site: Create a new menu item for J!Dump. Set it to 'Open in New Window'</li>
			</ul>
		</li>
		<li>You can't use dumpVar() in system plugins that are run before the
			J!Dump plugin is run, so it is best to use ordering in the plugin
			manager to put J!Dump upfront.</li>
		</ul>
  </div>
</div>
<div class="card col-md-12 mb-4">
  <div class="card-body bg-warning">
    <h2 class="card-title">Warning</h2>
		<p>This component is only meant to be used on development test sites, NOT in live or production environments.
		 If you must use it on a live site, don't do stupid things like dumpVar($password) !</p>
	</div>
</div>
<div class="card col-md-12">
  <div class="card-body bg-success">
    <h2 class="card-title">Credits</h2>
		<p>This new version is based on <a href="https://github.com/mathiasverraes/jdump">Mathias Verraes J!Dump</a>
		 but has bin completely reworked for Joomla v.4.x. It still includes Folder Tree Static by Alf Magne
		Kalleland that is released under LGPL and can be found at http://www.dhtmlgoodies.com. SVG icons are generated
		by IcoMoon.io then modified and optimized.
	</div>
</div>
<form action="<?php echo Route::_('index.php?option=com_jdump&view=about'); ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="boxchecked" value="0">
	<?php echo HTMLHelper::_('form.token'); ?>
</form>

</div>
