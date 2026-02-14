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
     * ロガー
     * @Flow\Inject
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

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
    const MCP_METHOD_RESOURCEES_LIST = 'resources/list';
    const MCP_METHOD_RESOURCEES_READ = 'resources/read';
    
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
                self::MCP_METHOD_RESOURCEES_LIST => $this->listResources(),
                self::MCP_METHOD_RESOURCEES_READ => $this->readResource(),
                default => [],
            };

            $this->logger->info('MCP Method Called: ' . $arguments['method']);
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
            'protocolVersion' => '2025-11-25',
            'capabilities' => [
                'logging' => [],
                'tools' => [
                    'listChanged' => false,
                ],
                'resources' => [
                    'listChanged' => false,
                ],
                'prompts' => [],
                'apps' => [], 
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
                //     '_meta' => [
                //         'resourceUri' => 'ui://naming/result',
                //         'visibility' => [
                //             'model',
                //             'app',
                //         ],
                //     ]
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
        sleep(10);
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

    public function listResources(): array
    {
        return [
            'resources' => [
                [
                    'name' => 'Sample Resource',
                    'title' => 'サンプルリソース',
                    'description' => 'これはサンプルのリソースです。',
                    'typemimeType' => 'text/html;profile=mcp-app',
                    'url' => 'ui://naming/result',
                ]
            ]
        ];
    }

    /**
     * リソースの読み込み
     */
    public function readResource(): array
    {
        // 本来はここに入力値から生成するロジックが入る
        $nickname = 'アドマイヤベガ';

        return [
            'contents' => [
                [
                    'uri' => 'ui://naming/result',
                    'mimeType' => 'text/html;profile=mcp-app',
                    'text' => $this->generateHtml($nickname),
                ]
            ]
        ];
    }

    /**
     * MCP Apps用のHTMLを生成
     */
    private function generateHtml(string $nickname): string
    {
        return <<<EOM
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary-color: #6366f1;
            --accent-color: #818cf8;
            --bg-color: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-sub: #64748b;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-color);
            margin: 0;
            padding: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-sizing: border-box;
        }
        .card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px 16px;
            text-align: center;
            border: 1px solid #e2e8f0;
            width: 100%;
            max-width: 320px;
        }
        .icon {
            font-size: 32px;
            margin-bottom: 4px;
            display: inline-block;
            animation: bounce 2s infinite;
        }
        h1 {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-sub);
            margin: 0 0 12px 0;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .result-box {
            margin: 0 0 12px 0;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 12px;
            color: white;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
        }
        .result-label {
            font-size: 11px;
            opacity: 0.9;
            margin-bottom: 2px;
            display: block;
            font-weight: 500;
        }
        .nickname {
            font-size: 22px;
            font-weight: 800;
            line-height: 1.3;
        }
        .footer-msg {
            font-size: 11px;
            color: var(--text-sub);
            line-height: 1.4;
            margin: 0;
        }
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✨</div>
        <h1>Naming Ceremony</h1>
        <div class="result-box">
            <span class="result-label">命名</span>
            <div class="nickname">{$nickname}</div>
        </div>
        <p class="footer-msg">
            あなたの新しいアイデンティティが<br>誕生しました。
        </p>
    </div>
</body>
</html>
EOM;
    }
}
