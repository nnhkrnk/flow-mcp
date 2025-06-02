<?php

namespace NNHKRNK\MCP\Tool\InputSchemaType;

use NNHKRNK\MCP\Tool\AbstractInputSchemaType;

/**
 * IntegerInputSchemaType クラスは、int型の入力スキーマを表現します。
 */
class IntegerInputSchemaType extends AbstractInputSchemaType
{
    /**
     * @var string $description オブジェクトの説明
     */
    private string $description;

    /**
     * コンストラクタ
     *
     * @param array $properties オブジェクトのプロパティを定義する配列
     */
    public function __construct(string|int $name, string $descriptuion)
    {
        parent::__construct($name, 'int');
        $this->description = $descriptuion;
    }

    /**
     * @inheritdoc
     */
    public function getInputSchema(): array
    {
        return [
            'type' => $this->getType(),
            'description' => $this->description,
        ];
    }
}
