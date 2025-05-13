<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/12 14:53
 */

namespace Tests\traits;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use OpenAI\Client;

trait RequestMock
{
    public static function buildService(int $type, bool $onlyRightReturn = false): \OpenAI\model\OpenAI
    {
        $apiKeys = 'sk-23333333';
        $service = Client::build($type, $apiKeys);

        // 创建模拟的 Guzzle Client
        $mockClient = m::mock(\GuzzleHttp\Client::class, [
            ['base_uri' => $service->getHost()],
        ]);

        if (!$onlyRightReturn) {
            $mockClient
                ->shouldReceive('post')
                ->with('chat/completions', m::type('array'))
                ->andReturnUsing(
                    function () {
                        // 抛出其他异常，已记录
                        throw new RequestException(
                            "something is wrong",
                            new Request('post', 'chat/completions'),
                            new Response(
                                401,
                                ['Content-Type' => 'application/json'],
                                '{"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "code": "invalid_request_error", "message": "Payload Too Large."}',
                            ),
                        );
                    },
                    function () {
                        // 抛出其他异常，未记录
                        throw new RequestException(
                            "something is wrong",
                            new Request('post', 'chat/completions'),
                            new Response(
                                423,
                                ['Content-Type' => 'application/json'],
                                '{"error": {"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "type": "Throttling", "message": "Too many fine-tune job in running, please retry later."}}',
                            ),
                        );
                    },
                    function () {
                        // 抛出Client异常
                        throw new ClientException(
                            "Payload Too Large",
                            new Request('post', 'chat/completions'),
                            new Response(
                                400,
                                ['Content-Type' => 'application/json'],
                                // TODO 返回的什么格式？
                                '{"error": {"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "type": "invalid_request_error", "message": "Payload Too Large."}}',
                            ),
                        );
                    },
                    // 返回错误格式
                    function () {
                        return new Response(
                            200,
                            ['Content-Type' => 'text/plain;charset=UTF-8'],
                            'this is text message',
                        );
                    },
                    // 返回流数据
                    function () {
                        return new Response(
                            200,
                            ['Content-Type' => 'text/event-stream;charset=UTF-8'],
                            'data: {"id":"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57","choices":[{"delta":{"content":"我是","function_call":null,"refusal":null,"role":null,"tool_calls":null},"finish_reason":null,"index":0,"logprobs":null}],"created":1735113344,"model":"qwen-plus","object":"chat.completion.chunk","service_tier":null,"system_fingerprint":null,"usage":null}',
                        );
                    },
                    // 返回JSON数据
                    function () {
                        return new Response(
                            200,
                            ['Content-Type' => 'application/json'],
                            '{"choices": [{"message": {"role": "assistant", "content": "我是阿里云开发的一款超大规模语言模型，我叫通义千问。"}, "finish_reason": "stop", "index": 0, "logprobs": null}], "object": "chat.completion", "usage": {"prompt_tokens": 3019, "completion_tokens": 104, "total_tokens": 3123, "prompt_tokens_details": {"cached_tokens": 2048}}, "created": 1735120033, "system_fingerprint": null, "model": "qwen-plus", "id": "chatcmpl-6ada9ed2-7f33-9de2-8bb0-78bd4035025a"}',
                        );
                    }
                );
        } else {
            $mockClient
                ->shouldReceive('post')
                ->with('chat/completions', m::type('array'))
                ->andReturnUsing(
                    function ($path, $options) {
                        if (isset($options['stream']) && $options['stream']) {
                            // 返回流数据
                            return new Response(
                                200,
                                ['Content-Type' => 'text/event-stream;charset=UTF-8'],
                                'data: {"id":"chatcmpl-e30f5ae7-3063-93c4-90fe-beb5f900bd57","choices":[{"delta":{"content":"我是","function_call":null,"refusal":null,"role":null,"tool_calls":null},"finish_reason":null,"index":0,"logprobs":null}],"created":1735113344,"model":"qwen-plus","object":"chat.completion.chunk","service_tier":null,"system_fingerprint":null,"usage":null}',
                            );
                        } else {
                            // 返回JSON数据
                            return new Response(
                                200,
                                ['Content-Type' => 'application/json'],
                                '{"choices": [{"message": {"role": "assistant", "content": "我是阿里云开发的一款超大规模语言模型，我叫通义千问。"}, "finish_reason": "stop", "index": 0, "logprobs": null}], "object": "chat.completion", "usage": {"prompt_tokens": 3019, "completion_tokens": 104, "total_tokens": 3123, "prompt_tokens_details": {"cached_tokens": 2048}}, "created": 1735120033, "system_fingerprint": null, "model": "qwen-plus", "id": "chatcmpl-6ada9ed2-7f33-9de2-8bb0-78bd4035025a"}',
                            );
                        }
                    },
                );
        }

        $service->setClient($mockClient);
        return $service;
    }
}
