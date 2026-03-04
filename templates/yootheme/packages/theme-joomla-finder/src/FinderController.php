<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Input\Input;
use YOOtheme\File;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;
use YOOtheme\Joomla\Media;
use YOOtheme\Path;

class FinderController
{
    public static function index(Request $request, Response $response, Input $input): Response
    {
        // get media root and current path
        $root = Media::getRoot($input->getString('root', ''));
        $path = Path::join($root, $input->getString('folder', ''));

        if (!str_starts_with($path, $root)) {
            $path = $root;
        }

        $files = [];

        foreach (File::listDir($path, true) ?: [] as $file) {
            $filename = basename($file);

            // ignore hidden files
            if (str_starts_with($filename, '.')) {
                continue;
            }

            $files[] = [
                'name' => $filename,
                'path' => Path::relative($root, $file),
                'url' => Path::relative(JPATH_ROOT, $file),
                'type' => File::isDir($file) ? 'folder' : 'file',
                'size' => HTMLHelper::_('number.bytes', File::getSize($file)),
            ];
        }

        return $response->withJson($files);
    }
}
