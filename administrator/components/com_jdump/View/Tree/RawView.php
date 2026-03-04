<?php
/**
 * J!Dump
 * @version      $Id$
 * @package      com_jdump
 * @copyright    Copyright (C) 2007 - 2018 Mathias Verraes. All rights reserved.
 * @license      GNU/GPL
 * @link         https://github.com/mathiasverraes/jdump
 */
namespace Joomla\Component\Jdump\Administrator\View\Tree;

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Jdump\Administrator\Helper\JdumpHelper;

class RawView extends BaseHtmlView
{
	public $version;

	public $mediaUri;

	public $nodeId = null;

	public $popoverId = null;

	public $autoclose = 0;

	public function display($tpl = null)
	{
		$app = Factory::getApplication();

		$params   = ComponentHelper::getParams('com_jdump');

		$this->autoclose = $params->get('autoclose', 0);

		// we need to add these paths so the component can work in both site and administrator
		$this->addTemplatePath(dirname(__FILE__, 3) . '/tmpl/tree');

		// client information (site, administrator, ... )
		$client = ApplicationHelper::getClientInfo($app->getClientID());

		// make sure we only show the component
		$app->input->set('tmpl', 'component');

		$this->application = $client->name;
		$this->version = JdumpHelper::getVersion();
		$this->mediaUri = URI::root(true) . '/media/com_jdump/';
		$this->closebutton = $app->input->getInt('closebutton', 1);

		// render tree and assign to template
		$this->tree = $this->renderTree();

		parent::display($tpl);
	}

	public function renderTree()
	{
		$output = '';

		// get the nodes from the model
		$nodes = $this->get('nodes');

		// render the nodes to <ul><li...
		foreach ($nodes as $node) {
			$output .= $this->renderNode($node);
		}
		return $output;
	}

	public function renderNode($node)
	{
		switch ($node['type'])
		{
			case 'object':
			case 'array':
				return $this->renderObjArray($node);
				break;
			case 'integer':
			case 'float':
			case 'double':
				return $this->renderNumber($node);
				break;
			case 'string':
				return $this->renderString($node);
				break;
			case 'null':
			case 'resource':
				return $this->renderNull($node);
				break;
			case 'boolean':
				return $this->renderBoolean($node);
				break;
			case 'method':
				return $this->renderMethod($node);
				break;
			case 'methods':
			case 'properties':
				return $this->renderMethProp($node);
				break;
			case 'message':
				return $this->renderMessage($node);
				break;
			default:
				return $this->renderObjArray($node);
				break;
		}
	}

	public function renderObjArray($node)
	{
		$children = count($node['children']);

		$output = '';
		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="a' . ++$this->nodeId . '">' ;
		$output .= '<span class="dumpType"> [';
		$output .= (isset($node['classname']) ? $node['classname'] . ' ' : '');
		$output .= $node['type'];
		$output .= ']</span> ';

		$output .= $node['name'];
//		$output .= $this->renderSource($node);

		$output .= $children ? '' : ' = <i>(empty)</i>';
		$output .= '</a>';

		$output .= $this->renderSource($node);

		if ($children)
		{
			$output .= '<ul>';
			foreach($node['children'] as $child)
			{
				$output .= $this->renderNode($child);
			}
			$output .= '</ul>';

		}
		$output .= '</li>';

		return $output;
	}

	public function renderNull($node)
	{
		$output = '';

		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="node_' . ++$this->nodeId . '">' ;
		$output .= '<span class="dumpType"> ['. $node['type'] . ']</span> ';
		$output .= $node['name'];
		$output .= $this->renderSource($node);
		$output .= '</a>';
		$output .= '</li>';
		return $output;
	}

	public function renderNumber($node)
	{
		$output = '';

		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="node_' . ++$this->nodeId . '">' ;
		$output .= '<span class="dumpType"> ['. $node['type'] . ']</span> ';
		$output .= $node['name'];
		$output .= ' = ' . $node['value'];
		$output .= $this->renderSource($node);
		$output .= '</a>';
		$output .= '</li>';

		return $output;

	}

	public function renderBoolean($node)
	{
		$output = '';

		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="node_' . ++$this->nodeId . '">' ;
		$output .= '<span class="dumpType"> ['. $node['type'] . ']</span> ';
		$output .= $node['name'];
		$output .= $this->renderSource($node);
		$output .= ' = ' . ($node['value'] ? 'TRUE' : 'FALSE');
		$output .= $this->renderSource($node);
		$output .= '</a>';
		$output .= '</li>';
		return $output;

	}

	public function renderString($node)
	{
		$output = '';

		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="node_' . ++$this->nodeId . '">' ;
		$output .= '<span class="dumpType"> ['. $node['type'] . ']</span> ';
		$output .= $node['name'];
		$output .= ' = "' . nl2br(htmlspecialchars($node['value'] , ENT_QUOTES)). '"';
		if (isset($node['length'])) { $output .= ' <span class="dumpString">(Length = '.intval($node['length']).')</span>'; }
		$output .= $this->renderSource($node);
		$output .= '</a>';
		$output .= '</li>';
		return $output;

	}

	public function renderMessage($node)
	{
		$output = '';

		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="node_' . ++$this->nodeId . '">' ;
		$output .= '<i>'.$node['value'].'</i>';
		$output .= $this->renderSource($node);
		$output .= '</a>';
		$output .= '</li>';
		return $output;

	}

	public function renderMethod($node)
	{
		$output = '';

		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="node_' . ++$this->nodeId . '">' . $node['name'] . '</a>';
		$output .= '</li>';

		return $output;

	}

	public function renderMethProp($node)
	{
		$output = '';

		$output .= '<li class="' . $node['type'] . '.svg">';
		$output .= '<a href="#" id="node_' . ++$this->nodeId . '">' . $node['name'] . '</a>';

		if (count($node['children'])) {
			$output .= '<ul>';
			foreach($node['children'] as $child) {
				$output .= $this->renderNode($child);
			}
			$output .= '</ul>';
		}
		$output .= '</li>';

		return $output;
	}

	public function renderSource($node)
	{
		$params   = ComponentHelper::getParams('com_jdump');

		$output = '';

		if ($node['source'] && $params->get('showOrigin', 1))
		{
			$popid = 'popover'.++$this->popoverId;

			$data  = 'data-id="i' . $popid . '" ';
			$data .= 'data-func="'. $node['source']['type'].$node['source']['function'].'" ';
			$data .= 'data-cls="'. $node['source']['class'].'" ';
			$data .= 'data-file="'. $node['source']['file'].'" ';
			$data .= 'data-line="'. $node['source']['line'].'" ';

			$output .= '<img id="'.$popid.'" title="Click for source" class="jdump-popover" src="'.$this->mediaUri.'images/drawer.svg"';
			$output .= 'alt="Tooltip" border="0" width="32" height="32" '. $data .'/></span>';

		}

		return $output;
	}
}
