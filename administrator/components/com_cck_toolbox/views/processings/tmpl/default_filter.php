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

$clear	=	"document.getElementById('filter_state').value='-1';document.getElementById('filter_type').value='';";
if ( $this->js['filter'] ) {
	$doc->addScriptDeclaration( $this->js['filter'] );
}
if ( $listDir == 'asc' ) {
	$selected_asc	=	' selected="selected"';
	$selected_desc	=	'';
} else {
	$selected_asc	=	'';
	$selected_desc	=	' selected="selected"';
}

$status_filter	=	JCckDev::getForm( $cck['core_state_filter'], $this->state->get( 'filter.state' ), $config, array( 'css'=>'span12' ) );
?>

<div class="<?php echo $this->css['filter']; ?>" id="filter-bar">
	<?php include_once dirname( __DIR__, 4 ).'/com_cck/views/cck/tmpl/default_filter.php'; ?>
	<div class="<?php echo $this->css['filter_select']; ?>">
        <?php
        echo $this->html['filter_select_header'];
		echo JCckDev::getForm( $cck['more_toolbox_processing_type'], $this->state->get( 'filter.type' ), $config, array( 'selectlabel'=>'All Types',
																	   'storage_field'=>'filter_type', 'attributes'=>'onchange="this.form.submit()"', 'css'=>'span12' ) );
        echo $this->html['filter_select_separator'];
        echo $this->html['filter_select_divider'];
        echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolderFilter', 'name'=>'core_folder_filter' ), $this->state->get( 'filter.folder' ), $config, array( 'storage_field'=>'filter_folder' ) );
		echo $this->html['filter_select_separator'];
		echo $status_filter;
		echo $this->html['filter_select_separator'];
        ?>
	</div></div>
</div>