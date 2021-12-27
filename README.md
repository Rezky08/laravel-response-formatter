
# API FORMATTER
This package is made so that every response that is returned has the same format.

# HOW TO INSTALL
there are several steps you have to do in order to use it properly

## Install Package
do the installation with ```composer require rezky/api-formatter```

## Add Service Provider

add provider ```Rezky\ApiFormatter\ApiFormatterServiceProvider::class``` in file ```config/app.php```
``` 
 ...
'providers' => [
 ...  
/*  
 * Application Service Providers... 
 */
 Rezky\ApiFormatter\ApiFormatterServiceProvider::class,
 ...
]
```

## Publish Config
publish config with command ``php artisan vendor:publish --provider="Rezky\ApiFormatter\ApiFormatterServiceProvider::class" --tag="config"``\
after published the config will be in ```config/code.php```

## Customize Config
if you want to change the code, do it in ```config/code.php```
you can add code and group or reduce code and group

`code` - internal code list, not http code\
`group` - internal grouping of code against http code

once added, you must convert the list to constant with the command so that it can be used\
``php artisan code:create``\
constant will be in ``Rezky\ApiFormatter\Http\Response``

### example config
```
'code' => [  
  ...
  'CODE_SUCCESS' => '000',  
  ...
],
'group' =>[  
 Illuminate\Http\Response::HTTP_OK => [  
  'CODE_SUCCESS'  
  ],
]
```
``CODE_SUCCESS`` - key label\
``000`` - internal code

 key label must prefixed with ``CODE_`` and is in one of the groups. otherwise default with http code ``500``


## End
 package ready to use

# Example
## Throw Error
If **throw error** is used, the program will stop on that line and return an error message

```
...
use Rezky\ApiFormatter\Exception\Error;
...

class TestApiController extends Controller  
{  
  public function index(){  
	  ...
	  throw Error::make(Response::CODE_ERROR_UNATHORIZED);  
	  ...
  }  
}
```

it will return
```
{
	"code": "301",
	"message": "error unathorized",
	"data": []
}
```


## Return Response
```
...
use Rezky\ApiFormatter\Http\Response;
...

class TestApiController extends Controller  
{  
  public function index(){  
  	return new Response(Response::CODE_SUCCESS,'DATA');  
  }  
}
```
it will return 
```
{
	"code": "000",
	"message": "success",
	"data": "DATA"
}
```

### response support
| class support 			| return 			|
|---------------------------|---------------------------|
| ``\Illuminate\Database\Eloquent\Model``| ``data`` => ``array`` |
| ``\Illuminate\Support\Collection``| ``data`` => ``array`` |
| ``\Illuminate\Pagination\LengthAwarePaginator``| ``data`` => ``array`` with ``paginator``|	
| ``\Illuminate\Http\Resources\Json\JsonResource``| ``data`` => ``array ``/``object`` and with/without ``paginator``|	




# Format List
field ``data`` following ``$data`` format

### Default Format
```
{
  "code": "000",
  "message": "success",
  "data": [
    "test"
  ]
}
```
### With Paginator 
```
{
  "code": "000",
  "message": "success",
  "data": [
    "ini data"
  ],
  "paginator": {
    "last_item": 1,
    "total_item": 1,
    "page": 1,
    "has_next_page": false,
    "total_page": 1,
    "per_page": 1
  }
}
```

# ADD EXCEPTION HANDLER (OPTIONAL)
you can add 
