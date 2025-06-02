<?php

namespace NNHKRNK\MCP\Tool;

abstract class AbstractInputSchemaType
{
    /**
     * @var string $type 入力スキーマのタイプ
     */
    private string|int $name;

    /**
     * @var string $type 入力スキーマのタイプ
     */
    private string $type;

    /**
     * コンストラクタ
     *
     * @param string $type 入力スキーマのタイプ
     */
    public function __construct(string|int $name, string $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * 入力スキーマを取得する
     *
     * @return array 入力スキーマの配列
     */
    abstract public function getInputSchema(): array;

    /**
     * 入力スキーマの名前を取得する
     *
     * @return string 入力スキーマの名前
     */
    public function getName(): string|int
    {
        return $this->name;
    }

    /**
     * 入力スキーマのタイプを取得する
     *
     * @return string 入力スキーマのタイプ
     */
    public function getType(): string
    {
        return $this->type;
    }
}