<?php

namespace NNHKRNK\MCP\Tests\Unit\Tool;

use Neos\Flow\Tests\UnitTestCase;
use NNHKRNK\MCP\Tool\Tools\NamingTool;

class NamingToolTest extends UnitTestCase
{
    private NamingTool $namingTool;

    public function setUp(): void
    {
        $this->namingTool = new NamingTool();
    }

    //////////////////////////////////////////////////////////////////////
    // NamingToolのgetToolInfo()のテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: ツール情報に必要なキーが含まれていることを確認する
     */
    public function testGetToolInfoStructure(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();
        self::assertArrayHasKey('name', $toolInfo);
        self::assertArrayHasKey('description', $toolInfo);
        self::assertArrayHasKey('inputSchema', $toolInfo);
        self::assertArrayHasKey('_meta', $toolInfo);
    }

    /**
     * 正常系: ツール名が正しく設定されていることを確認する
     */
    public function testGetToolInfoHasCorrectName(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();
        self::assertSame('NamingTool', $toolInfo['name']);
    }

    /**
     * 正常系: ツールの説明が空でないことを確認する
     */
    public function testGetToolInfoHasDescription(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();
        self::assertNotEmpty($toolInfo['description']);
    }

    /**
     * 正常系: inputSchemaがobjectタイプであることを確認する
     */
    public function testGetToolInfoInputSchemaIsObjectType(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();
        $inputSchema = $toolInfo['inputSchema'];
        self::assertArrayHasKey('type', $inputSchema);
        self::assertSame('object', $inputSchema['type']);
    }

    /**
     * 正常系: inputSchemaにyournameプロパティが含まれていることを確認する
     */
    public function testGetToolInfoInputSchemaHasYournameProperty(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();
        $inputSchema = $toolInfo['inputSchema'];
        self::assertArrayHasKey('properties', $inputSchema);
        self::assertArrayHasKey('yourname', $inputSchema['properties']);
    }

    /**
     * 正常系: inputSchemaのrequiredにyournameが含まれていることを確認する
     */
    public function testGetToolInfoInputSchemaRequiresYourname(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();
        $inputSchema = $toolInfo['inputSchema'];
        self::assertArrayHasKey('required', $inputSchema);
        self::assertContains('yourname', $inputSchema['required']);
    }

    /**
     * 正常系: _metaにUI情報が含まれていることを確認する
     */
    public function testGetToolInfoMetaHasUiInfo(): void
    {
        $toolInfo = $this->namingTool->getToolInfo();
        self::assertArrayHasKey('ui', $toolInfo['_meta']);
        self::assertArrayHasKey('resourceUri', $toolInfo['_meta']['ui']);
        self::assertArrayHasKey('visibility', $toolInfo['_meta']['ui']);
    }

    //////////////////////////////////////////////////////////////////////
    // NamingToolのcall()のテスト
    //////////////////////////////////////////////////////////////////////

    /**
     * 正常系: call()が必要なキーを含むレスポンスを返すことを確認する
     */
    public function testCallReturnsCorrectStructure(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);
        self::assertArrayHasKey('content', $result);
        self::assertArrayHasKey('isError', $result);
        self::assertIsArray($result['content']);
    }

    /**
     * 正常系: call()のisErrorがfalseであることを確認する
     */
    public function testCallIsErrorFalse(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);
        self::assertFalse($result['isError']);
    }

    /**
     * 正常系: call()のcontentがtextタイプを持つことを確認する
     */
    public function testCallContentHasTextType(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);
        self::assertNotEmpty($result['content']);
        self::assertSame('text', $result['content'][0]['type']);
        self::assertArrayHasKey('text', $result['content'][0]);
    }

    /**
     * 正常系: call()のcontentに空でないテキストが含まれることを確認する
     */
    public function testCallContentTextIsNotEmpty(): void
    {
        $result = $this->namingTool->call(['yourname' => 'Taro']);
        self::assertNotEmpty($result['content'][0]['text']);
    }
}
