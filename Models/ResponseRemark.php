<?php

namespace Rezky\LaravelResponseFormatter\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ResponseRemark extends Model
{
    use HasFactory;

    protected $table = 'response_remarks';
    public $timestamps = false;

    const RESPONSE_TYPE_ERROR = 'E';
    const RESPONSE_TYPE_INFO = 'I';
    const RESPONSE_TYPE_WARNING = 'W';
    const RESPONSE_TYPE_EXCEPTION = 'X';

    const RESPONSE_GROUP_DATA = 'data';
    const RESPONSE_GROUP_AUTH = 'auth';
    const RESPONSE_GROUP_SERVER = 'server';

    public static function getAvailableResponseTypes(): array
    {
        $class = new \ReflectionClass(self::class);
        $consts = $class->getConstants();
        $consts = array_filter($consts, function ($value,$key) {
            return Str::startsWith($key, 'RESPONSE_TYPE_');
        },ARRAY_FILTER_USE_BOTH);
        return $consts;
    }
    public static function getAvailableResponseGroup(): array
    {
        $class = new \ReflectionClass(self::class);
        $consts = $class->getConstants();
        $consts = array_filter($consts, function ($value,$key) {
            return Str::startsWith($key, 'RESPONSE_GROUP_');
        },ARRAY_FILTER_USE_BOTH);
        return $consts;
    }

}
