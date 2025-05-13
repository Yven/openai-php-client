<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/13 15:50
 */

namespace Tests\response;

use OpenAI\ChatMessage;
use OpenAI\Client;
use OpenAI\constant\LLM;
use OpenAI\constant\Result;
use OpenAI\exception\LlmRequesException;
use OpenAI\response\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testGetFunc()
    {
        $data = json_decode('{"choices": [{"message": {"role": "assistant", "content": "我是阿里云开发的一款超大规模语言模型，我叫通义千问。"}, "finish_reason": "stop", "index": 0, "logprobs": null}], "object": "chat.completion", "usage": {"prompt_tokens": 3019, "completion_tokens": 104, "total_tokens": 3123, "prompt_tokens_details": {"cached_tokens": 2048}}, "created": 1735120033, "system_fingerprint": null, "model": "qwen-plus", "id": "chatcmpl-6ada9ed2-7f33-9de2-8bb0-78bd4035025a"}', true);

        $uuid = 'test-group-uuid-1234';
        $message = new ChatMessage($uuid);
        $message->append('test');

        $res = Response::from($data, $message);

        $this->assertEquals($data['id'], $res->getRemoteId());
        $this->assertEquals($uuid, $res->getGroupUuid());
        $this->assertEquals(LLM::QWEN_ALI, $res->getLlmType());
        $this->assertEquals($data['model'], $res->getModel());
        $this->assertEquals(1, $res->getSeq());
        $this->assertEquals(1, $res->getIndex());
        $this->assertStringContainsString('test', $res->getQuestion());
        $this->assertEquals([], $res->getFile());
        $this->assertEquals([], $res->getImg());
        $this->assertEquals($data['choices'][0]['message']['content'], $res->getContent());
        $this->assertNull($res->getReasoningContent());
        $this->assertNull($res->getToolCalls());

        $service = Client::build(LLM::QWEN_ALI, 'sk-xxx');
        $service->query('test');
        $res->setReqData($service->getBody());
        $body = $service->getBody();
        unset($body['messages']);
        $body = json_encode($body);
        $this->assertEquals($body, $res->getReqData());

        $this->assertEquals($data['usage']['prompt_tokens'], $res->getInputToken());
        $this->assertEquals($data['usage']['completion_tokens'], $res->getOutputToken());
        $this->assertEquals(json_encode($data['usage']), $res->getUsage());
        $this->assertEquals($data['choices'][0]['finish_reason'], $res->getResultStatus());
        $this->assertEquals(Result::getName($data['choices'][0]['finish_reason']), $res->getResultMessage());

        $res->setResultStatus('something_wrong');
        try {
            $res->getResultMessage();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertStringContainsString("不存在", $e->getMessage());
        }

        $usage = $res->getUsage();
        $res->appendUsage($usage);
        $this->assertEquals(2*$data['usage']['prompt_tokens'], $res->getInputToken());
        $this->assertEquals(2*$data['usage']['completion_tokens'], $res->getOutputToken());

        $res->setUsage(null);
        $res->appendUsage($usage);
        $this->assertEquals($data['usage']['prompt_tokens'], $res->getInputToken());
        $this->assertEquals($data['usage']['completion_tokens'], $res->getOutputToken());

        $aliveRes = Response::fromKeepAlive();
        $this->assertEquals(true, $aliveRes->isKeepAlive());
    }
}
