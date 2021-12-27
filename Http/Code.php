<?php
namespace Rezky\ApiFormatter\Http;

use Illuminate\Http\Response as LaravelResponse;

interface Code{

    /** CODE LIST HERE */

	const CODE_ERROR = '501';
	const CODE_ERROR_INVALID_DATA = '502';
	const CODE_ERROR_RESOURCE_NOT_FOUND = '503';
	const CODE_ERROR_ROUTE_NOT_FOUND = '504';
	const CODE_ERROR_UNAUTHENTICATED = '505';
	const CODE_ERROR_UNAUTHORIZED = '506';
	const CODE_UNDEFINED_RESPONSE = '507';
	const CODE_SUCCESS = '000';
	const CODE_DATA_CREATED = '001';
	/** END CODE LIST HERE */

    /** DEFAULT CODE */
    /** END DEFAULT CODE */

    const CODE_GROUP = [
        /** CODE GROUP LIST HERE */
        /** END CODE GROUP LIST HERE */
    ];

    const DEFAULT_CODE = [
        'CODE_ERROR' => '501',
        'CODE_ERROR_INVALID_DATA' => '502',
        'CODE_ERROR_RESOURCE_NOT_FOUND' => '503',
        'CODE_ERROR_ROUTE_NOT_FOUND' => '504',
        'CODE_ERROR_UNAUTHENTICATED' => '505',
        'CODE_ERROR_UNAUTHORIZED' => '506',
        'CODE_UNDEFINED_RESPONSE' => '507'
    ];

    const DEFAULT_CODE_GROUP = [
            LaravelResponse::HTTP_OK => [

            ],
            LaravelResponse::HTTP_CREATED => [

            ],
            LaravelResponse::HTTP_UNAUTHORIZED => [
                'CODE_ERROR_UNAUTHENTICATED'
            ],
            LaravelResponse::HTTP_FORBIDDEN =>[
                /** dont remove */
                'CODE_ERROR_UNAUTHORIZED'
            ],
            LaravelResponse::HTTP_UNPROCESSABLE_ENTITY => [
                /** dont remove */
                'CODE_ERROR_INVALID_DATA',
                'CODE_ERROR_RESOURCE_NOT_FOUND'
            ],
            LaravelResponse::HTTP_SERVICE_UNAVAILABLE => [

            ],
            LaravelResponse::HTTP_NOT_FOUND =>[
                /** dont remove */
                'CODE_ERROR_ROUTE_NOT_FOUND'
            ],
            LaravelResponse::HTTP_INTERNAL_SERVER_ERROR => [
                /** dont remove */
                'CODE_ERROR'
            ]
    ];

}