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

use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$config	=	JCckDev::init( array( '42', 'select_dynamic', 'select_simple', 'text', 'wysiwyg_editor' ), true, array( 'item'=>$this->item, 'vName'=>$this->vName ) );
$cck	=	JCckDev::preload( array( 'more_toolbox_processing_title', 'core_description', 'core_state', 'more_toolbox_processing_type', 'more_toolbox_processing_scriptfile' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=edit&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
	<div class="seblod first">       
        <ul class="spe spe_title">
            <?php echo JCckDev::renderForm( $cck['more_toolbox_processing_title'], $this->item->title, $config, array( 'variation'=>'value' ) ); ?>
        </ul>
        <ul class="spe spe_folder">
            <?php echo JCckDev::renderForm( $cck['more_toolbox_processing_type'], $this->item->type, $config, array( 'variation'=>'value' ) ); ?>
        </ul>
        <ul class="spe spe_description">
        </ul>
        <ul class="spe spe_state">
        </ul>
	</div>

    <div class="seblod">
        <?php
        $this->item->scriptfile =   ( $this->item->scriptfile[0] != '/' ) ? '/'.$this->item->scriptfile : $this->item->scriptfile;
        if ( is_file( JPATH_SITE.$this->item->scriptfile ) ) {
            $options    =   new Registry( $this->item->options );
            
            include_once JPATH_SITE.$this->item->scriptfile;
        }
        ?>
    </div>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
    <input type="hidden" id="name" name="name" value="<?php echo @$this->item->name; ?>" />
    <?php
	JCckDev::validate( $config );
    echo HTMLHelper::_( 'form.token' );
	?>
</div>
</form>

<?php
Helper_Display::quickCopyright();
?>

<script type="text/javascript">
(function ($){
    JCck.Dev = {
        submit: function(task) {
            Joomla.submitbutton(task);
        }
    };
    Joomla.submitbutton = function(task) {
        if (task == "processing.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };
})(jQuery);
</script>