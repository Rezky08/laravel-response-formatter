<?php

namespace Rezky\LaravelResponseFormatter\Http;

use Dflydev\DotAccessData\Data;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as LaravelResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Rezky\LaravelResponseFormatter\Exception\Error;

class Response implements Responsable, Code
{

    const PAGINATOR_TYPE_DEFAULT = 1;
    const PAGINATOR_TYPE_DATA_TABLE = 2;

    /**
     * @var array|Model|LengthAwarePaginator $data
     */
    protected $data;

    /**
     * @var string $code
     */
    protected string $code;

    /**
     * @var array
     */
    protected array $codeList;

    /**
     * @var array
     */
    protected array $codeGroupList;

    /**
     * @var string
     */
    protected string $message;

    /**
     * @var int
     */
    protected int $paginatorType;

    /**
     *
     * @param string $code
     * @param array|Model $data
     */
    function __construct($code = '', $data = [], $message = "", $paginatorType = self::PAGINATOR_TYPE_DEFAULT)
    {
        $this->data = $data;
        $this->code = $code;
        $this->message = $message;
        $this->paginatorType = $paginatorType;

        if (config('code') !== null) {
            $this->codeList = config('code.code');
            $this->codeGroupList = config('code.group');
        } else {
            throw new \Error("cannot find 'code' config, make sure 'code' already in your 'config' directory");
        }
    }

    public static function getDefaultCode(): array
    {
        return self::DEFAULT_CODE;
    }

    public static function getDefaultGroup(): array
    {
        return self::DEFAULT_CODE_GROUP;
    }

    public function getAvailableCode(): array
    {
        /** @var \ReflectionClass $oClass */

        $codes = [];
        foreach ($this->codeList as $key => $value) {
            if (strpos($key, 'CODE_') > -1) {
                $codes[$value] = $key;
            }
        }
        return $codes;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    public function getResponseMessageByCode(&$code)
    {
        $label = $this->getResponseLabelFromCode($code);
        $label = str_replace('CODE_', '', $label);
        $label = str_replace('_', ' ', $label);
        $label = strtolower($label);
        return $label;
    }

    public function getResponseGroupByCode($code)
    {
        $codeLabel = $this->getResponseLabelFromCode($code);
        foreach ($this->codeGroupList as $group => $value) {
            if (in_array($codeLabel, $value)) {
                return $group;
            }
        }
        return LaravelResponse::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function getResponseLabelFromCode(&$code)
    {
        $codes = $this->getAvailableCode();
        if (!key_exists($code, $codes)) {
            $code = self::CODE_UNDEFINED_RESPONSE;
            $label = 'CODE_UNDEFINED_RESPONSE';
        } else {
            $label = $codes[$code];
        }
        return $label;
    }

    public function formatPaginator(&$response, $data, $paginatorType = self::PAGINATOR_TYPE_DEFAULT)
    {
        /** @var LengthAwarePaginator $data */
        switch (true) {
            case $paginatorType === self::PAGINATOR_TYPE_DEFAULT :
                $response['paginator'] = [
                    'last_item' => $data->lastItem(),
                    'total_item' => $data->total(),
                    'page' => $data->currentPage(),
                    'has_next_page' => $data->hasMorePages(),
                    'total_page' => $data->lastPage(),
                    'per_page' => (int)$data->perPage(),
                ];
                return $response['paginator'];
            case $paginatorType === self::PAGINATOR_TYPE_DATA_TABLE :
                $params = Request::all();
                $draw = (int)$params['draw'];
                $paginator = [
                    "draw" => $draw + 1,
                    "page" => $data->currentPage(),
                    "pages" => $data->lastPage(),
                    "start" => $data->firstItem(),
                    "end" => $data->lastItem(),
                    "length" => (int)$data->perPage(),
                    "recordsTotal" => $data->total(),
                    "recordsDisplay" => $data->total(),
                    "recordsFiltered" => $data->total(),
                    "serverSide" => true,
                ];
                $response = array_merge($paginator);
                return $paginator;
        }
    }

    public function formatData($data, $code, $paginatorType)
    {
        $message = $this->message ?: $this->getResponseMessageByCode($code);
        $response = [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
        switch (true) {
            case $data instanceof Model:
                /** @var Model $data */
                $response['data'] = $data->toArray();
                break;
            case $data instanceof Collection:
                /** @var Collection $data */
                $response['data'] = $data->toArray();
                break;
            case $data instanceof LengthAwarePaginator:
                /** @var LengthAwarePaginator $data */
                $this->formatPaginator($response, $data, $paginatorType);

                /** @var Collection $data */
                $data = $data->getCollection();

                $response['data'] = $data->toArray();
                break;
            case $data instanceof JsonResource:
                /** @var JsonResource $data */
                if ($data->resource instanceof LengthAwarePaginator) {
                    /** @var LengthAwarePaginator $resource */
                    $resource = $data->resource;
                    $this->formatPaginator($response, $resource, $paginatorType);


                    /** @var Collection $resource */
                    $resource = $resource->getCollection();
                    $response['data'] = $resource->toArray();
                } else {
                    $data = $data->response();
                    $response['data'] = $data->getData(true);
                    if (isset($response['data']['data'])) {
                        $response['data'] = $response['data']['data'];
                    }
                }
                break;
        }

        return $response;
    }

    public function responseJson($data, $code): JsonResponse
    {
        $httpStatus = $this->getResponseGroupByCode($code);
        $response = $this->formatData($data, $code, $this->paginatorType);
        return response()->json($response, $httpStatus);
    }

    /** {@inheritDoc} */
    public function toResponse($request)
    {
        if ($request->expectsJson()) {
            return $this->responseJson($this->data, $this->code);
        }
    }
}
