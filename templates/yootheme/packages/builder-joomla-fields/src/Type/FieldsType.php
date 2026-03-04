<?php

namespace YOOtheme\Builder\Joomla\Fields\Type;

use Joomla\CMS\Event\CustomFields\BeforePrepareFieldEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Component\Fields\Administrator\Helper\FieldsHelper;
use Joomla\Component\Users\Administrator\Helper\UsersHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Registry\Registry;
use YOOtheme\Builder\Joomla\Source\ArticleHelper;
use YOOtheme\Builder\Joomla\Source\UserHelper;
use YOOtheme\Builder\Source;
use YOOtheme\Config;
use YOOtheme\Event;
use YOOtheme\Path;
use YOOtheme\Str;
use function YOOtheme\app;
use function YOOtheme\trans;

/**
 * @phpstan-type Field object{
 *   id: int,
 *   title: string,
 *   name: string,
 *   note: string,
 *   state: int,
 *   access: int,
 *   created_time: string,
 *   created_user_id: int,
 *   ordering: int,
 *   language: string,
 *   params: Registry,
 *   fieldparams: Registry,
 *   type: string,
 *   default_value: string,
 *   context: string,
 *   group_id: int,
 *   label: string,
 *   description: string,
 *   required: int,
 *   only_use_in_subform: int,
 *   language_title: ?string,
 *   language_image: ?string,
 *   editor: ?string,
 *   access_level: string,
 *   author_name: string,
 *   group_title: string,
 *   group_access: int,
 *   group_state: int,
 *   group_note: string,
 *   value: mixed,
 *   rawvalue: mixed
 *  } & \stdClass
 *
 * @phpstan-import-type Article from ArticleHelper
 * @phpstan-import-type ObjectConfig from Source
 * @phpstan-import-type FieldConfig from Source
 */
