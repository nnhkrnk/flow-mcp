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
    }
}

