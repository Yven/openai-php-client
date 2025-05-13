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

class QwenTest extends TestCase
{
    use RequestMock;

    protected function tearDown(): void
    {
        m::close();
    }

    private static int $defaultLLM = LLM::QWEN_ALI;

    public function testSend() {
        $service = self::buildService(self::$defaultLLM);

        try {
            $service->query("你好，请问你可以做什么？")->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertEquals(401, $e->getCode());
            $this->assertStringContainsString('请求中的 API Key 错误。请重新获取API Key。', $e->getMessage());
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

        $data1 = $service->query("你好，请问你可以做什么？")->send();
        $this->assertInstanceOf(Response::class, $data1);

        $message = new ChatMessage;
        $message->append("你好，请问你可以做什么？");
        $data2 = $service->query($message)->send();
        $this->assertInstanceOf(Response::class, $data2);

        try {
            $service->query(['你好，请问你可以做什么？']);
        } catch (\Exception $e) {
            $this->assertInstanceOf(\Exception::class, $e);
            $this->assertStringContainsString('参数类型错误', $e->getMessage());
        }

        try {
            $service->query("你好，请问你可以做什么？")->withModel(Model::DEEPSEEK_REASONER);
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString('使用了不支持的模型', $e->getMessage());
        }

        try {
            $service->query("你好，请问你可以做什么？")->withModel(Model::QWEN_QWQ)->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString('此模型只支持流模式', $e->getMessage());
        }
    }

    public function testStream() {
        $service = self::buildService(self::$defaultLLM, true);

        $data = $service->query("你好，请问你可以做什么？")->stream();
        $this->assertInstanceOf(StreamResponse::class, $data);
    }

    public function testCheck() {
        $service = self::buildService(self::$defaultLLM, true);

        try {
            $service->query("你好，请问你可以做什么？")->withModel(Model::QWEN_LONG)->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString('请上传文件，或切换为文本模型', $e->getMessage());
        }

        try {
            $service->query("你好，请问你可以做什么？")->withModel(Model::QWEN_VL_PLUS)->send();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString('请上传图片，或切换为文本模型', $e->getMessage());
        }
    }

    public function testWithModel() {
        $service = self::buildService(self::$defaultLLM, true);

        $service->query("你好，请问你可以做什么？")->withModel(Model::QWEN_3);
        $this->assertEquals(true, $service->getBody()['enable_thinking']);
    }

    public function testWithAutoModel() {
        $service = self::buildService(self::$defaultLLM, true);

        $message = new ChatMessage;
        $service->query($message)->withAutoModel(Model::QWEN_PLUS);
        $this->assertEquals(Model::getCode(Model::QWEN_PLUS), $service->getBody()['model']);

        $message = new ChatMessage;
        $imgList = ['https://foo/images_logo.png'];
        $message->appendImg($imgList, '请解释画中的内容');
        $data = $service->query($message)->withAutoModel()->send();
        $this->assertInstanceOf(Response::class, $data);
        $this->assertEquals(Model::getCode(Model::QWEN_VL_PLUS), $service->getBody()['model']);
        $this->assertEquals($imgList, $message->getImg());

        $message = new ChatMessage;
        $fileId = 'file-fe-xxx';
        $message->appendFile($fileId, true)->append("请解释文件内容");
        $data = $service->query($message)->withAutoModel()->stream();
        $this->assertInstanceOf(StreamResponse::class, $data);
        $this->assertEquals(Model::getCode(Model::QWEN_LONG), $service->getBody()['model']);
        $this->assertContains($fileId, $message->getFile());
    }
}
