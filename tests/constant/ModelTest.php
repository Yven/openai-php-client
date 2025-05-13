<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/13 15:08
 */

namespace Tests\constant;

use OpenAI\constant\LLM;
use OpenAI\constant\Model;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testIsMatch() {
        $this->assertEquals(false, Model::isMatch('qwen-plus', '\\OpenAI\\model\\NewTester'));
    }

    public function testGetLlmByModel() {
        $this->assertEquals(LLM::QWEN_ALI, Model::getLlmByModel(Model::QWEN_3));
        $this->assertEquals(-1, Model::getLlmByModel(-1));
    }

    public function testGetName() {
        try {
            Model::getName(-1);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertStringContainsString('不存在', $e->getMessage());
        }

        $this->assertEquals('通义千问-Plus', Model::getName(Model::QWEN_PLUS));
    }

    public function testGetCode() {
        try {
            Model::getCode(-1);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertStringContainsString('不存在', $e->getMessage());
        }

        $this->assertEquals('qwen3-32b', Model::getCode(Model::QWEN_3));
    }

    public function testGetNameList() {
        $dsList = Model::getNameList(LLM::DEEPSEEK);
        $this->assertCount(2, $dsList);

        $allList = Model::getNameList();
        $this->assertCount(7, $allList);
    }

    public function testGetValueList() {
        $dsList = Model::getValueList(LLM::DEEPSEEK);
        $this->assertCount(2, $dsList);

        $allList = Model::getValueList();
        $this->assertCount(7, $allList);
    }

    public function testGetValue() {
        try {
            Model::getValue('qwen3-32b-lastest');
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertStringContainsString('不存在', $e->getMessage());
        }

        $this->assertEquals(Model::QWEN_3, Model::getValue('qwen3-32b'));
    }
}
