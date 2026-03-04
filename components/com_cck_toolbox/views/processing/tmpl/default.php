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

use Joomla\CMS\HTML\HTMLHelper;

$id		=	str_replace( ' ', '_', trim( $this->pageclass_sfx ) );
$id		=	( $id ) ? 'id="'.$id.'" ' : '';
?>
<?php if ( !$this->raw_rendering ) { ?>
<div <?php echo $id; ?>class="cck_page cck-clrfix"><div>
<?php }
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_page_title ) {
	$tag		=	$this->tag_page_title;
	$class		=	trim( $this->class_page_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'><span>' . $this->title . '</span></'.$tag.'>';
}
if ( $this->show_page_desc && $this->description != '' ) {
	$description	=	HTMLHelper::_( 'content.prepare', $this->description );
	$tag_desc		=	'';

	if ( $this->tag_desc == 'div_div' ) {
		$tag_desc	=	'div';
	}
	if ( !( $this->tag_desc == 'p' && strpos( $description, '<p>' ) === false ) ) {
		$this->tag_desc	=	'div';
	}
	if ( !$this->raw_rendering ) {
		$description	=	'<'.$this->tag_desc.' class="cck_page_desc'.$this->pageclass_sfx.' cck-clrfix">' . $description . '</'.$this->tag_desc.'>';

		if ( $this->tag_desc == 'div' ) {
			$description	.=	'<div class="clr"></div>';
		}
	} else {
		$class			=	trim( $this->class_desc );
		$class			=	$class ? ' class="'.$class.'"' : '';

		if ( $tag_desc == 'div' ) {
			$description	=	'<div>'.$description.'</div>';
		}
		$description	=	'<'.$this->tag_desc.$class.'>' . $description . '</'.$this->tag_desc.'>';
	}
}
if ( $this->show_page_desc == 1 && $this->description != '' ) {
	echo $description;
}
echo $this->data;
?>
<?php if ( !$this->raw_rendering ) { ?>
</div></div>
<?php } ?>