<?php

namespace Joomla\Plugin\Fields\Location;

use DOMElement;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;

class Location extends FieldsPlugin
{
    /**
     * Transforms the field into a DOM XML element and appends it as a child on the given parent.
     *
     * @param   \stdClass    $field   The field.
     * @param   DOMElement  $parent  The field node parent.
     * @param   Form        $form    The form.
     *
     * @return  DOMElement
     *
     * @since   3.7.0
     */
    public function onCustomFieldsPrepareDom($field, DOMElement $parent, Form $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if ($fieldNode) {
            FormHelper::addFieldPrefix('Joomla\Plugin\Fields\Location\Fields');
        }

        return $fieldNode;
    }
}
