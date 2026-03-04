<?php

namespace Joomla\Plugin\Fields\MediaFile;

use Joomla\CMS\Form\Form;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;

class Mediafile extends FieldsPlugin
{
    public function onCustomFieldsPrepareDom($field, \DOMElement $parent, Form $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if ($fieldNode) {
            $fieldNode->setAttribute('type', 'Media');
            $fieldNode->setAttribute('preview', 'false');
            $fieldNode->setAttribute('types', 'images,audios,videos,documents');
        }

        return $fieldNode;
    }
}
