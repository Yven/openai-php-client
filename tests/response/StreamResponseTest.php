<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/13 15:50
 */

namespace Tests\response;

use OpenAI\response\Response;
use OpenAI\response\StreamResponse;
use PHPUnit\Framework\TestCase;

class StreamResponseTest extends TestCase
{
    public function testIterator()
    {
        $data = new \GuzzleHttp\psr7\Response(
            200,
            ['Content-Type' => 'text/event-stream;charset=UTF-8'],
            "data: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"\",\"function_call\":null,\"refusal\":null,\"role\":\"assistant\",\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"我是\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"来自\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"阿里\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"云的超大规模\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"语言模型，我\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"叫通义千\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\n: keep-alive\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"问。\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":null,\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[{\"delta\":{\"content\":\"\",\"function_call\":null,\"refusal\":null,\"role\":null,\"tool_calls\":null},\"finish_reason\":\"stop\",\"index\":0,\"logprobs\":null}],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":null}\n\ndata: {\"id\":\"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57\",\"choices\":[],\"created\":1735113344,\"model\":\"qwen-plus\",\"object\":\"chat.completion.chunk\",\"service_tier\":null,\"system_fingerprint\":null,\"usage\":{\"completion_tokens\":17,\"prompt_tokens\":22,\"total_tokens\":39,\"completion_tokens_details\":null,\"prompt_tokens_details\":{\"audio_tokens\":null,\"cached_tokens\":0}}}\n\ndata: [DONE]",
        );

        $res = new StreamResponse($data->getBody());

        $content = '';
        $endItem = null;
        $endStatus = '';
        /** @var Response $item */
        foreach ($res as $item) {
            if ($item->isKeepAlive()) continue;
            $content .= $item->getContent();
            if (!is_null($item->getUsage())) $endItem = $item;
            if (!is_null($item->getResultStatus())) $endStatus = $item->getResultStatus();
        }

        $this->assertStringContainsString('阿里', $content);
        $this->assertEquals('stop', $endStatus);
        $this->assertEquals(22, $endItem->getInputToken());
    }
}
