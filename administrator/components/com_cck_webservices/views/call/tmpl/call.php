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

$config		=	JCckDev::init( array(), true, array( 'item' => $this->item, 'vName'=>$this->vName ) );
$ajax_load	=	'components/com_cck/assets/styles/seblod/images/ajax.gif';
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo Route::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&layout=call&id='.(int)$this->item->id ); ?>" method="post" id="adminForm" name="adminForm">

<div class="<?php echo $this->css['wrapper']; ?>">
    <div class="seblod first">
        <ul class="spe spe_title">
            <?php
            echo JCckDev::renderForm( 'more_webservices_call_title', $this->item->title, $config, array( 'variation'=>'disabled' ) );
            echo '<input type="hidden" id="name" name="name" value="'.$this->item->name.'" />';
            ?>
        </ul>
        <ul class="spe spe_folder">
            <?php echo JCckDev::renderForm( 'more_webservices_call_webservice', $this->item->webservice, $config, array( 'variation'=>'disabled' ) ); ?>
        </ul>
        <ul class="spe spe_state">
        </ul>
        <ul class="spe spe_description">
        </ul>
    </div>

    <?php if ( $this->item->request ) { ?>
    <div class="seblod">
        <div class="legend top left"><?php echo Text::_( 'COM_CCK_CALL_REQUEST' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
            <?php
            echo JCckDev::renderForm( 'more_webservice_request_function', $this->item->request, $config, array( 'variation'=>'disabled' ) );
            ?>
        </ul>
    </div>
    <?php } ?>

	<div class="seblod">
        <div class="legend top left"><?php echo Text::_( 'COM_CCK_CALL_RESPONSE' ); ?></div>
        <ul class="adminformlist adminformlist-2cols">
        <?php
        $app            =   Factory::getApplication();
        $fields         =   array();
        $id             =   $app->input->get( 'id', 0 );
        $ws_name        =   JCckDatabase::loadResult( 'SELECT name FROM #__cck_more_webservices_calls WHERE id = '.(int)$id );

        $webservice     =   JCckWebservice::getCall( $ws_name );

        if ( !is_object( $webservice ) ) {
            return;
        }

        PluginHelper::importPlugin( 'cck_webservice' );
        $app->triggerEvent( 'onCCK_WebserviceCall', array( &$webservice, $fields, $config ) );

        if ( $webservice->response != '' ) {
            if ( @simplexml_load_string( $webservice->response ) !== false ) {
                $xml    =   new JCckDevXml( $webservice->response );
                $output =   $xml->asIndentedXML();
            } else {
                $output =   $webservice->response;
            }
            echo '<li><pre class="cck-xml">'. htmlspecialchars( $output ).'</pre></li>';
        }
        ?>
        </ul>
    </div>
</div>

<div class="clr"></div>
<div>
    <input type="hidden" id="task" name="task" value="" />
    <input type="hidden" id="myid" name="id" value="<?php echo @$this->item->id; ?>" />
    <?php
    // echo $this->form->getInput( 'id' );
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
		if (task == "<?php echo $this->vName; ?>.cancel" || $("#adminForm").validationEngine("validate",task) === true) {
			JCck.submitForm(task, document.getElementById('adminForm'));
		}
	};
})(jQuery);
</script>