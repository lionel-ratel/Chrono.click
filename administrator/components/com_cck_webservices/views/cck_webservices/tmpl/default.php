<?php
/**
* @version 			SEBLOD WebServices 1.x
* @package			SEBLOD WebServices Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo htmlspecialchars( Uri::getInstance()->getPath() ); ?>" method="post" id="adminForm" name="adminForm">
<?php if ( !empty( $this->sidebar ) ) { ?>
    <div id="j-sidebar-container" class="span2 top-bar">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php } else { ?>
    <div id="j-main-container">
<?php } ?>

<div class="<?php echo $this->css['wrapper']; ?> hidden-phone">
    <div class="<?php echo $this->css['w100']; ?>">
        <div class="seblod first cpanel_news full beta">
            <div class="legend top center plus"><?php echo CCK_LABEL .' &rarr; '. Text::_( 'COM_CCK_ADDON_'.CCK_NAME ); ?></div>
            <ul class="adminformlist">
                <li style="text-align:center;">
                    <?php echo Text::_( 'COM_CCK_ADDON_'.CCK_NAME.'_DESC' ); ?>
                </li>
            </ul>
            <div class="clr"></div>            
        </div>
    </div>
	<div class="seblod-less cpanel_news full">
        <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', Text::_( 'COM_CCK_PANE_CPANEL' ), array( 'active'=>'collapse0', 'useCookie'=>'1' ) ); ?>
        <div id="cpanel" class="cpanel compact">
            <?php
			echo '<div class="fltlft">';
            Helper_Admin::addIcon( CCK_ADDON, _C4_LINK, _C4_NAME, Text::_( _C4_TEXT.'_MANAGER'.'_BR' ) );
			Helper_Admin::addIcon( CCK_ADDON, _C3_LINK, _C3_NAME, Text::_( _C3_TEXT.'_MANAGER'.'_BR' ) );
			// Helper_Admin::addIcon( CCK_ADDON, _C1_LINK, _C1_NAME, Text::_( _C1_TEXT.'_MANAGER'.'_BR' ) );
			Helper_Admin::addIcon( CCK_ADDON, _C2_LINK, _C1_NAME, Text::_( _C2_TEXT.'_MANAGER'.'_BR' ) );
			Helper_Admin::addIcon( CCK_ADDON, 'spacer', 'spacer', 'spacer' );
			echo '</div><div class="clr"></div>'
			?>
        </div>
        <?php echo JCckDevAccordion::end(); ?>
	</div>
    <div class="clr"></div>
</div>

<div class="clr"></div>
<div>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</div>

<?php
Helper_Display::quickCopyright();
?>
</div>
</form>