<?php

namespace Rezky\ApiFormatter\Exception;

use Rezky\ApiFormatter\Http\Response;
use Exception;
use Illuminate\Contracts\Support\Responsable;
use Throwable;

class Error extends Exception implements Responsable
{

    protected Response $response;

    /**
     * @param string $code
     * @param array $data
     */
    public function __construct($code = '', $data=[],Throwable $previous=null)
    {
        $this->response = new Response($code,$data);
        parent::__construct($this->response->getResponseMessageByCode($code), $code, $previous);
    }

    /**
     * @param string $code
     * @param array $data
     * @return static
     */
    public static function make($code = '', $data=[]):self{
        return new static($code,$data);
    }

    /** {@inheritDoc} */
    public function toResponse($request)
    {
        return $this->response->toResponse($request);
    }
}
