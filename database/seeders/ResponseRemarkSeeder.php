<?php

namespace Rezky\LaravelResponseFormatter\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\Response as HttpResponse;
use Rezky\LaravelResponseFormatter\Models\ResponseRemark;

class ResponseRemarkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $response_remarks = [
            [
                "resp_code" => "501",
                "resp_desc" => "Internal Server Error",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_ERROR,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_SERVER,
                "const_name" => "INTERNAL_SERVER_ERROR",
                "http_code" => (string)HttpResponse::HTTP_INTERNAL_SERVER_ERROR,
            ],
            [
                "resp_code" => "504",
                "resp_desc" => "Route Not Found",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_ERROR,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_SERVER,
                "const_name" => "ROUTE_NOT_FOUND",
                "http_code" => (string)HttpResponse::HTTP_BAD_REQUEST,
            ],
            [
                "resp_code" => "503",
                "resp_desc" => "Resource Not Found",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_ERROR,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_DATA,
                "const_name" => "RESOURCE_NOT_FOUND",
                "http_code" => (string)HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],
            [
                "resp_code" => "502",
                "resp_desc" => "Invalid Data",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_ERROR,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_DATA,
                "const_name" => "INVALID_DATA",
                "http_code" => (string)HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],
            [
                "resp_code" => "505",
                "resp_desc" => "Unauthenticated",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_ERROR,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_AUTH,
                "const_name" => "UNAUTHENTICATED",
                "http_code" => (string)HttpResponse::HTTP_FORBIDDEN,
            ],
            [
                "resp_code" => "506",
                "resp_desc" => "Unauthorized",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_ERROR,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_AUTH,
                "const_name" => "UNAUTHORIZED",
                "http_code" => (string)HttpResponse::HTTP_FORBIDDEN,
            ],
            [
                "resp_code" => "507",
                "resp_desc" => "Undefined Response",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_ERROR,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_DATA,
                "const_name" => "UNDEFINED_RESPONSE",
                "http_code" => (string)HttpResponse::HTTP_UNPROCESSABLE_ENTITY,
            ],
            [
                "resp_code" => "001",
                "resp_desc" => "Success",
                "resp_type" => ResponseRemark::RESPONSE_TYPE_INFO,
                "resp_group" => ResponseRemark::RESPONSE_GROUP_DATA,
                "const_name" => "SUCCESS",
                "http_code" => (string)HttpResponse::HTTP_OK,
            ]
        ];

        foreach ($response_remarks as $response_remark) {
            $respRemark = new ResponseRemark($response_remark);
            $respRemark->save();
        }

    }
}
