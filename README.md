# HHTTP

### 简介
各类io扩展
- http
- 日志记录

### 安装:
```bash
composer require hhttp/io
```

### 日志配置
filesystems.php 增加配置:
```php
'debug'  => [
    'driver' => 'daily',
    'path'   => storage_path('logs/io/laravel.log'),
    'level'  => 'debug',
    'days'   => 30, # 保留30天 根据具体情况设置
],
```

### http客户端调用(与GuzzleHttp用法一致；增加了请求日志记录)
```php
$uri = config('http_service.inner_service') . '/api/test';
$res = (new HHttp())->post(
    uri: $uri,
    options: [
        'form_params' => $requestData
    ]
);
$data = $res->getBody()->getContents()
#----------------------------------------------------------------
$uri = config('http_service.inner_service') . '/api/test';
$res = (new HHttp())->post(
    uri: $uri,
    options: [
        'headers' => [
            'Content-Type' => 'application/json'
        ],
        'json' => [
            'card_no' => $account_id,
        ],
    ]
);
$data = $res->getBody()->getContents()

$uri = rtrim(config('apis.family_doctor.url'), DIRECTORY_SEPARATOR) . '/innerapi/xxx';
$res = (new HHttp())->get(
    uri: $uri,
    options: ['query' => $sign_data]
);
$data = json_decode($res->getBody()->getContents(),true);
```


```
- 配置收集的数据清理脚本
- \App\Console\Kernel::schedule方法中增加
```php
    # 应用hoo自定义的定时
    (new \hoo\io\common\Console\Kernel())->schedule($schedule);
```