class FieldsType
{
    /**
     * @param list<Field> $fields
     *
     * @return ObjectConfig
     */
    public static function config(
        Source $source,
        string $type,
        string $context,
        array $fields
    ): array {
        return [
            'fields' => array_filter(
                array_reduce(
                    $fields,
                    fn($fields, $field) => $fields +
                        static::configFields(
                            $field,
                            [
                                'type' => 'String',
                                'name' => strtr($field->name, '-', '_'),
                                'metadata' => [
                                    'label' => $field->title,
                                    'group' => $field->group_title ?: trans('Fields'),
                                ],
                                'extensions' => [
                                    'call' => [
                                        'func' => [static::class, 'resolve'],
                                        'args' => ['context' => $context, 'name' => $field->name],
                                    ],
                                ],
                            ],
                            $source,
                            $context,
                            $type,
                        ),
                    [],
                ),
            ),
        ];
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return array<string, FieldConfig>
     */
    protected static function configFields(
        object $field,
        array $config,
        Source $source,
        string $context,
        string $type
    ): array {
        $config = is_callable($callback = [__CLASS__, "config{$field->type}"])
            ? $callback($field, $config, $source, $context, $type)
            : static::configGenericField($field, $config);

        $config =
            Event::emit('source.com_fields.field|filter', $config, $field, $source, $context) ?: [];

        return array_is_list($config)
            ? array_combine(array_column($config, 'name'), $config)
            : [$config['name'] => $config];
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configGenericField(object $field, array $config): array
    {
        if ($field->fieldparams->get('multiple')) {
            return ['type' => ['listOf' => 'ValueField']] + $config;
        }

        return $config;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configText(object $field, array $config): array
    {
        return array_replace_recursive($config, [
            'metadata' => ['filters' => ['limit', 'preserve']],
        ]);
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configTextarea(object $field, array $config): array
    {
        return array_replace_recursive($config, [
            'metadata' => ['filters' => ['limit', 'preserve']],
        ]);
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configEditor(object $field, array $config): array
    {
        return array_replace_recursive($config, [
            'metadata' => ['filters' => ['limit', 'preserve']],
        ]);
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configCalendar(object $field, array $config): array
    {
        return array_replace_recursive($config, ['metadata' => ['filters' => ['date']]]);
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configUser(object $field, array $config): array
    {
        return ['type' => 'User'] + $config;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return ?FieldConfig
     */
    protected static function configSubform(
        object $field,
        array $config,
        Source $source,
        string $context,
        string $type
    ): ?array {
        $fields = [];

        foreach ((array) $field->fieldparams->get('options', []) as $option) {
            $subField = static::getSubfield($option->customfield, $context);

            if (!$subField) {
                continue;
            }

            // Joomla update from 3 to 4 changed Repeatable fields to Subform fields,
            // created subform only fields for the subfields and
            // prefixed them with the field name and an underscore
            $prefix = "{$field->name}_";
            $name = str_starts_with($subField->name, $prefix)
                ? substr($subField->name, strlen($prefix))
                : $subField->name;

            $fields += static::configFields(
                $subField,
                [
                    'type' => 'String',
                    'name' => Str::snakeCase($name),
                    'metadata' => [
                        'label' => $subField->title,
                    ],
                    'extensions' => [
                        'call' => [
                            'func' => [static::class, 'resolveSubfield'],
                            'args' => ['context' => $context, 'id' => $option->customfield],
                        ],
                    ],
                ],
                $source,
                $context,
                $type,
            );
        }

        if ($fields) {
            $name = Str::camelCase(['Field', $field->name], true);
            $source->objectType($name, ['fields' => $fields]);

            return ($field->fieldparams->get('repeat')
                ? ['type' => ['listOf' => $name]]
                : [
                    'type' => $name,
                    'metadata' => array_merge($config['metadata'], ['label' => $field->title]),
                ]) + $config;
        }

        return null;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configMedia(object $field, array $config): array
    {
        return ['type' => 'MediaField'] + $config;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configMediafile(object $field, array $config): array
    {
        return ['type' => 'File'] + $config;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configSql(object $field, array $config): array
    {
        return [
            'type' => $field->fieldparams->get('multiple') ? ['listOf' => 'SqlField'] : 'SqlField',
        ] + $config;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig|list<FieldConfig>
     */
    protected static function configList(object $field, array $config): array
    {
        if ($field->fieldparams->get('multiple')) {
            return [
                [
                    'type' => ['listOf' => 'ChoiceField'],
                ] + $config,
                [
                    'name' => "{$config['name']}String",
                    'type' => 'ChoiceFieldString',
                ] + $config,
            ];
        }

        return ['type' => 'ChoiceField'] + $config;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return FieldConfig
     */
    protected static function configRadio(object $field, array $config): array
    {
        return ['type' => 'ChoiceField'] + $config;
    }

    /**
     * @param Field $field
     * @param FieldConfig $config
     *
     * @return list<FieldConfig>
     */
    protected static function configCheckboxes(object $field, array $config): array
    {
        return [
            [
                'type' => ['listOf' => 'ChoiceField'],
            ] + $config,
            [
                'name' => "{$config['name']}String",
                'type' => 'ChoiceFieldString',
            ] + $config,
        ];
    }

    /**
     * @param Article $item
     *
     * @return Article
     */
    public static function field($item)
    {
        return $item;
    }

    /**
     * @param Article $item
     * @param array<string, mixed> $args
     *
     * @return mixed
     */
    public static function resolve($item, array $args)
    {
        if (!isset($item->id)) {
            return null;
        }

        $field = static::getField($args['name'], $item, $args['context']);

        return $field ? static::resolveField($field, $field->rawvalue) : null;
    }

    /**
     * @param Field $field
     * @param mixed $value
     *
     * @return mixed
     */
    public static function resolveField(object $field, $value)
    {
        if (is_callable($callback = [static::class, "resolve{$field->type}"])) {
            return $callback($field);
        }

        return static::resolveGenericField($field, $value);
    }

    /**
     * @param Field $field
     * @param mixed $value
     *
     * @return mixed
     */
    public static function resolveGenericField(object $field, $value)
    {
        if ($field->fieldparams->exists('multiple')) {
            $value = (array) $value;

            if ($field->fieldparams['multiple']) {
                return array_map(
                    fn($value) => is_scalar($value) ? ['value' => $value] : $value,
                    $value,
                );
            } else {
                return array_first($value);
            }
        }

        return $field->rawvalue;
    }

    /**
     * @param Field $field
     *
     * @return User
     */
    public static function resolveUser(object $field)
    {
        return UserHelper::get((int) $field->rawvalue);
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveSubform(object $field)
    {
        return is_string($field->rawvalue) ? json_decode($field->rawvalue, true) : $field->rawvalue;
    }

    /**
     * @param mixed $value
     * @param array<string, mixed> $args
     *
     * @return mixed
     */
    public static function resolveSubfield($value, array $args)
    {
        $subfield = static::getSubfield($args['id'], $args['context']);

        if (!$subfield || empty($value["field{$args['id']}"])) {
            return null;
        }

        $subfield = clone $subfield;

        $subfield->rawvalue = $subfield->value = $value["field{$args['id']}"];

        return static::resolveField($subfield, $subfield->rawvalue);
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveList(object $field)
    {
        return static::resolveSelect($field, (bool) $field->fieldparams->get('multiple'));
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveCheckboxes(object $field)
    {
        return static::resolveSelect($field, true);
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveRadio(object $field)
    {
        return static::resolveSelect($field);
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveSelect(object $field, bool $multiple = false)
    {
        $result = [];

        foreach ($field->fieldparams->get('options', []) as $option) {
            if (in_array($option->value, (array) $field->rawvalue ?: [])) {
                if ($multiple) {
                    $result[] = $option;
                    continue;
                }

                return $option;
            }
        }

        return $result;
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveImagelist(object $field)
    {
        $config = app(Config::class);
        $root = Path::relative(
            $config('app.rootDir'),
            Path::join($config('app.uploadDir'), $field->fieldparams->get('directory')),
        );

        return static::resolveGenericField(
            $field,
            array_map(
                fn($value) => Path::join($root, $value),
                array_filter((array) $field->rawvalue, fn($value) => $value && $value != -1),
            ),
        );
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveMediafile(object $field)
    {
        $file = rawurldecode($field->rawvalue ?? '');
        $file = !$file || str_starts_with($file, '~') ? $file : Path::join('~', $file);
        return HTMLHelper::cleanImageURL($file)->url ?: null;
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveMedia(object $field)
    {
        $value = $field->rawvalue;

        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        if (str_starts_with($value, '{')) {
            return json_decode($value, true);
        }

        return ['imagefile' => $value, 'alt_text' => ''];
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveUsergrouplist(object $field)
    {
        return static::resolveGenericField(
            $field,
            array_intersect_key(static::getUserGroups(), array_flip((array) $field->rawvalue)),
        );
    }

    /**
     * @param Field $field
     *
     * @return mixed
     */
    public static function resolveSql(object $field)
    {
        if ($field->rawvalue === '') {
            return null;
        }

        /** @var DatabaseDriver $db */
        $db = app(DatabaseDriver::class);
        $query = $field->fieldparams->get('query', '');
        $condition = array_reduce(
            (array) $field->rawvalue,
            fn($carry, $value) => $value ? $carry . ", {$db->quote($value)}" : $carry,
        );

        // Run the query with a having condition because it supports aliases
        $db->setQuery(
            sprintf(
                'SELECT value, text FROM (%s) as a having value in (%s)',
                preg_replace('/[\s;]*$/', '', $query),
                trim($condition, ','),
            ),
        );

        $items = $db->loadObjectList();

        return $field->fieldparams->get('multiple') ? $items : array_last($items);
    }

    /**
     * @return array<string, string>
     */
    protected static function getUserGroups(): array
    {
        $data = [];

        foreach (UsersHelper::getGroups() as $group) {
            $data[$group->value] = Text::_(preg_replace('/^(- )+/', '', $group->text));
        }

        return $data;
    }

    /**
     * @param ?Article $item
     *
     * @return ?Field $field
     */
    public static function getField(string $name, $item, string $context)
    {
        $fields = static::getFields($item, $context);

        return $fields[$name] ?? null;
    }

    /**
     * @param ?Article $item
     *
     * @return array<string, Field>
     */
    protected static function getFields($item, string $context)
    {
        if (isset($item->_fields)) {
            return $item->_fields;
        }

        PluginHelper::importPlugin('fields');

        $fields = [];

        foreach ($item->jcfields ?? FieldsHelper::getFields($context, $item) as $field) {
            if ($item && !isset($item->jcfields)) {
                Factory::getApplication()
                    ->getDispatcher()
                    ->dispatch(
                        'onCustomFieldsBeforePrepareField',
                        new BeforePrepareFieldEvent('onCustomFieldsBeforePrepareField', [
                            'context' => $context,
                            'item' => $item,
                            'subject' => $field,
                        ]),
                    );
            }

            $fields[$field->name] = $field;
        }

        if (isset($item)) {
            $item->_fields = $fields;
        }

        return $fields;
    }

    /**
     * @param string $id
     *
     * @return ?Field
     */
    public static function getSubfield($id, string $context)
    {
        static $fields = [];

        if (!isset($fields[$context])) {
            $fields[$context] = [];

            foreach (FieldsHelper::getFields($context, null, false, null, true) as $field) {
                $fields[$context][$field->id] = $field;
            }
        }

        return $fields[$context][$id] ?? null;
    }
}
