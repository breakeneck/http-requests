# Simple http request library with xml and json support

Send json request:
```php
$jsonResponse = (new \Breakeneck\Http\Request())
    ->json()
    ->setData(['value' => 'param'])
    ->post('http://example.com/{route}', ['{route}' => 'api']);

print_r($jsonResponse->content);    
```

Send xml request:
```php
$xmlResponse = (new \Breakeneck\Http\Request())
    ->xml('root') // Parameter can be omitted, if your request doesn't contain body
    ->setData(['value' => 'param'])
    ->put('http://example.com/{route}', ['{route}' => 'api']);

print_r($xmlResponse->content);
```

Send delete request:
```php
$response = (new \Breakeneck\Http\Request())
    ->delete('http://example.com/{route}', ['{route}' => 'api']);
    
print_r($response->content);
```
If your request is get or delete, your data will be converted to query string:
```php
$response = (new \Breakeneck\Http\Request())
    ->setData(['id' => 31])
    ->get('http:://example.com');
    
print_r($response->request->getUrl() === 'http:://example.com?id=31');
```
You can also use custom headers
```php
$response = (new \Breakeneck\Http\Request())
    ->addHeaders(['Content-Type' => 'application/text'])
    ->delete('http://example.com/{route}/id/{username}', ['{route}' => 'api', '{username}' => 'breakeneck']);
```

After you get response, you will still be able to access your request object as `$xmlResponse->request` for logging or other purposes.
