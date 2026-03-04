<?php

namespace Joomla\Module\YOOthemeBuilder\Site\Dispatcher;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use YOOtheme\View;
use YOOtheme\View\HtmlElement;
use function YOOtheme\app;

class Dispatcher extends AbstractModuleDispatcher
{
    /**
     * @inheritdoc
     *
     * @return array<string, mixed>
     */
    protected function getLayoutData(): array
    {
        $data = parent::getLayoutData();
        $module = $data['module'];

        // Make module re-renderable
        $module->_builder ??= $module->content;

        $module->content = app(View::class)->builder($module->_builder, [
            'prefix' => "module-{$module->id}",
        ]);

        if ($module->content && in_array($module->position, ['top', 'bottom'])) {
            $module->content = HtmlElement::tag(
                $data['params']->get('module_tag', 'div'),
                ['id' => "module-{$module->id}", 'class' => 'builder'],
                $module->content,
            );
        }

        return $data;
    }
}
