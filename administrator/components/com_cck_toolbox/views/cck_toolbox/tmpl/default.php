<?php
/**
* @version 			SEBLOD Toolbox 1.x
* @package			SEBLOD Toolbox Add-on for SEBLOD 3.x
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$css        =   array();
$doc        =   Factory::getDocument();
$user       =   Factory::getUser();
$userId     =   $user->id;

$config     =   JCckDev::init( array( '42', 'select_simple' ), true, array( 'vName'=>$this->vName ) );
$cck        =   JCckDev::preload( array( 'core_state_filter' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

$icon_trash =   Uri::root( true ).'/administrator/components/com_cck/assets/images/16/icon-16-trash.png';

$canChange  =   1; /* TODO#SEBLOD: get */
$doc->addStyleDeclaration('.plugins-list li{width:16em; float:left; min-height:28px;} .plugins-list li.not-found{color:#DC3912;} .plugins-list li.unpublished{color:#109618;}');
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
        <div class="seblod first cpanel_news full">
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
        <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', Text::_( 'COM_CCK_ANALYTICS_CONTENT' ), array( 'active'=>'collapse0', 'parent'=>true, 'useCookie'=>'1' ) ); ?>
        <div class="seblod">
            <?php
            $items  =   JCckDatabase::loadObjectList( 'SELECT a.storage_location AS text, COUNT( a.storage_location ) AS num FROM #__cck_core AS a'
                                                    . ' WHERE a.cck != "" AND a.storage_location != "" GROUP BY a.storage_location', 'text' );
            echo Helper_Display::renderChart( $items, array( 'id'=>'items',
                                                             'legend'=>'none',
                                                             'type'=>'PieChart',
                                                             'width'=>'600',
                                                             'height'=>'420' ) );
            ?>
        </div>
        <?php echo JCckDevAccordion::open( 'cckOptions', 'collapse1', Text::_( 'COM_CCK_OPTIMIZATIONS_PLUGINS' ) ); ?>
        <div class="seblod">
            <?php
            $html   =   '';
            $items  =   JCckDatabase::loadObjectList( 'SELECT a.folder AS text, COUNT( a.folder ) AS num FROM #__extensions AS a'
                                                    . ' WHERE a.type = "plugin" AND a.folder LIKE "cck_%" AND a.enabled = 1 GROUP BY a.folder', 'text' );
            foreach ( $items as $k=>$item ) {
                $rows   =   $this->completePlugins( $items, $k, 'not-found' )
                        .   $this->completePlugins( $items, $k, 'unpublished' );

                if ( $rows ) {
                    $html   .=  '<fieldset><legend>'.$items[$k]->text.'</legend>'
                            .   '<ul class="plugins-list">'.$rows.'</ul>'
                            .   '</fieldset>';
                }
            }
            
            echo Helper_Display::renderChart( $items, array( 'id'=>'plugins',
                                                             'legend'=>'none',
                                                             'label2'=>'In Use',
                                                             'label3'=>'Not Found',
                                                             'label4'=>'Unpublished',
                                                             'type'=>'BarChart',
                                                             'width'=>'600',
                                                             'height'=>'420' ) );

            echo $html;
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
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo HTMLHelper::_( 'form.token' ); ?>
</div>

<?php
Helper_Display::quickCopyright();
?>
</div>
</form>

<script type="text/javascript">
jQuery(document).ready(function($) {
    $("div.pane1").css("height","425px");
});
</script>