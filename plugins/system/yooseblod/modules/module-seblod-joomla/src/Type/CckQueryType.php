<?php

namespace YooSeblod\MSeblod;

/**
 * Custom Query Type
 *
 * @see https://yootheme.com/support/yootheme-pro/joomla/developers-sources#add-custom-sources
 */
class CckQueryType
{
    public static function config()
    {
        $object     =   array(
                            'type' => 'CckSeblodField',
                            'args' => [
                                'id' => [
                                    'type' => 'String'
                                ],
                            ],
                            'metadata' => [                        
                                'label' => ucfirst( 'Fields' ),
                                'group' => 'SEBLOD',
                            ],
                            'extensions' => [
                                'call' => __CLASS__ . '::resolve',
                            ]
                        );

        $results['fields']['seblod']    =   $object;

        return $results;
    }

    public static function resolve($item, $args, $context, $info)
    {
    	return CckTypeProvider::get( $args['id'] );
    }
}
