<?php

namespace NNHKRNK\MCP\Tool\Tools;

use NNHKRNK\MCP\Tool\AbstractTool;
use NNHKRNK\MCP\Tool\InputSchemaType\StringInputSchemaType;
use NNHKRNK\MCP\Tool\InputSchemaType\ObjectInputSchemaType;;
use NNHKRNK\MCP\Tool\ToolInterface;

/**
 * NamingTool クラスは、ツールの名前と説明を提供するクラスです。
 */
class NamingTool extends AbstractTool implements ToolInterface
{
    public function __construct()
    {
        $name = 'NamingTool';
        $description = '本名からあだ名を生成します';
        $inputSchemaType = new ObjectInputSchemaType(
            'inputSchema',
            [
                new StringInputSchemaType('yourname', 'あなたの名前.')
            ],
            ['yourname']
        );
        parent::__construct($name, $description, $inputSchemaType);
    }

    /**
     * @inheritdoc
     */
    public function call(array $params): array
    {
        return [
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'Your name is アドマイヤベガ.'
                ],
            ],
            'isError' => false,
        ];
    }
}
