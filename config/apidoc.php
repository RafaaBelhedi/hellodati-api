<?php

return [

    /*
     * The output path for the generated documentation.
     */
    'output' => 'public/docs',

    /*
     * The router to be used (Laravel or Dingo).
     */
    'router' => 'laravel',

    /*
     * Generate a Postman collection in addition to HTML docs.
     */
    'postman' => [
        /*
         * Specify whether the Postman collection should be generated.
         */
        'enabled' => false,

        /*
         * The name for the exported Postman collection. Default: config('app.name')." API"
         */
        'name' => null,

        /*
         * The description for the exported Postman collection.
         */
        'description' => null,
    ],

    /*
     * The routes for which documentation should be generated.
     * Each group contains rules defining which routes should be included ('match', 'include' and 'exclude' sections)
     * and rules which should be applied to them ('apply' section).
     */
    'routes' => [
        [
            /*
             * Specify conditions to determine what routes will be parsed in this group.
             * A route must fulfill ALL conditions to pass.
             */
            'match' => [

                /*
                 * Match only routes whose domains match this pattern (use * as a wildcard to match any characters).
                 */
                'domains' => [
                    '*',
                    // 'domain1.*',
                ],

                /*
                 * Match only routes whose paths match this pattern (use * as a wildcard to match any characters).
                 */
                'prefixes' => [
                    'auth*',
                    'user*',
                    'password*',
                    'hotel*',
                    'room*',
                    'device*',
                    'post_type*',
                    'post*',
                    'extra_post*',
                    'tourist*',
                    'stay*',
                    'shopping_order*',
                    'app_version*',
                    'dati_app_version*',
                    'dati_last_version*',
                    'notif_structure*',
                    'notif_hotels_devices*',
                ],

                /*
                 * Match only routes registered under this version. This option is ignored for Laravel router.
                 * Note that wildcards are not supported.
                 */
                'versions' => [
                    'v1',
                ],
            ],

            /*
             * Include these routes when generating documentation,
             * even if they did not match the rules above.
             * Note that the route must be referenced by name here (wildcards are supported).
             */
            'include' => [
                // 'users.index', 'healthcheck*'
            ],

            /*
             * Exclude these routes when generating documentation,
             * even if they matched the rules above.
             * Note that the route must be referenced by name here (wildcards are supported).
             */
            'exclude' => [
            ],

            /*
             * Specify rules to be applied to all the routes in this group when generating documentation
             */
            'apply' => [
                /*
                 * Specify headers to be added to the example requests
                 */
                'headers' => [
                    "Accept" => "application/json",
                    "Content-Type" => "application/json",
                    'Authorization' => 'Bearer {token}',
                    // 'Api-Version' => 'v2',
                ],

                /*
                 * If no @response or @transformer declarations are found for the route,
                 * we'll try to get a sample response by attempting an API call.
                 * Configure the settings for the API call here,
                 */
                'response_calls' => [
                    /*
                     * API calls will be made only for routes in this group matching these HTTP methods (GET, POST, etc).
                     * List the methods here or use '*' to mean all methods. Leave empty to disable API calls.
                     */
                    'methods' => [],

                    /*
                     * For URLs which have parameters (/users/{user}, /orders/{id?}),
                     * specify what values the parameters should be replaced with.
                     * Note that you must specify the full parameter, including curly brackets and question marks if any.
                     */
                    'bindings' => [
                         //'{token}' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImM2M2RiYzdjYjUwMzYxMWM0MGM3ZDBjMTUwMWQzMzEwZTYxNTM1YmNjZWVmNTY0OGZjNWY3MGI0ZjE0OTQ5NGQwNmFkZTBmMjlkMDY5ODk0In0.eyJhdWQiOiIyIiwianRpIjoiYzYzZGJjN2NiNTAzNjExYzQwYzdkMGMxNTAxZDMzMTBlNjE1MzViY2NlZWY1NjQ4ZmM1ZjcwYjRmMTQ5NDk0ZDA2YWRlMGYyOWQwNjk4OTQiLCJpYXQiOjE1NTUwNjQ0NDAsIm5iZiI6MTU1NTA2NDQ0MCwiZXhwIjoxNTg2Njg2ODQwLCJzdWIiOiIxIiwic2NvcGVzIjpbIioiXX0.sARkJQW0E_PqrPT-BcH16wkclu6GBAOqLkkDKneyLJT0Tw0JLel5capRUo1ZHOw95Aq7m3PwYRntGzOWgqJdH7LRlYrjJRQ3_zjuQPakqMcUVC1V6-SSGgo2H-gOnpIYBZrCGhniIsYZBDfT_ZwWAxZVnAg3X5BG6GLm3FrL5WAl6Bq-2NE6vHOGJz4f7YpDlgVmut666-po__wz7gwG1BZ_39LKdCo0fERk6du44cnNtRFKhUt5jo4ky3DQ7Nq4SxOZZK2dFSzE8pg0EO4QJW0NCR7tNF92_tJurI0eEATt4KoZ60t24cvLhGJWgi7FukB5ckqNt1ayGuAer8NWDsQIvYwTvHUOT31eGj8CkrDnmWaQgQigG1deUqLxhW5ScZUNoO_RQrexA2cYNms3LWfiwOxh-rjudLxaLmjUJWVa0DzMqVSnAJ7AuuNwsxvaMBV_OWhQmQaQwwvqUKn8yvCniyj3rI05iPydJk8xVHuZYUm4mPM50Z_JP1TZa0JUZ41QvyYlM98Q0f4mOUHuV8I99S8MUK-FcwPwWmrRfQ7er07Cpv2x7K8Bg8jpr7H0WQeGpyHuaip9PcZ0yIbN13uaTXwXnvNPLw_5ot_ITVIpJxdJSgpvSJtuGECa-E7aKQugcMNkPx9q9-1BGWhv4cr6EG4vnE02NszjNwKopbY'
                    ],

                    /*
                     * Environment variables which should be set for the API call.
                     * This is a good place to ensure that notifications, emails
                     * and other external services are not triggered during the documentation API calls
                     */
                    'env' => [
                        'APP_ENV' => 'documentation',
                        'APP_DEBUG' => false,
                        // 'env_var' => 'value',
                    ],

                    /*
                     * Headers which should be sent with the API call.
                     */
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        //'Authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImM2M2RiYzdjYjUwMzYxMWM0MGM3ZDBjMTUwMWQzMzEwZTYxNTM1YmNjZWVmNTY0OGZjNWY3MGI0ZjE0OTQ5NGQwNmFkZTBmMjlkMDY5ODk0In0.eyJhdWQiOiIyIiwianRpIjoiYzYzZGJjN2NiNTAzNjExYzQwYzdkMGMxNTAxZDMzMTBlNjE1MzViY2NlZWY1NjQ4ZmM1ZjcwYjRmMTQ5NDk0ZDA2YWRlMGYyOWQwNjk4OTQiLCJpYXQiOjE1NTUwNjQ0NDAsIm5iZiI6MTU1NTA2NDQ0MCwiZXhwIjoxNTg2Njg2ODQwLCJzdWIiOiIxIiwic2NvcGVzIjpbIioiXX0.sARkJQW0E_PqrPT-BcH16wkclu6GBAOqLkkDKneyLJT0Tw0JLel5capRUo1ZHOw95Aq7m3PwYRntGzOWgqJdH7LRlYrjJRQ3_zjuQPakqMcUVC1V6-SSGgo2H-gOnpIYBZrCGhniIsYZBDfT_ZwWAxZVnAg3X5BG6GLm3FrL5WAl6Bq-2NE6vHOGJz4f7YpDlgVmut666-po__wz7gwG1BZ_39LKdCo0fERk6du44cnNtRFKhUt5jo4ky3DQ7Nq4SxOZZK2dFSzE8pg0EO4QJW0NCR7tNF92_tJurI0eEATt4KoZ60t24cvLhGJWgi7FukB5ckqNt1ayGuAer8NWDsQIvYwTvHUOT31eGj8CkrDnmWaQgQigG1deUqLxhW5ScZUNoO_RQrexA2cYNms3LWfiwOxh-rjudLxaLmjUJWVa0DzMqVSnAJ7AuuNwsxvaMBV_OWhQmQaQwwvqUKn8yvCniyj3rI05iPydJk8xVHuZYUm4mPM50Z_JP1TZa0JUZ41QvyYlM98Q0f4mOUHuV8I99S8MUK-FcwPwWmrRfQ7er07Cpv2x7K8Bg8jpr7H0WQeGpyHuaip9PcZ0yIbN13uaTXwXnvNPLw_5ot_ITVIpJxdJSgpvSJtuGECa-E7aKQugcMNkPx9q9-1BGWhv4cr6EG4vnE02NszjNwKopbY',
                    ],

                    /*
                     * Cookies which should be sent with the API call.
                     */
                    'cookies' => [
                        // 'name' => 'value'
                    ],

                    /*
                     * Query parameters which should be sent with the API call.
                     */
                    'query' => [
                        // 'key' => 'value',
                    ],

                    /*
                     * Body parameters which should be sent with the API call.
                     */
                    'body' => [
                        // 'key' => 'value',
                    ],
                ],
            ],
        ],
    ],

    /*
     * Custom logo path. Will be copied during generate command. Set this to false to use the default logo.
     *
     * Change to an absolute path to use your custom logo. For example:
     * 'logo' => resource_path('views') . '/api/logo.png'
     *
     * If you want to use this, please be aware of the following rules:
     * - size: 230 x 52
     */
    'logo' => 'https://hellodati.com/img/logo1.png',

    /*
     * Configure how responses are transformed using @transformer and @transformerCollection
     * Requires league/fractal package: composer require league/fractal
     *
     * If you are using a custom serializer with league/fractal,
     * you can specify it here.
     *
     * Serializers included with league/fractal:
     * - \League\Fractal\Serializer\ArraySerializer::class
     * - \League\Fractal\Serializer\DataArraySerializer::class
     * - \League\Fractal\Serializer\JsonApiSerializer::class
     *
     * Leave as null to use no serializer or return a simple JSON.
     */
    'fractal' => [
        'serializer' => null,
    ],
];
