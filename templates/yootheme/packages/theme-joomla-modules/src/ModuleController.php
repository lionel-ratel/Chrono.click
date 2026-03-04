<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use YOOtheme\Builder;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;

class ModuleController
{
    protected DatabaseDriver $db;

    public function __construct(DatabaseDriver $db)
    {
        $this->db = $db;
    }

    public function getModule(Request $request, Response $response, Builder $builder): Response
    {
        $module = $this->getInstance($request->getQueryParam('id'));

        return $response->withJson([
            'title' => $module->title,
            'params' => $module->params,
            'content' =>
                $module->module === 'mod_yootheme_builder'
                    ? $builder->load($module->content ?? '')
                    : $module->content,
        ]);
    }

    public function saveModule(
        Request $request,
        Response $response,
        Builder $builder,
        User $user
    ): Response {
        $id = $request->getParam('id');
        $data = $request->getParam('data', []);

        $request->abortIf(!$id, 400);
        $request->abortIf(
            !$user->authorise('core.edit', "com_modules.module.{$id}"),
            403,
            'Insufficient User Rights.',
        );

        // save builder content
        if (array_key_exists('content', $data)) {
            $data = [
                'content' => json_encode(
                    $builder
                        ->withParams(['context' => 'save'])
                        ->load(json_encode($data['content'])),
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ),
            ];
        }

        return $response->withJson([
            'message' => $this->saveInstance($id, $data) ? 'success' : 'fail',
        ]);
    }

    public function getModules(Request $request, Response $response, ModuleConfig $module): Response
    {
        return $response->withJson($module->modules);
    }

    public function getPositions(
        Request $request,
        Response $response,
        ModuleConfig $module
    ): Response {
        return $response->withJson($module->positions);
    }

    protected function getInstance(string $id): ?object
    {
        /** @var DatabaseQuery $query */
        $query = $this->db->createQuery();
        $query->select('*')->from('#__modules')->where('id = :id')->bind(':id', $id, 'int');

        // decode module params
        $module = $this->db->setQuery($query)->loadObject();
        $module->params = json_decode($module->params, true);

        return $module;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function saveInstance(string $id, array $data): bool
    {
        $data += ['id' => $id];
        $object = (object) $data;

        // update module params
        if (is_array($object->params ?? null)) {
            $module = $this->getInstance($id);
            $object->params = json_encode(
                $object->params + $module->params,
                JSON_UNESCAPED_SLASHES,
            );
        }

        return $this->db->updateObject('#__modules', $object, 'id');
    }
}
