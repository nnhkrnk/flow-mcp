<?php

namespace NNHKRNK\MCP\Tool;

abstract class AbstractTool
{
    private string $name;

    private string $description;

    private AbstractInputSchemaType $inputSchema;


    /**
     * コンストラクタ
     *
     * @param string $name ツールの名前
     * @param string $description ツールの説明
     * @param AbstractInputSchema $inputSchema 入力スキーマ
     */
    public function __construct(string $name, string $description, AbstractInputSchemaType $inputSchemaType)
    {
        $this->name = $name;
        $this->description = $description;
        $this->inputSchema = $inputSchemaType;
    }

    /**
     * ツールの情報を取得する
     */
    public function getToolInfo(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'inputSchema' => $this->inputSchema->getInputSchema(),
        ];
    }
}
