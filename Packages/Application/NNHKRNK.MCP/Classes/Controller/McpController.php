<?php

namespace NNHKRNK\MCP\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;

use NNHKRNK\MCP\Tool\Tools\NamingTool;
use NNHKRNK\MCP\Tool\ToolInterface;

class McpController extends ActionController
{

    /**
     * レスポンスをJSON形式で返すためのビュー
     * @Flow\Inject
     * @var \Neos\Flow\Mvc\View\JsonView
     */
    protected $view;

    /**
     * MCPサーバがサポートするメディアタイプ
     */
    protected $supportedMediaTypes = ['text/html', 'application/json', 'application/xml', 'application/yaml', 'text/event-stream'];

    /**
     * MCPサーバが使用するJSON-RPCのバージョン
     */
    const JSON_RPC_VERSION = '2.0';

    /**
     * MCPサーバのメソッド名定数
     */
    const MCP_METHOD_INITIALIZE = 'initialize';
    const MCP_METHOD_NOTIFICATIONS_INITIALIZED = 'notifications/initialized';
    const MCP_METHOD_TOOLS_LIST = 'tools/list';
    const MCP_METHOD_TOOLS_CALL = 'tools/call';
    
    /**
     * ヘルスチェック用エンドポイント
     * MCPクライアントからのヘルスチェックを受け付けるアクション
     * 
     * @return void
     */
    public function healthCheckAction(): void
    {
        $this->view->assign('value', [
            'status' => 'OK',
            'message' => 'MCP server is running',
        ]);
    }

    /**
     * MCPサーバ用エンドポイント.
     *
     * @return void
     */
    public function handleMcpAction(): void
    {
        /**
         * リクエストパラメータを取得
         *
         * @var array{
         *     jsonrpc: string,
         *     id?: int,
         *     method: string,
         *     params?: array<mixed>
         * } $arguments
         */
        $arguments = $this->request->getArguments();

        $response['jsonrpc'] = self::JSON_RPC_VERSION;
        if (array_key_exists('id', $arguments)) {
            $response['id'] = $arguments['id'];
        }

        try {
            $response['result'] = match ($arguments['method']) {
                self::MCP_METHOD_INITIALIZE => $this->initMcp(),
                self::MCP_METHOD_NOTIFICATIONS_INITIALIZED => [],
                self::MCP_METHOD_TOOLS_LIST => $this->listMcpTools(),
                self::MCP_METHOD_TOOLS_CALL => $this->callMcpTool($arguments['params']),
            };
        } catch (\Exception $e) {
            $response['error'] = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ];
        } finally {
            $this->view->assign('value', $response);
        }
    }

    /**
     * MCPサーバの初期化
     */
    public function initMcp(): array
    {
        return [
            'protocolVersion' => '2025-03-26',
            'capabilities' => [
                'logging' => [],
                'tools' => [
                    'listChanged' => false,
                ],
                'resources' => [],
                'prompts' => [],   
            ],
            'serverInfo' => [
                'name' => 'Naming Server',
                'version' => '1.0.0',
            ],
        ];
    }

    /**
     * MCPツールのリストを返す
     *
     * @return array List of tools
     */
    public function listMcpTools(): array
    {
        $namingTool = new NamingTool();
        return [
            'tools' => [
                // [
                //     'name' => 'Naming Tool',
                //     'description' => '本名からあだ名を生成します.',
                //     'inputSchema' => [
                //         'type' => 'object',
                //         'properties' => [
                //             'yourname' => [
                //                 'type' => 'string',
                //                 'description' => 'あなたの名前.'
                //             ]
                //         ],
                //         'required' => ['yourname']
                //     ],
                // ],
                $namingTool->getToolInfo(),
            ]
        ];
    }

    /**
     * MCPツールを呼び出す
     *
     * @param array{
     *     name: string,
     *     arguments: array<mixed>,
     * } $params Parameters for the tool call
     * @return array Response from the tool call
     */
    public function callMcpTool(array $params): array
    {
        $tool = new NamingTool();
        return $tool->call($params);
        
        // if ($params['name'] === 'Naming Tool') {
        //     return [
        //         'content' => [
        //             [
        //                 'type' => 'text',
        //                 'text' => 'Your name is アドマイヤベガ.'
        //             ],
        //         ],
        //         'isError' => false,
        //     ];
        // }

        // // ツールがない場合は例外を投げる
        // throw new \Exception('Unknown tool: ' . $params['name'], -32602);
    }
}
