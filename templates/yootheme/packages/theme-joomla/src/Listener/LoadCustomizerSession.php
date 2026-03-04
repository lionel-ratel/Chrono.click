<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\CMS\Application\CMSApplication;
use Joomla\Input\Input;
use Joomla\Session\SessionInterface;
use YOOtheme\Arr;
use YOOtheme\Config;

class LoadCustomizerSession
{
    public Config $config;
    public CMSApplication $joomla;

    protected string $cookie = 'yootheme_session';
    protected string $post = 'customizer_session';

    public function __construct(Config $config, CMSApplication $joomla)
    {
        $this->config = $config;
        $this->joomla = $joomla;
    }

    public function handle(): void
    {
        $input = $this->joomla->getInput();
        $session = $this->joomla->getSession();

        // If not customizer route
        if ($input->get('p') !== 'customizer') {
            if (!$this->config->get('app.isSite')) {
                return;
            }

            // Get params from request or cookie
            $params = $this->handlePost($input, $session) ?? $this->handleCookie($input, $session);

            if ($params === null) {
                return;
            }

            $this->applyConfigChanges($params['config'] ?? []);

            // Pass through e.g. page, modules and template params
            $this->config->add('req.customizer', $params);
        } else {
            // Customizer controller
            $id = $session->getId();
            $this->config->set('customizer.session', "{$id}_{$this->createSessionToken($id)}");
        }

        $this->joomla->set('caching', 0);
        $this->config->set('app.isCustomizer', true);
        $this->config->set('customizer.id', $this->config->get('theme.id'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function handlePost(Input $input, SessionInterface $session): ?array
    {
        $id = $this->parseSessionId($input->post->get($this->post) ?? '');
        $data = $input->post->getBase64('customizer');

        if (!$id || !$data) {
            return null;
        }

        // Get params and merge with customizer data from request
        $params = array_replace(
            $session->get($this->cookie) ?: [],
            json_decode(base64_decode($data), true),
        );

        $session->set($this->cookie, Arr::pick($params, ['config', 'admin', 'user_id']));

        return $params;
    }

    /**
     * Get params from frontend session.
     *
     * With shared sessions enabled, we cannot be sure, that the customizer is in the session data.
     * Due to concurrent requests, the session data might have been overwritten by another request.
     *
     * @return array<string, mixed>
     */
    protected function handleCookie(Input $input, SessionInterface $session): ?array
    {
        $id = $this->parseSessionId($input->cookie->get($this->cookie) ?? '');

        return $id ? $session->get($this->cookie, []) : null;
    }

    protected function parseSessionId(string $sessionId): ?string
    {
        if (!str_contains($sessionId, '_')) {
            return null;
        }

        [$id, $hmac] = explode('_', $sessionId, 2);

        return hash_equals($this->createSessionToken($id), $hmac) ? $id : null;
    }

    protected function createSessionToken(string $id): string
    {
        return hash_hmac('md5', $id, $this->config->get('app.secret'));
    }

    /**
     * @param array{type: string, path: list<string>, value?: mixed} $changes
     */
    protected function applyConfigChanges(array $changes): void
    {
        foreach ($changes as $change) {
            $index = '~theme.' . implode('.', $change['path']);
            if ($change['type'] === 'REMOVE') {
                $this->config->del($index);
            } else {
                $this->config->set($index, $change['value']);
            }
        }
    }
}
