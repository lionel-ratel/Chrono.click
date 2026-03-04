<?php

namespace YOOtheme\Builder\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\Filesystem\File;
use YOOtheme\Config;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\Http\Uri;
use YOOtheme\HttpClientInterface;
use YOOtheme\Path;

class BuilderController
{
    public static function loadImage(
        Request $request,
        Response $response,
        CMSApplication $joomla,
        Config $config,
        HttpClientInterface $http
    ): Response {
        $src = $request->getParam('src');
        $md5 = $request->getParam('md5');

        $params = ComponentHelper::getParams('com_media');

        $uri = new Uri($src);
        $file = basename($uri->getPath());

        if ($uri->getHost() === 'images.unsplash.com') {
            $file .= ".{$uri->getQueryParam('fm', 'jpg')}";
        }

        $path = Path::join($config('app.uploadDir'), $config('~theme.media_folder'));

        $file = File::makeSafe($file);
        $dest = Path::join($path, $file);

        try {
            $request->abortIf(
                !str_starts_with($dest, $config('app.uploadDir')),
                400,
                'Invalid path.',
            );

            // file already exists?
            while ($iterate = @md5_file($dest)) {
                if ($iterate === $md5 || is_null($md5)) {
                    return $response->withJson(Path::relative(JPATH_ROOT, $dest));
                }

                $file = preg_replace_callback(
                    '/(?:-(\d{2}))?(\.[^.]+)?$/',
                    fn($match) => sprintf('-%02d%s', intval($match[1]) + 1, $match[2] ?? ''),
                    $file,
                    1,
                );

                $dest = Path::join($path, $file);
            }

            // create file
            File::write($dest, '');

            // download file
            $tmp = Path::join($path, uniqid());
            $res = $http->get($src);

            $request
                ->abortIf(!$res->isOk(), $res->getStatusCode(), 'Download failed.')
                ->abortIf(!File::write($tmp, $res->getBody()), 500, 'Error writing file.');

            // allow .svg + mp4 files
            $params->set('upload_extensions', "{$params->get('upload_extensions')},svg,mp4");

            // raise upload_maxsize
            $params->set('upload_maxsize', 30 * 1024 * 1024);

            // add mp4 mime type
            $params->set('upload_mime', "{$params->get('upload_mime')},video/mp4");

            // ignore MIME-type check for .svg files
            $params->set(
                'ignore_extensions',
                $params->get('ignore_extensions')
                    ? "{$params->get('ignore_extensions')},svg"
                    : 'svg',
            );

            if (
                !(new MediaHelper())->canUpload([
                    'name' => $file,
                    'tmp_name' => $tmp,
                    'size' => filesize($tmp),
                ])
            ) {
                File::delete($tmp);

                $queue = $joomla->getMessageQueue();
                $message = count($queue) ? "{$file}: {$queue[0]['message']}" : '';

                $request->abort(500, $message);
            }

            // move file
            $request->abortIf(!File::move($tmp, $dest), 500, 'Error writing file.');

            return $response->withJson(Path::relative(JPATH_ROOT, $dest));
        } catch (\Exception $e) {
            // delete incomplete file
            File::delete($dest);

            throw $e;
        }
    }
}
