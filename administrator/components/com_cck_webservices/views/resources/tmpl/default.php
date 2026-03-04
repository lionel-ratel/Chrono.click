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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

$css		=	array();
$doc		=	Factory::getDocument();
$user		=	Factory::getUser();
$userId		=	$user->id;
$listOrder	=	$this->state->get( 'list.ordering' );
$listDir	=	$this->state->get( 'list.direction' );
$title2		=	Text::_( 'COM_CCK_GO_TO_WEBSERVICE_DESCRIPTION' );
$config		=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true, array( 'vName'=>$this->vName ) );
$cck		=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear', 'core_state_filter', 'more_webservices_resource_type' ) );
Text::script( 'COM_CCK_CONFIRM_DELETE' );
PluginHelper::importPlugin( 'cck_webservice' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName() ); ?>" method="post" id="adminForm" name="adminForm">
<?php if ( !empty( $this->sidebar ) ) { ?>
    <div id="j-sidebar-container" class="span2 top-bar">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php } else { ?>
    <div id="j-main-container">
<?php } ?>

<?php include_once __DIR__.'/default_filter.php'; ?>
<div class="<?php echo $this->css['items']; ?>">
	<table class="<?php echo $this->css['table']; ?>">
	<thead>
		<tr>
			<th width="60" class="center hidden-phone nowrap"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone no-pad"><?php echo HTMLHelper::_('grid.checkall'); ?></th>
			<th class="center" colspan="2"><?php echo HTMLHelper::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder ); ?></th>
			<th width="20%" class="center hidden-phone nowrap" colspan="2"><?php echo HTMLHelper::_( 'grid.sort', 'COM_CCK_APP_FOLDER', 'folder_title', $listDir, $listOrder ); ?></th>
			<th width="16%" class="center hidden-phone nowrap"><?php echo HTMLHelper::_( 'grid.sort', 'COM_CCK_TYPE', 'a.type', $listDir, $listOrder ); ?></th>
			<th width="16%" class="center hidden-phone nowrap"><?php echo Text::_( 'COM_CCK_METHODS' ); ?></th>
			<th width="8%" class="center nowrap"><?php echo HTMLHelper::_( 'grid.sort', 'COM_CCK_STATUS', 'a.published', $listDir, $listOrder ); ?></th>
			<th width="32" class="center hidden-phone nowrap"><?php echo HTMLHelper::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
		</tr>
	</thead>
    <tbody>
	<?php
	foreach ( $this->items as $i=>$item ) {
		$checkedOut		= 	! ( $item->checked_out == $userId || $item->checked_out == 0 );
		$canCheckin		=	$user->authorise( 'core.manage', 'com_checkin' ) || $item->checked_out == $userId || $item->checked_out == 0;
		$canChange		=	$user->authorise( 'core.edit.state', CCK_ADDON ) && $canCheckin;
		$canEdit		=	$user->authorise( 'core.edit', CCK_ADDON );
		$canEditFolder	=	$user->authorise( 'core.edit', CCK_COM.'.folder.'.$item->folder );
		$canEditOwn		=	'';	
		
		$link 			=	Route::_( 'index.php?option='.$this->option.'&task='.$this->vName.'.edit&id='. $item->id );
		$linkFilter		=	Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->folder );
		$linkFolder		=	Route::_( 'index.php?option='.CCK_COM.'&task=folder.edit&id='. $item->folder );
		Helper_Admin::addFolderClass( $css, $item->folder, $item->folder_color, $item->folder_colorchar );
		?>
		<tr class="row<?php echo $i % 2; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone no-pad"><?php Helper_Display::quickCheckbox( $i, $item ); ?></td>
			<td width="30px" class="center hidden-phone"></td>
			<td>
				<div class="title-left" id="title-<?php echo $item->id; ?>">
					<?php
					if ( $item->checked_out ) {
						echo HTMLHelper::_( 'jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->vName.'s.', $canCheckin )."\n";
					}
					if ( $canEdit && ! $checkedOut ) {
						echo '<a href="'.$link.'">'.$this->escape( $item->title ).'</a><div class="small">'.$this->escape( '/'.$item->name ).'</div>';
					} else {
						echo '<span>'.$this->escape( $item->title ).'</span><div class="small">'.$this->escape( '/'.$item->name ).'</div>';
					}
					?>
				</div>
			</td>
			<td align="center" width="4%">
				<a href="<?php echo $linkFilter; ?>" style="text-decoration: none;" class="hidden-phone">
                    <div class="<?php echo ( $item->folder_color || ( $item->folder_colorchar && $item->folder_introchar ) ) ? 'folderColor'.$item->folder : ''; ?>" style="vertical-align: middle;">
                        <strong><?php echo $item->folder_introchar; ?></strong>
                    </div>
                </a>
			</td>
			<td class="center hidden-phone">
				<?php
                if ( ! $item->folder_parent ) {
                    $linkFolderTree	=	Route::_( 'index.php?option='.CCK_COM.'&view=folders&filter_folder='. $item->folder );
                    $folder_parent	=	'';
                } else {
                    $linkFolderTree	=	Route::_( 'index.php?option='.CCK_COM.'&view=folders&filter_folder='. $item->folder_parent );
                    $folder_parent	=	'<br /><a class="folder-parent small" href="'.$linkFolderTree.'">'.$item->folder_parent_title.'</a>';
                }
				echo ( $canEditFolder ) ? '<a href="'.$linkFolder.'">' . $this->escape( $item->folder_title ) . '</a>' . $folder_parent
										: '<span>' . $this->escape( $item->folder_title ) . '</span>' . $folder_parent;
                ?>
			</td>
			<td class="center hidden-phone"><?php echo $item->type; ?></td>
            <td class="center hidden-phone"><?php echo str_replace( ',', '<br />', $item->methods ); ?></td>
			<td class="center"><?php echo HTMLHelper::_( 'jgrid.published', $item->published, $i, $this->vName.'s.', $canChange, 'cb' ); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'border-top', $item->id ); ?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
    <?php if ( (int)$this->pagination->pagesTotal > 1 ) { ?>
	<tfoot>
		<tr height="40px;">
	        <td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'border-top', 'up' ); ?></td>
			<td colspan="8" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'border-top', 'up' ); ?></td>
		</tr>
	</tfoot>
	<?php } ?>
	</table>
</div>
<?php /* include_once __DIR__.'/default_batch.php'; */ ?>
<div class="clr"></div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="return_v" id="return_v" value="resources" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</div>

<?php
Helper_Include::addStyleDeclaration( implode( '', $css ) );

$js	=	'
		(function ($){
			Joomla.submitbutton = function(task, cid) {
				if (task == "'.$this->vName.'s.delete") {
					if (confirm(Joomla.JText._("COM_CCK_CONFIRM_DELETE"))) {
						Joomla.submitform(task);
					} else {
						return false;
					}
				}
				Joomla.submitform(task);
			}
		})(jQuery);
		';
$doc->addScriptDeclaration( $js );
?>
</div>
</form>
<?php Helper_Display::quickCopyright(); ?>