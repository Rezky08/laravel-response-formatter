<?php

return [
    /**
     * customize your response code here
     */
    'code' => [
        // 000 - 100 SUCCESS
        'CODE_SUCCESS' => '000',
        'CODE_DATA_CREATED' => '001',
        // 101 - 600 FAILED
        'CODE_TIMEOUT' => '111',
        'CODE_ERROR' => '300',
        'CODE_ERROR_INVALID_DATA' => '301',
        'CODE_ERROR_INVALID_FILE' => '302',
        'CODE_ERROR_UNAUTHORIZED' => '402',
        'CODE_ERROR_UNAUTHENTICATED' => '403',
        'CODE_ERROR_RESOURCE_NOT_FOUND' => '504',

        /** Keep code exists */
        'CODE_ERROR_ROUTE_NOT_FOUND' => '503',
        'CODE_ERROR_DATABASE_TRANSACTION' => '505',
        'CODE_UNDEFINED_RESPONSE' => '506'
    ],
    /**
     * customize your response code group here
     */
    'group' =>[
        Illuminate\Http\Response::HTTP_OK => [
            'CODE_SUCCESS'
        ],
        Illuminate\Http\Response::HTTP_CREATED => [
            'CODE_DATA_CREATED'
            /** Keep code exists */
        ],
        Illuminate\Http\Response::HTTP_UNAUTHORIZED => [
            'CODE_ERROR_UNAUTHORIZED',
            'CODE_ERROR_UNAUTHENTICATED'
            /** Keep code exists */
        ],
        Illuminate\Http\Response::HTTP_FORBIDDEN =>[
            /** Keep code exists */
        ],
        Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY => [
            'CODE_ERROR_INVALID_DATA',
            'CODE_ERROR_RESOURCE_NOT_FOUND',
            'CODE_ERROR_INVALID_FILE'
            /** Keep code exists */
        ],
        Illuminate\Http\Response::HTTP_SERVICE_UNAVAILABLE => [
            /** Keep code exists */
            'CODE_UNDEFINED_RESPONSE'
        ],
        Illuminate\Http\Response::HTTP_NOT_FOUND =>[
            /** Keep code exists */
            'CODE_ERROR_ROUTE_NOT_FOUND'
        ],
        Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR => [
            /** Keep code exists */
            'CODE_ERROR_DATABASE_TRANSACTION'
        ]
    ]
];
