<?php

namespace NNHKRNK\MCP\Tool\InputSchemaType;

use NNHKRNK\MCP\Tool\AbstractInputSchemaType;

/**
 * ObjectInputSchemaType クラスは、オブジェクト型の入力スキーマを表現します。
 */
class ObjectInputSchemaType extends AbstractInputSchemaType
{
    /**
     * @var array<AbstractInputSchemaType> $properties オブジェクトのプロパティ
     */
    private array $properties;

    /**
     * @var array<string> $required 必須プロパティ
     */
    private array $required = [];

    /**
     * コンストラクタ
     *
     * @param string $name 入力スキーマの名前
     * @param array $properties オブジェクトのプロパティを定義する配列
     * @param array $required 必須プロパティの配列
     */
    public function __construct(string $name, array $properties, array $required = [])
    {
        // TODO バリデーション


        parent::__construct($name, 'object');
        $this->properties = $properties;
        $this->required = $required;
    }

    /**
     * @inheritdoc
     */
    public function getInputSchema(): array
    {
        $childProperties = [];
        foreach ($this->properties as $property) {
            $childProperties[$property->getName()] = $property->getInputSchema();
        }
        return [
            'type' => $this->getType(),
            'properties' => $childProperties,
            'required' => $this->required,
        ];
    }

}
