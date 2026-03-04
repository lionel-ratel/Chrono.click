<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Config;
use YOOtheme\File;
use YOOtheme\Path;
use YOOtheme\Theme\SystemCheck as BaseSystemCheck;
use function YOOtheme\trans;

class SystemCheck extends BaseSystemCheck
{
    protected ApiKey $apiKey;
    protected DatabaseDriver $db;

    /**
     * Constructor.
     */
    public function __construct(DatabaseDriver $db, ApiKey $apiKey, Config $config)
    {
        $this->db = $db;
        $this->apiKey = $apiKey;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function getRequirements(): array
    {
        $res = [];

        // Check for debug mode
        if (constant('JDEBUG')) {
            $res[] = trans(
                'The System debug mode generates too much session data which can lead to unexpected behavior. Disable the debug mode.',
            );
        }

        // Check for SEBLOD Plugin and setting
        $components = ComponentHelper::getComponents();
        $cck = $components['com_cck'] ?? false;
        if ($cck && $cck->enabled == 1) {
            if ($cck->getParams()->get('hide_edit_icon')) {
                $res[] = trans(
                    'The SEBLOD plugin causes the builder to be unavailable. Disable the feature <em>Hide Edit Icon</em> in the <a href="index.php?option=com_config&view=component&component=com_cck" target="_blank">SEBLOD configuration</a>.',
                );
            }
        }

        try {
            // Check for RSFirewall settings @TODO check if enabled?
            $rsfw = $this->db
                ->setQuery(
                    "SELECT value FROM #__rsfirewall_configuration WHERE name = 'verify_emails'",
                )
                ->loadResult();

            if ($rsfw == 1) {
                $res[] = trans(
                    'The RSFirewall plugin corrupts the builder content. Disable the feature <em>Convert email addresses from plain text to images</em> in the <a href="index.php?option=com_rsfirewall&view=configuration" target="_blank">RSFirewall configuration</a>.',
                );
            }
        } catch (\Exception $e) {
        }

        return array_merge($res, parent::getRequirements());
    }

    /**
     * @inheritdoc
     */
    public function getRecommendations(): array
    {
        $res = [];

        $extensions = implode(',', [
            'mod_yootheme_builder',
            'mod_yootheme_link',
            'plg_fields_location',
            'plg_fields_mediafile',
            'tpl_yootheme',
        ]);

        if ($files = File::glob("~/{,administrator/}language/*-*/*{{$extensions}}*.ini")) {
            $res[] = trans(
                'Language files found in the Joomla language directory override the extension\'s language files. Remove the files: %files%',
                [
                    '%files%' => implode(
                        '',
                        array_map(
                            fn($file) => '<br><code>' . Path::relative('~', $file) . '</code>',
                            $files,
                        ),
                    ),
                ],
            );
        }

        return array_merge($res, parent::getRecommendations());
    }

    protected function hasApiKey(): bool
    {
        return (bool) $this->apiKey->get();
    }
}
