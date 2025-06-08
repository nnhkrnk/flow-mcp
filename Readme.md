## 環境構築

### `Configration/`に`Settings.file`を作成
```
Neos:
  Flow:
    mvc:
      routes:
        'Neos.Flow': TRUE
    core:
      phpBinaryPathAndFilename: 'C:/PHPファイルまでのパス/php.exe'
```

### composer install
```
$ composer install
```

### MCPサーバを立てる
```
$ ./flow server:run
```

### 動作確認

```
$ curl -i -X GET 'http://localhost:8081/sse'
HTTP/1.1 200 OK
Host: localhost:8081
Date: Sat, 31 May 2025 17:58:33 GMT
Connection: close
X-Powered-By: PHP/8.2.17
Content-Type: application/json
X-Flow-Powered: Flow/8.3
Content-Length: 49

{"status":"OK","message":"MCP server is running"}
```

### Functional Testing

```
$ .\bin\phpunit -c .\Build\BuildEssentials\PhpUnit\FunctionalTests.xml .\Packages\Application\NNHKRNK.MCP\Tests\Functional --debug
```
