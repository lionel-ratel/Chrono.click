<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Language\Text;
use YOOtheme\Config;
use YOOtheme\Theme\I18nConfig;

class LoadThemeI18n
{
    public Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function handle(): void
    {
        $this->config->add('theme.data.i18n', [
            'close' => ['label' => Text::_('TPL_YOOTHEME_CLOSE')],
            'totop' => ['label' => Text::_('TPL_YOOTHEME_BACK_TO_TOP')],
            'marker' => ['label' => Text::_('TPL_YOOTHEME_OPEN')],
            'navbarToggleIcon' => ['label' => Text::_('TPL_YOOTHEME_OPEN_MENU')],
            'paginationPrevious' => ['label' => Text::_('TPL_YOOTHEME_PREVIOUS_PAGE')],
            'paginationNext' => ['label' => Text::_('TPL_YOOTHEME_NEXT_PAGE')],
            'searchIcon' => [
                'toggle' => Text::_('TPL_YOOTHEME_OPEN_SEARCH'),
                'submit' => Text::_('TPL_YOOTHEME_SUBMIT_SEARCH'),
            ],
            'slider' => [
                'next' => Text::_('TPL_YOOTHEME_NEXT_SLIDE'),
                'previous' => Text::_('TPL_YOOTHEME_PREVIOUS_SLIDE'),
                'slideX' => Text::_('TPL_YOOTHEME_SLIDE_%S'),
                'slideLabel' => Text::_('TPL_YOOTHEME_%S_OF_%S'),
            ],
            'slideshow' => [
                'next' => Text::_('TPL_YOOTHEME_NEXT_SLIDE'),
                'previous' => Text::_('TPL_YOOTHEME_PREVIOUS_SLIDE'),
                'slideX' => Text::_('TPL_YOOTHEME_SLIDE_%S'),
                'slideLabel' => Text::_('TPL_YOOTHEME_%S_OF_%S'),
            ],
            'lightboxPanel' => [
                'next' => Text::_('TPL_YOOTHEME_NEXT_SLIDE'),
                'previous' => Text::_('TPL_YOOTHEME_PREVIOUS_SLIDE'),
                'slideLabel' => Text::_('TPL_YOOTHEME_%S_OF_%S'),
                'close' => Text::_('TPL_YOOTHEME_CLOSE'),
            ],
        ]);
    }

    /**
     * @param I18nConfig $config
     */
    public static function handleConfig($config): void
    {
        $config->merge([
            'consent' => [
                'button_accept' => Text::_('TPL_YOOTHEME_CONSENT_BUTTON_ACCEPT'),
                'text_openstreetmap' => Text::_('TPL_YOOTHEME_CONSENT_OPEN_STREET_MAP'),
                'text_google_maps' => Text::_('TPL_YOOTHEME_CONSENT_GOOGLE_MAPS'),
                'text_vimeo' => Text::_('TPL_YOOTHEME_CONSENT_VIMEO'),
                'text_youtube' => Text::_('TPL_YOOTHEME_CONSENT_YOUTUBE'),
                'service_google_advertising' => Text::_(
                    'TPL_YOOTHEME_CONSENT_SERVICE_GOOGLE_ADVERTISING',
                ),
                'service_google_analytics' => Text::_(
                    'TPL_YOOTHEME_CONSENT_SERVICE_GOOGLE_ANALYTICS',
                ),
                'service_google_maps' => Text::_('TPL_YOOTHEME_CONSENT_SERVICE_GOOGLE_MAPS'),
                'service_openstreetmap' => Text::_('TPL_YOOTHEME_CONSENT_SERVICE_OPEN_STREET_MAP'),
                'service_vimeo' => Text::_('TPL_YOOTHEME_CONSENT_SERVICE_VIMEO'),
                'service_youtube' => Text::_('TPL_YOOTHEME_CONSENT_SERVICE_YOUTUBE'),
            ],
        ]);
    }
}
