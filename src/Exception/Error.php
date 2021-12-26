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
    public function __construct($code = '', $data=[],$message="",Throwable $previous=null)
    {
        $this->response = new Response($code,$data);
        $message = $message??$this->response->getResponseMessageByCode($code);
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param string $code
     * @param array $data
     * @return static
     */
    public static function make($code = '', $data=[],$message=""):self{
        return new static($code,$data,$message);
    }

    /** {@inheritDoc} */
    public function toResponse($request)
    {
        return $this->response->toResponse($request);
    }
}
