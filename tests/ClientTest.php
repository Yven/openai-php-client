<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/10 17:05
 */

namespace Tests;

use OpenAI\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testBuild()
    {
        $apiKeys = 'sk-2333';
        $service = Client::build(\OpenAI\constant\LLM::QWEN_ALI, $apiKeys);
        $this->assertInstanceOf(\OpenAI\model\Qwen::class, $service);

        $service = Client::build(\OpenAI\constant\LLM::DEEPSEEK, $apiKeys);
        $this->assertInstanceOf(\OpenAI\model\DeepSeek::class, $service);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('不支持的AI模型');
        Client::build(2025, $apiKeys);
    }
}
