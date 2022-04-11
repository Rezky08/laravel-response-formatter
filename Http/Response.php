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
use Rezky\LaravelResponseFormatter\Exception\Error;

class Response implements Responsable,Code
{

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
     *
     * @param string $code
     * @param array|Model $data
     */
    function __construct($code='',$data=[],$message="")
    {
        $this->data = $data;
        $this->code = $code;
        $this->message = $message;

        if (config('code') !== null){
            $this->codeList = config('code.code');
            $this->codeGroupList = config('code.group');
        }else{
            throw new \Error("cannot find 'code' config, make sure 'code' already in your 'config' directory");
        }
    }

    public static function getDefaultCode():array{
        return self::DEFAULT_CODE;
    }

    public static function getDefaultGroup():array{
        return self::DEFAULT_CODE_GROUP;
    }

    public function getAvailableCode():array{
        /** @var \ReflectionClass $oClass */

        $codes = [];
        foreach ($this->codeList as $key => $value){
            if (strpos($key,'CODE_')>-1){
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

    public function getResponseMessageByCode(&$code){
        $label = $this->getResponseLabelFromCode($code);
        $label = str_replace('CODE_','',$label);
        $label = str_replace('_',' ',$label);
        $label = strtolower($label);
        return $label;
    }

    public function getResponseGroupByCode($code){
        $codeLabel = $this->getResponseLabelFromCode($code);
        foreach ($this->codeGroupList as $group => $value){
            if (in_array($codeLabel,$value)){
                return $group;
            }
        }
        return LaravelResponse::HTTP_INTERNAL_SERVER_ERROR;
    }

    public function getResponseLabelFromCode(&$code){
        $codes = $this->getAvailableCode();
        if (!key_exists($code,$codes)){
            $code = self::CODE_UNDEFINED_RESPONSE;
            $label = 'CODE_UNDEFINED_RESPONSE';
        }else{
            $label = $codes[$code];
        }
        return $label;
    }

    public function formatData($data,$code){
        $message = $this->message ?: $this->getResponseMessageByCode($code);
        $response = [
            'code'    =>  $code,
            'message' =>  $message,
            'data'    =>  $data,
        ];
        switch (true){
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
                $paginationOptions = [
                    'last_item' => $data->lastItem(),
                    'total_item' => $data->total(),
                    'page' => $data->currentPage(),
                    'has_next_page' => $data->hasMorePages(),
                    'total_page' => $data->lastPage(),
                    'per_page' => (int) $data->perPage(),
                ];
                $response['paginator'] = $paginationOptions;

                /** @var Collection $data */
                $data = $data->getCollection();

                $response['data']= $data->toArray();
                break;
            case $data instanceof JsonResource:
                /** @var JsonResource $data */
                if ($data->resource instanceof LengthAwarePaginator){
                    /** @var LengthAwarePaginator $resource */
                    $resource = $data->resource;
                    $paginationOptions = [
                        'last_item' => $resource->lastItem(),
                        'total_item' => $resource->total(),
                        'page' => $resource->currentPage(),
                        'has_next_page' => $resource->hasMorePages(),
                        'total_page' => $resource->lastPage(),
                        'per_page' => (int) $resource->perPage(),
                    ];
                    $response['paginator'] = $paginationOptions;

                    /** @var Collection $resource */
                    $resource = $resource->getCollection();
                    $response['data']=$resource->toArray();
                }else{
                    $data = $data->response();
                    $response['data'] = $data->getData(true);
                    if (isset($response['data']['data'])){
                        $response['data'] = $response['data']['data'];
                    }
                }
                break;
        }

        return $response;
    }

    public function responseJson($data,$code):JsonResponse
    {
        $httpStatus = $this->getResponseGroupByCode($code);
        $response = $this->formatData($data,$code);

        return response()->json($response,$httpStatus);
    }

    /** {@inheritDoc} */
    public function toResponse($request)
    {
        if ($request->expectsJson()){
            return $this->responseJson($this->data,$this->code);
        }
    }
}
