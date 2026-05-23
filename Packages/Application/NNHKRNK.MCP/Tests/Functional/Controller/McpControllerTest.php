<?php

namespace NNHKRNK\MCP\Tests\Functional\Controller;

use Neos\Flow\Tests\FunctionalTestCase;

class McpControllerTest extends FunctionalTestCase
{
    /**
     * @var bool
     */
    protected $testableSecurityEnabled = true;

    /**
     * @var ServerRequestFactoryInterface
     */
    protected $serverRequestFactory;


    //////////////////////////////////////////////////////////////////////
    // MCPサーバのヘルスチェックを行うAPIのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: ヘルスチェックAPIが200 OKを返すことを確認する
     */
    public function testHealthCheckAction(): void
    {
        $response = $this->browser->request('http://localhost:8081/sse');
        self::assertSame(200, $response->getStatusCode());
    }

    /**
     * 正常系: ヘルスチェックAPIのレスポンスボディが正しい形式であることを確認する
     */
    public function testHealthCheckResponseBody(): void
    {
        $response = $this->browser->request('http://localhost:8081/sse');
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('status', $responseBody);
        self::assertArrayHasKey('message', $responseBody);
        self::assertSame('OK', $responseBody['status']);
    }

    ////////////////////////////////////////////////////////////////////////
    // MCPサーバのイニシャライズを行うAPIのテスト
    ////////////////////////////////////////////////////////////////////////

    /**
     * 正常系: イニシャライズAPIが200 OKを返すことを確認する
     *
     * @return void
     */
    public function testInitializeAction(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'params' => [],
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
        self::assertIsArray($responseBody['result']);
        self::assertArrayHasKey('protocolVersion', $responseBody['result']);
        self::assertArrayHasKey('capabilities', $responseBody['result']);
        self::assertArrayHasKey('logging', $responseBody['result']['capabilities']);
        self::assertArrayHasKey('tools', $responseBody['result']['capabilities']);
        self::assertArrayHasKey('resources', $responseBody['result']['capabilities']);
        self::assertArrayHasKey('prompts', $responseBody['result']['capabilities']);
        self::assertArrayHasKey('serverInfo', $responseBody['result']);
        self::assertArrayHasKey('name', $responseBody['result']['serverInfo']);
        self::assertArrayHasKey('version', $responseBody['result']['serverInfo']);
    }

    /**
     * 正常系: イニシャライズAPIのIDなしリクエストが正常に処理されることを確認する
     *
     * @return void
     */
    public function testInitializeActionWithoutId(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'initialize',
            'params' => [],
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayNotHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
    }

    ////////////////////////////////////////////////////////////////////////
    // MCPサーバの通知初期化を行うAPIのテスト
    ////////////////////////////////////////////////////////////////////////

    /**
     * 正常系: 通知初期化APIが200 OKを返すことを確認する
     *
     * @return void
     */
    public function testNotificationsInitializedAction(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'notifications/initialized',
            'params' => [],
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );

        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
    }

    /**
     * 正常系: 通知初期化APIのレスポンスが空の結果を返すことを確認する
     *
     * @return void
     */
    public function testNotificationsInitializedActionReturnsEmptyResult(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'notifications/initialized',
            'params' => [],
            'id' => 2,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );

        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('result', $responseBody);
        self::assertEmpty($responseBody['result']);
    }

    //////////////////////////////////////////////////////////////////////
    // MCPサーバのツール一覧を取得するAPIのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: ツール一覧APIが200 OKを返すことを確認する
     * @return void
     */
    public function testToolsListAction(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'tools/list',
            'params' => [],
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
        self::assertArrayHasKey('tools', $responseBody['result']);
        self::assertIsArray($responseBody['result']['tools']);
        self::assertNotEmpty($responseBody['result']['tools']);
        foreach ($responseBody['result']['tools'] as $tool) {
            self::assertArrayHasKey('name', $tool);
            self::assertArrayHasKey('description', $tool);
            self::assertArrayHasKey('inputSchema', $tool);
        }
    }

    //////////////////////////////////////////////////////////////////////
    // MCPサーバのツールを呼び出すAPIのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: ツール呼び出しAPIが200 OKを返すことを確認する
     * @return void
     */
    public function testToolsCallAction(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'tools/call',
            'params' => [
                'toolName' => 'NamingTool',
                'input' => [
                    'yourname' => 'Taro'
                ]
            ],
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
    }

    /**
     * 正常系: ツール呼び出しAPIのレスポンスコンテンツが正しい構造を持つことを確認する
     * @return void
     */
    public function testToolsCallActionResponseContent(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'tools/call',
            'params' => [
                'toolName' => 'NamingTool',
                'input' => [
                    'yourname' => 'Hanako'
                ]
            ],
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('result', $responseBody);
        $result = $responseBody['result'];
        self::assertArrayHasKey('content', $result);
        self::assertIsArray($result['content']);
        self::assertNotEmpty($result['content']);
        self::assertArrayHasKey('isError', $result);
        self::assertFalse($result['isError']);
        foreach ($result['content'] as $content) {
            self::assertArrayHasKey('type', $content);
            self::assertArrayHasKey('text', $content);
        }
    }

    //////////////////////////////////////////////////////////////////////
    // MCPサーバのリソース一覧を取得するAPIのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: リソース一覧APIが200 OKを返すことを確認する
     * @return void
     */
    public function testResourcesListAction(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'resources/list',
            'params' => [],
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
        self::assertArrayHasKey('resources', $responseBody['result']);
        self::assertIsArray($responseBody['result']['resources']);
        self::assertNotEmpty($responseBody['result']['resources']);
        foreach ($responseBody['result']['resources'] as $resource) {
            self::assertArrayHasKey('name', $resource);
            self::assertArrayHasKey('title', $resource);
            self::assertArrayHasKey('description', $resource);
            self::assertArrayHasKey('mimeType', $resource);
            self::assertArrayHasKey('url', $resource);
        }
    }

    //////////////////////////////////////////////////////////////////////
    // MCPサーバのリソースを読み込むAPIのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: リソース読み込みAPIが200 OKを返すことを確認する
     * @return void
     */
    public function testResourcesReadAction(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'resources/read',
            'params' => [],
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
        self::assertArrayHasKey('contents', $responseBody['result']);
        self::assertIsArray($responseBody['result']['contents']);
        self::assertNotEmpty($responseBody['result']['contents']);
        foreach ($responseBody['result']['contents'] as $content) {
            self::assertArrayHasKey('uri', $content);
            self::assertArrayHasKey('mimeType', $content);
            self::assertArrayHasKey('text', $content);
        }
    }

    //////////////////////////////////////////////////////////////////////
    // MCPサーバの不明なメソッドを呼び出すAPIのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: ツール呼び出しAPIが200 OKを返すことを確認する
     * @return void
     */
    public function testUnknownMethodCallAction(): void
    {
        $url = 'http://localhost:8081/sse';
        $method = 'POST';
        $body = [
            'jsonrpc' => '2.0',
            'method' => 'unknown/method',
            'id' => 1,
        ];

        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
        self::assertEmpty($responseBody['result']);
    }
}
