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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$css		=	array();
$doc        =   Factory::getDocument();
$user		=	Factory::getUser();
$userId		=	$user->id;
$listOrder	=	$this->state->get( 'list.ordering' );
$listDir	=	$this->state->get( 'list.direction' );

$config		=	JCckDev::init( array( '42', 'select_simple' ), true, array( 'vName'=>$this->vName ) );
$cck		=	JCckDev::preload( array( 'core_state_filter' ) );
Text::script( 'COM_CCK_CONFIRM_DELETE' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

$canChange	=	1; /* TODO#SEBLOD: get */
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

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="<?php echo $this->css['w50']; ?>">
        <div class="<?php echo $this->css['items']; ?> first pane1 integration-apps">
            <div class="legend top left"><?php echo Text::_( 'COM_CCK_INTEGRATION_APPS' ); ?></div>
            <table class="<?php echo $this->css['table']; ?> cck-margin-top-20">
                <thead>
                    <tr class="half">
                        <th width="32" class="center hidden-phone nowrap"></th>
                        <th class="center"><?php echo Text::_( 'COM_CCK_TITLE' ); ?></th>
                        <th width="15%" class="center nowrap"><?php echo Text::_( 'COM_CCK_STATUS' ); ?></th>
                        <th width="32" class="center hidden-phone nowrap"></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $items	=	JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_more_webservices_apps AS a ORDER by a.title' ); //#
                if ( count( $items ) ) {
                    foreach ( $items as $i=>$item ) {
                        $canEdit	=	1;
                        $checkedOut	=	0;
                        $link 		=	Route::_( 'index.php?option='.$this->option.'&task=integration_app.edit&id='. $item->id );
                        ?>
                        <tr class="row<?php echo $i % 2; ?> half">
                            <td class="center hidden-phone"><?php echo $i + 1 . '<span class="cck-display-none">' . str_replace( 'id="cb', 'id="cbc', HTMLHelper::_( 'grid.id', $i, $item->id ) ) . '</span>'; ?></td>
                            <td>
                                <?php
                                if ( $canEdit && ! $checkedOut ) {
                                    echo '<a href="'.$link.'">'.$this->escape( $item->title ).'</a>';
                                } else {
                                    echo '<span>'.$this->escape( $item->title ).'</span>';
                                }
                                ?>
                            </td>
                            <td class="center"><?php echo HTMLHelper::_( 'jgrid.published', $item->published, $i, 'integration_apps.', $canChange, 'cbc' ); ?></td>
                            <td class="center hidden-phone">
                                <a href="javascript:void(0);" class="btn btn-micro hasTooltip" onclick="return JCck.Dev.delete('integration_apps.delete','cbc<?php echo $i; ?>');" title="<?php echo Text::_( 'COM_CCK_DELETE' ); ?>">
                                    <span class="icon-delete"></span>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr class="half"><td class="center" colspan="5">'.Text::_( 'COM_CCK_NO_APP' ).'</td></tr>';
                }
                ?>
                </tbody>
			</table>
        </div>
    </div>
	<div class="<?php echo $this->css['w50']; ?>">
        <div class="<?php echo $this->css['items']; ?> first pane1 integration-auths">
            <div class="legend top left"><?php echo Text::_( 'COM_CCK_INTEGRATION_AUTHS' ); ?></div>
            <table class="<?php echo $this->css['table']; ?> cck-margin-top-20">
                <thead>
                    <tr class="half">
                        <th width="32" class="center hidden-phone nowrap"></th>
                        <th class="center"><?php echo Text::_( 'COM_CCK_TITLE' ); ?></th>
                        <th width="15%" class="center nowrap"><?php echo Text::_( 'COM_CCK_AUTO' ); ?></th>
                        <th width="15%" class="center nowrap"><?php echo Text::_( 'COM_CCK_STATUS' ); ?></th>
                        <th width="32" class="center hidden-phone nowrap"></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $items  =   JCckDatabase::loadObjectList( 'SELECT a.* FROM #__cck_more_webservices_auths AS a ORDER by a.title' ); //#
                if ( count( $items ) ) {
                    foreach ( $items as $i=>$item ) {
                        $canEdit	=	1;
                        $checkedOut	=	0;
                        $link       =   Route::_( 'index.php?option='.$this->option.'&task=integration_auth.edit&id='. $item->id );
                        ?>
                        <tr class="row<?php echo $i % 2; ?> half">
                            <td class="center hidden-phone"><?php echo $i + 1 . '<span class="cck-display-none">' . str_replace( 'id="cb', 'id="cbg', HTMLHelper::_( 'grid.id', $i, $item->id ) ) . '</span>'; ?></td>
                            <td>
                                <?php
                                if ( $canEdit && ! $checkedOut ) {
                                    echo '<a href="'.$link.'">' . $this->escape( $item->title ) . '</a>';
                                } else {
                                    echo '<span>'  .$this->escape( $item->title ) . '</span>';
                                }
                                ?>
                            </td>
                            <td class="center"><?php echo HTMLHelper::_( 'jgrid.isDefault', $item->featured, $i, 'integration_auths.', false, 'cbgf' ); ?></td>
                            <td class="center"><?php echo HTMLHelper::_( 'jgrid.published', $item->published, $i, 'integration_auths.', $canChange, 'cbg' ); ?></td>
                            <td class="center hidden-phone">
                                <a href="javascript:void(0);" class="btn btn-micro hasTooltip" onclick="return JCck.Dev.delete('integration_auths.delete','cbg<?php echo $i; ?>');" title="<?php echo Text::_( 'COM_CCK_DELETE' ); ?>">
                                    <span class="icon-delete"></span>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
					echo '<tr class="half"><td class="center" colspan="5">'.Text::_( 'COM_CCK_NO_AUTHORIZATION' ).'</td></tr>';
				}
                ?>
                </tbody>
			</table>
        </div>
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
$js =   '
        (function ($){
            JCck.Dev = {
                delete: function(task, cid) {
                    if (confirm(Joomla.JText._("COM_CCK_CONFIRM_DELETE"))) {
                        return '.( JCck::on( '4' ) ? 'Joomla.' : '' ).'listItemTask(cid,task);
                    } else {
                        return false;
                    }
                }
            };
            $(document).ready(function() {
                $("div.pane1").deepestHeight();
                $("div.pane2").deepestHeight();
            });
        })(jQuery);
        ';
$doc->addScriptDeclaration( $js );
?>
</div>
</form>
<?php Helper_Display::quickCopyright(); ?>