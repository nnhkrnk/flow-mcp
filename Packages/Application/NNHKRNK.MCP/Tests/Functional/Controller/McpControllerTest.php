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

    private const API_TOKEN = 'test-secret-token';

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager->get(\Neos\Flow\Configuration\ConfigurationManager::class);
        // テスト用トークンをランタイム設定として注入
        $settings = $this->objectManager->get(\Neos\Flow\Configuration\ConfigurationManager::class)
            ->getConfiguration(\Neos\Flow\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS, 'NNHKRNK.MCP');
        $settings['apiToken'] = self::API_TOKEN;
        // Flow のプロキシオブジェクトへの設定注入はコントローラー生成時に行われるため、
        // テスト用設定ファイル (Configuration/Testing/Settings.yaml) で管理することを推奨します。
    }

    //////////////////////////////////////////////////////////////////////
    // MCPサーバのヘルスチェックを行うAPIのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: ヘルスチェックAPIが認証なしで200 OKを返すことを確認する
     */
    public function testHealthCheckAction(): void
    {
        $response = $this->browser->request('http://localhost:8081/sse');
        self::assertSame(200, $response->getStatusCode());
    }

    //////////////////////////////////////////////////////////////////////
    // 認証テスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 異常系: Authorizationヘッダーなしのリクエストが401を返すことを確認する
     */
    public function testHandleMcpActionWithoutTokenReturns401(): void
    {
        $response = $this->browser->request(
            'http://localhost:8081/sse',
            method: 'POST',
            arguments: [
                'jsonrpc' => '2.0',
                'method' => 'initialize',
                'params' => [],
                'id' => 1,
            ]
        );
        self::assertSame(401, $response->getStatusCode());
    }

    /**
     * 異常系: 不正なトークンのリクエストが401を返すことを確認する
     */
    public function testHandleMcpActionWithWrongTokenReturns401(): void
    {
        $this->browser->addAutomaticRequestHeader('Authorization', 'Bearer wrong-token');
        $response = $this->browser->request(
            'http://localhost:8081/sse',
            method: 'POST',
            arguments: [
                'jsonrpc' => '2.0',
                'method' => 'initialize',
                'params' => [],
                'id' => 1,
            ]
        );
        $this->browser->removeAutomaticRequestHeader('Authorization');
        self::assertSame(401, $response->getStatusCode());
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

        $this->browser->addAutomaticRequestHeader('Authorization', 'Bearer ' . self::API_TOKEN);
        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        $this->browser->removeAutomaticRequestHeader('Authorization');

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

        $this->browser->addAutomaticRequestHeader('Authorization', 'Bearer ' . self::API_TOKEN);
        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        $this->browser->removeAutomaticRequestHeader('Authorization');

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

        $this->browser->addAutomaticRequestHeader('Authorization', 'Bearer ' . self::API_TOKEN);
        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        $this->browser->removeAutomaticRequestHeader('Authorization');

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

        $this->browser->addAutomaticRequestHeader('Authorization', 'Bearer ' . self::API_TOKEN);
        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        $this->browser->removeAutomaticRequestHeader('Authorization');

        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
    }

    //////////////////////////////////////////////////////////////////////
    // MCPサーバの未知メソッド呼び出しのテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: 未知メソッド呼び出しAPIが200 OKを返すことを確認する
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

        $this->browser->addAutomaticRequestHeader('Authorization', 'Bearer ' . self::API_TOKEN);
        $response = $this->browser->request(
            $url,
            method: $method,
            arguments: $body
        );
        $this->browser->removeAutomaticRequestHeader('Authorization');

        self::assertSame(200, $response->getStatusCode());
        $responseBody = json_decode($response->getBody()->getContents(), true);
        self::assertArrayHasKey('jsonrpc', $responseBody);
        self::assertArrayHasKey('id', $responseBody);
        self::assertArrayHasKey('result', $responseBody);
        self::assertEmpty($responseBody['result']);
    }
}
