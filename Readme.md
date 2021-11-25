# PHP-Xdebug

## Useage

- 修改`/ConfigurationFile/php/php.ini`

```php
[xdebug] 
zend_extension=/usr/lib/php/20170718/xdebug.so 
xdebug.remote_enable=1 
xdebug.mode = debug 
xdebug.client_host= 10.11.201.141<IDE的IP地址>
xdebug.client_port = 9001<IDE监听的端口>
xdebug.remote_autostart = 1 
xdebug.start_with_request = yes 
xdebug.log=/tmp/xdebug-local.log
xdebug.remote_autostart=1
```

- 修改laugh.json

```json
{
    "version": "0.2.0", 
    "configurations": [ 
        { 
            "name": "Listen for XDebug", 
            "type": "php", 
            "request": "launch", 
            "port": 9001<IDE监听的端口>, 
            "hostname": "10.11.201.141<IDE的IP地址>", 
            "pathMappings": { 
                "/var/www/html/": "${workspaceRoot}/" 
            }, 
            "log": true 
            },
            { 
            "name": "Launch currently open script", 
            "type": "php", 
            "request": "launch", 
            "program": "${file}", 
            "cwd": "${fileDirname}", 
            "port": 9001
            } 
            ] 
    }
```

- 服务默认信息

| 服务    | 账户  | 密码        | 端口  |
| ------- | ----- | ----------- | ----- |
| Mysql   | g2mtu | 123456      | 3307  |
| SSH     | root  | xdebugadmin | 2222  |
| Apache2 |       |             | 10086 |

- 启动

```
docker-compose up -d --build
```

- 注意网站根目录映射在当前文件夹下的`file`