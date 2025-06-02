<?php

namespace NNHKRNK\MCP\Tool;

Interface ToolInterface
{
    /**
     * Toolを実行するメソッド
     */
    public function call(array $params): array;
}
