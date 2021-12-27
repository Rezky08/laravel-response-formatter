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

        ],
        Illuminate\Http\Response::HTTP_UNPROCESSABLE_ENTITY => [

        ],
        Illuminate\Http\Response::HTTP_SERVICE_UNAVAILABLE => [

        ],
        Illuminate\Http\Response::HTTP_NOT_FOUND =>[

        ],
        Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR => [

        ]
    ]
];
