<?php
/**
* @version 			SEBLOD eCommerce 1.x
* @package			SEBLOD eCommerce Add-on for SEBLOD 3.x
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
use Joomla\CMS\Uri\Uri;

PluginHelper::importPlugin( 'cck_webservice' );

$icon_service	=	Uri::root( true ).'/administrator/components/com_cck_webservices/assets/images/16/icon-16-webservices.png';

if ( $this->definitions ) {
?>
	<div class="<?php echo $this->css['items']; ?> first">
	    <table class="<?php echo $this->css['table']; ?>">
	    <thead>
	        <tr class="half">
				<th width="60" class="center hidden-phone"></th>
				<th width="64" class="center hidden-phone"></th>
	            <th><?php echo Text::_( 'COM_CCK_WEBSERVICE' ); ?></th>
	            <th class="hidden-phone nowrap" width="22%"><?php echo Text::_( 'COM_CCK_TYPE' ); ?></th>
	            <th class="center nowrap" width="10%"><?php echo Text::_( 'COM_CCK_STATUS' ); ?></th>
				<th width="32" class="center hidden-phone"></th>
	        </tr>
	    </thead>
	    <?php
		$lang		=	Factory::getLanguage();

		foreach ( $this->definitions as $i => $def ) {
			$checkedOut		= 	! ( $def->checked_out == $userId || $def->checked_out == 0 );
			$canCheckin		=	$user->authorise( 'core.manage', 'com_checkin' ) || $def->checked_out == $userId || $def->checked_out == 0;
			$canChange		=	$user->authorise( 'core.edit.state', CCK_ADDON ) && $canCheckin;
			$canEdit		=	$user->authorise( 'core.edit', CCK_ADDON );
			$canEditOwn		=	'';
			
			$link 			=	Route::_( 'index.php?option='.$this->option.'&task=webservice.edit&id='. $def->id );
			$link3			=	'javascript: document.getElementById(\'filter_webservice\').value=\''.$def->id.'\'; document.getElementById(\'adminForm\').submit();';

			require_once JPATH_SITE.'/plugins/cck_webservice/'.$def->type.'/'.$def->type.'.php';
			$url			=	JCck::callFunc( 'plgCCK_Webservice'.$def->type, 'getInfoURL', $def );
			?>
			<tr class="row<?php echo $i % 2; ?> half">
				<td class="center hidden-phone">
					<?php echo $i + 1 . '<span class="cck-display-none">' . str_replace( 'id="cb', 'id="dcb', HTMLHelper::_( 'grid.id', $i, $def->id ) ) . '</span>'; ?>
						
				</td>
				<td width="30px" class="center hidden-phone dropdown-col">
					<?php
					if ( isset( $this->def_calls[$def->id] ) ) {
						HTMLHelper::_( '.cckactionsdropdown.addCustomLinkItem', Text::_( 'COM_CCK_FILTER' ), 'search', 'cb_link'.$i, $link3 );

						echo HTMLHelper::_( '.cckactionsdropdown.render', $this->escape( $def->title ) );
					}
					?>
				</td>
				<td align="left">
					<div class="title-left" id="title-<?php echo $def->id;?>">
						<?php
						if ( $def->checked_out ) {
							echo HTMLHelper::_( 'jgrid.checkedout', $i, $def->editor, $def->checked_out_time, 'webservices.', $canCheckin, 'dcb' )."\n";
						}
						if ( $canEdit && ! $checkedOut ) {
							echo '<a href="'.$link.'">'.$this->escape( $def->title ).'</a>';
						} else {
							echo '<span>'.$this->escape( $def->title ).'</span>';
						}
						echo '<span class="definition-desc hidden-phone">'.strip_tags( $def->description ).'</span>';
						?>
					</div>
				</td>
				<td class="hidden-phone"><?php echo Text::_( 'PLG_CCK_WEBSERVICE_'.strtoupper( $def->type ).'_LABEL2' ); ?></td>
	            <td class="center"><?php echo HTMLHelper::_( 'jgrid.published', $def->published, $i, 'webservices.', true, 'dcb' ); ?></td>
				<td class="center hidden-phone">
					<a href="javascript:void(0);" class="btn btn-micro hasTooltip" onclick="return JCck.Dev.delete('webservices.delete','dcb<?php echo $i; ?>');" title="<?php echo Text::_( 'COM_CCK_DELETE' ); ?>">
						<span class="icon-delete"></span>
					</a>
				</td>
			</tr>
			<?php
		}
	    ?>
	    </table>
	</div>
<?php } ?>