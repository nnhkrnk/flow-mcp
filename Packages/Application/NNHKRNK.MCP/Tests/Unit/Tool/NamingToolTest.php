<?php

namespace NNHKRNK\MCP\Tests\Unit\Tool;

use Neos\Flow\Tests\UnitTestCase;
use NNHKRNK\MCP\Tool\Tools\NamingTool;

class NamingToolTest extends UnitTestCase
{
    private NamingTool $namingTool;

    protected function setUp(): void
    {
        parent::setUp();
        $this->namingTool = new NamingTool();
    }

    //////////////////////////////////////////////////////////////////////
    // getToolInfo のテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: getToolInfo がツール情報の必須キーを含む配列を返すことを確認する
     */
    public function testGetToolInfoReturnsRequiredKeys(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();

        self::assertArrayHasKey('name', $toolInfo);
        self::assertArrayHasKey('description', $toolInfo);
        self::assertArrayHasKey('inputSchema', $toolInfo);
        self::assertArrayHasKey('_meta', $toolInfo);
    }

    /**
     * 正常系: getToolInfo がツール名として 'NamingTool' を返すことを確認する
     */
    public function testGetToolInfoReturnsCorrectName(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();

        self::assertSame('NamingTool', $toolInfo['name']);
    }

    /**
     * 正常系: getToolInfo がツールの説明を返すことを確認する
     */
    public function testGetToolInfoReturnsDescription(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();

        self::assertNotEmpty($toolInfo['description']);
        self::assertIsString($toolInfo['description']);
    }

    /**
     * 正常系: getToolInfo の inputSchema がオブジェクト型であることを確認する
     */
    public function testGetToolInfoInputSchemaIsObject(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();

        self::assertSame('object', $toolInfo['inputSchema']['type']);
        self::assertArrayHasKey('properties', $toolInfo['inputSchema']);
        self::assertArrayHasKey('required', $toolInfo['inputSchema']);
    }

    /**
     * 正常系: getToolInfo の inputSchema が 'yourname' プロパティを含むことを確認する
     */
    public function testGetToolInfoInputSchemaContainsYournameProperty(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();

        self::assertArrayHasKey('yourname', $toolInfo['inputSchema']['properties']);
        self::assertSame('string', $toolInfo['inputSchema']['properties']['yourname']['type']);
        self::assertContains('yourname', $toolInfo['inputSchema']['required']);
    }

    /**
     * 正常系: getToolInfo の _meta に UI 情報が含まれることを確認する
     */
    public function testGetToolInfoMetaContainsUiInfo(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();

        self::assertArrayHasKey('ui', $toolInfo['_meta']);
        self::assertArrayHasKey('resourceUri', $toolInfo['_meta']['ui']);
        self::assertArrayHasKey('visibility', $toolInfo['_meta']['ui']);
        self::assertSame('ui://naming/result', $toolInfo['_meta']['ui']['resourceUri']);
    }

    //////////////////////////////////////////////////////////////////////
    // call のテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: call が必須キーを含む配列を返すことを確認する
     */
    public function testCallReturnsRequiredKeys(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);

        self::assertArrayHasKey('content', $result);
        self::assertArrayHasKey('isError', $result);
    }

    /**
     * 正常系: call の isError が false であることを確認する
     */
    public function testCallResponseIsNotError(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);

        self::assertFalse($result['isError']);
    }

    /**
     * 正常系: call の content が空でない配列であることを確認する
     */
    public function testCallResponseContentIsNotEmpty(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);

        self::assertIsArray($result['content']);
        self::assertNotEmpty($result['content']);
    }

    /**
     * 正常系: call の content 各要素が type と text キーを持つことを確認する
     */
    public function testCallResponseContentItemsHaveTypeAndText(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);

        foreach ($result['content'] as $item) {
            self::assertArrayHasKey('type', $item);
            self::assertArrayHasKey('text', $item);
            self::assertSame('text', $item['type']);
            self::assertIsString($item['text']);
        }
    }

    /**
     * 正常系: call が異なる入力パラメータでも正常に動作することを確認する
     */
    public function testCallWithDifferentInputParams(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Hanako']);

        self::assertArrayHasKey('content', $result);
        self::assertFalse($result['isError']);
    }

    /**
     * 正常系: call が空の入力パラメータでも正常に動作することを確認する
     */
    public function testCallWithEmptyParams(): void
    {
        $result = $this->namingTool->call([]);

        self::assertArrayHasKey('content', $result);
        self::assertArrayHasKey('isError', $result);
    }
}
