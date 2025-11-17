<?php

namespace Rezky\LaravelResponseFormatter\Exception;

use Rezky\LaravelResponseFormatter\Http\Response;
use Exception;
use Illuminate\Contracts\Support\Responsable;
use Throwable;

class Error extends Exception implements Responsable
{

    protected Response $response;

    /**
     * @param string|\BackedEnum $code
     * @param array $data
     */
    public function __construct($code = '', $data=[],$message="",Throwable $previous=null)
    {
        if ($code instanceof \BackedEnum){
            $code = $code->value;
        }
        $this->response = new Response($code,$data);
        $message = $message??$this->response->getResponseMessageByCode($code);
        $this->response->setMessage($message);
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
