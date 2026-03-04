<?php

namespace YOOtheme\Builder\Joomla\Source\Listener;

use Joomla\CMS\Language\Text;
use stdClass;
use YOOtheme\Builder;
use YOOtheme\Builder\Templates\TemplateHelper;
use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\Theme\Joomla\LoadTemplateEvent;
use function YOOtheme\app;

/**
 * @phpstan-type Template array{
 *  type: ?string,
 *  query: Query,
 *  params?: Params,
 *  editUrl?: ?string
 * }
 *
 * @phpstan-type Query array{
 *  catid?: callable,
 *  tag?: callable,
 *  pages?: string,
 *  lang: string,
 *  include_child_categories?: string,
 *  include_child_tags?: string
 * }
 *
 * @phpstan-type Params array{
 *  item?: stdClass,
 *  category?: string|object,
 *  items?: array<mixed>,
 *  pagination?: mixed,
 *  tags?: list<object>,
 *  search?: array{searchword: string, total: mixed}
 * }
 */
class LoadTemplate
{
    public Config $config;
    public Builder $builder;

    public function __construct(Config $config, Builder $builder)
    {
        $this->config = $config;
        $this->builder = $builder;
    }

    /**
     * @param LoadTemplateEvent $event
     */
    public function handle($event): void
    {
        /** @var Template $view */
        $view = Event::emit('builder.template', $event);

        if (empty($view['type'])) {
            return;
        }

        // get template from customizer request?
        $template = $this->config->get('req.customizer.template');

        if ($this->config->get('app.isCustomizer')) {
            $this->config->set('customizer.view', $view['type']);
        }

        if ($this->config->get('app.isBuilder') && empty($template)) {
            return;
        }

        // get visible template
        $visible = app(TemplateHelper::class)->match($view);

        // set template identifier
        if ($this->config->get('app.isCustomizer')) {
            $this->config->add('customizer.template', [
                'id' => $template['id'] ?? null,
                'visible' => $visible['id'] ?? null,
            ]);
        }

        if ($template ??= $visible) {
            // get output from builder
            $output = $this->builder->render(
                json_encode($template['layout'] ?? []),
                ($view['params'] ?? []) + [
                    'prefix' => "template-{$template['id']}",
                    'template' => $template['type'],
                ],
            );

            // append frontend edit button?
            if ($output && isset($view['editUrl']) && !$this->config->get('app.isCustomizer')) {
                $output .=
                    "<a style=\"position: fixed!important\" class=\"uk-position-medium uk-position-bottom-right uk-position-z-index uk-button uk-button-primary\" href=\"{$view['editUrl']}\">" .
                    Text::_('JACTION_EDIT') .
                    '</a>';
            }

            $event->setOutput($output ?? '');
            $this->config->set('app.isBuilder', true);
            $this->config->set('app.template', $template);
        }
    }
}
