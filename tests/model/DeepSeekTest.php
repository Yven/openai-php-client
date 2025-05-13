<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/9 10:44
 */

namespace Tests\model;

use Mockery as m;
use OpenAI\ChatMessage;
use OpenAI\constant\LLM;
use OpenAI\constant\Model;
use OpenAI\exception\LlmFormatException;
use OpenAI\exception\LlmRequesException;
use OpenAI\response\Response;
use OpenAI\response\StreamResponse;
use PHPUnit\Framework\TestCase;
use Tests\traits\RequestMock;

class DeepSeekTest extends TestCase
{
    use RequestMock;

    protected function tearDown(): void
    {
        m::close();
    }

    private static int $defaultLLM = LLM::DEEPSEEK;

    public function testSend() {
        $service = self::buildService(self::$defaultLLM);

        try {
            $service->query("你好，请问你可以做什么？")->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertEquals(401, $e->getCode());
            $this->assertStringContainsString('认证失败', $e->getMessage());
        }

        try {
            $service->query("你好，请问你可以做什么？")->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertEquals(423, $e->getCode());
        }

        try {
            $service->query("你好，请问你可以做什么？")->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmFormatException::class, $e);
            $this->assertEquals(400, $e->getCode());
            $this->assertStringContainsString('invalid_request_error', $e->getMessage());
        }

        try {
            $service->query("你好，请问你可以做什么？")->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString('响应格式错误', $e->getMessage());
        }

        try {
            $service->query("你好，请问你可以做什么？")->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString('请求返回数据格式错误', $e->getMessage());
        }

        $data1 = $service->query("你好，请问你可以做什么？")->withSearch()->send();
        $this->assertInstanceOf(Response::class, $data1);
        $this->assertEquals(true, $service->getBody()['enable_search']);

        try {
            $service->query("你好，请问你可以做什么？")->withModel(Model::QWEN_QWQ);
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString('使用了不支持的模型', $e->getMessage());
        }
    }

    public function testStream() {
        $service = self::buildService(self::$defaultLLM, true);

        $data = $service->query("你好，请问你可以做什么？")->stream();
        $this->assertInstanceOf(StreamResponse::class, $data);
    }

    public function testWithModel() {
        $service = self::buildService(self::$defaultLLM, true);

        $service->query("你好，请问你可以做什么？")->withModel(Model::DEEPSEEK_REASONER);
        $this->assertEquals(Model::getCode(Model::DEEPSEEK_REASONER), $service->getBody()['model']);
    }

    public function testWithAutoModel() {
        $service = self::buildService(self::$defaultLLM, true);

        $message = new ChatMessage;
        $service->query($message)->withAutoModel();
        $this->assertEquals(Model::getCode(Model::DEEPSEEK_CHAT), $service->getBody()['model']);
    }
}
