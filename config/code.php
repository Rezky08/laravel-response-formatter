<?php

return [
    'handler' => [
      'override' => false
    ],

    'code' => [
        /**
         * customize your response code here
         * use uppercase letter for key
         */
        // 000 - 100 SUCCESS
        'CODE_SUCCESS' => '000',
        'CODE_DATA_CREATED' => '001',

    ],
    /**
     * customize your response code group here
     */
    'group' =>[
        Illuminate\Http\Response::HTTP_OK => [
            'CODE_SUCCESS'
        ],
        Illuminate\Http\Response::HTTP_CREATED => [

        ],
        Illuminate\Http\Response::HTTP_UNAUTHORIZED => [

        ],
        Illuminate\Http\Response::HTTP_FORBIDDEN =>[
            /** dont remove */
            'CODE_ERROR_UNAUTHORIZED',
            'CODE_ERROR_UNAUTHENTICATED'
        ],
        Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY => [
            /** dont remove */
            'CODE_ERROR_INVALID_DATA',
            'CODE_ERROR_RESOURCE_NOT_FOUND'
        ],
        Illuminate\Http\Response::HTTP_SERVICE_UNAVAILABLE => [

        ],
        Illuminate\Http\Response::HTTP_NOT_FOUND =>[
            /** dont remove */
            'CODE_ERROR_ROUTE_NOT_FOUND'
        ],
        Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR => [
            /** dont remove */
            'CODE_ERROR'
        ]
    ]
];
