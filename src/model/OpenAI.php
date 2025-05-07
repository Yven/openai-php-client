<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/3/19 17:32
 */

namespace OpenAI\model;

use OpenAI\ChatMessage;
use OpenAI\constant\Model;
use OpenAI\exception\LlmFormatException;
use OpenAI\exception\LlmRequesException;
use OpenAI\library\Helper;
use OpenAI\response\Response;
use OpenAI\response\StreamResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\StreamInterface;

/**
 * @Description
 * AI请求基类
 *
 * @Class  : OpenAI
 * @Package: extend\slms\llm\model
 */
abstract class OpenAI
{
    protected string $apiKeys;
    /** @var array 请求参数 */
    protected array $body;
    /** @var string 请求地址 */
    protected string $host;
    protected Client $client;
    /** @var string[] 请求消息头 */
    protected array $header;
    /** @var ChatMessage DeepSeek 请求接口消息对象 */
    protected ChatMessage $messages;
    /** @var string 请求路径 */
    protected string $path = 'chat/completions';
    /** @var string 默认使用模型 */
    protected string $defaultModel;
    /** @var array 请求错误信息 */
    protected static array $httpCodeErrorMessage;

    public function __construct(string $apiKeys)
    {
        $this->apiKeys = $apiKeys;
        $this->client = new Client([
            'base_uri'        => $this->host,
            'timeout'         => 5*60,
        ]);
        $this->header = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->apiKeys,
        ];
    }

    /**
     * 请求标题（或简略总结）
     * @return $this
     * @throws LlmRequesException
     */
    public function queryTitle(): OpenAI
    {
        array_unshift(
            $this->body['messages'],
            ...(new ChatMessage)->appendPrompt("使用20个以内的字符总结下面的问题和答案，遇到无法总结的情况直接返回\"默认标题\"")->toArray(),
        );
        $this->withModel($this->defaultModel);

        return $this;
    }

    /**
     * 设置 AI 模型
     *
     * @param int $model
     *
     * @return $this
     * @throws LlmRequesException
     */
    public function withModel(int $model): self
    {
        if (!Model::isMatch($model, get_class($this))) throw new LlmRequesException("使用了不支持的模型");
        $this->body['model'] = Model::getCode($model);

        return $this;
    }

    /**
     * 自动选择模型，传递的值为设置默认选择，不传则会使用默认模型
     *
     * @param int|null $model
     *
     * @return $this
     * @throws LlmRequesException
     */
    public function withAutoModel(int $model = null): self
    {
        return $this->withModel($model ?? $this->defaultModel);
    }

    /**
     * 设置联网模式
     *
     * @return $this
     */
    public function withSearch(): self
    {
        $this->body['enable_search'] = true;

        return $this;
    }

    /**
     * 构造DeepSeek请求参数
     *
     * @param string|ChatMessage $message 消息对象
     *
     * @return $this
     * @throws \Exception
     */
    public function query($message): self
    {
        if ($message instanceof ChatMessage) {
            $this->messages = $message;
        } else if (is_string($message)) {
            $this->messages = (new ChatMessage())->append($message);
        } else {
            throw new \Exception("参数类型错误");
        }

        $this->body = [
            "messages" => $this->messages->toArray(),
            "stream" => false,
            "response_format" => [
                "type" => "text"
            ],
            // 下面的参数可能会导致某些模型输出失败，用不上的暂时注释
            // "max_tokens" => 8192,
            // "stop" => null,
            // "tools" => null,
            // "presence_penalty" => 0,
            // "tool_choice" => "none",
            // "top_p" => 1,
            // "temperature" => 1,
            // "frequency_penalty" => 0,
            // "logprobs" => false,
            // "top_logprobs" => null
        ];

        if (!isset($this->body['model'])) {
            if (!$this->defaultModel) throw new \Exception("AI模型类初始化未完成");
            $this->body['model'] = $this->defaultModel;
        }

        return $this;
    }

    /**
     * 发送请求前的检查
     *
     * @return bool
     */
    protected function check(): bool
    {
        return true;
    }

    /**
     * 通用请求
     *
     * @return array|StreamInterface
     * @throws LlmFormatException
     * @throws LlmRequesException|\GuzzleHttp\Exception\GuzzleException
     */
    private function request()
    {
        if (Model::isMatch($this->body['model'], get_class($this))) throw new LlmRequesException("使用了不支持的模型");
        try {
            if (!$this->check()) throw new LlmRequesException("请求参数格式错误");
        } catch (LlmRequesException $e) {
            throw $e;
        }

        $options = [
            'headers' => $this->header,
            'body' => $this->body,
        ];

        // 流模式设置
        if ($this->body['stream']) {
            $options['stream'] = true;
        } else if (Model::isStreamForce($this->body['model'])) {
            throw new LlmRequesException("此模型只支持流模式");
        }

        try {
            // debug_log($this->body);
            $response = $this->client->post($this->path, $options);

            $res = Helper::httpResponseOutput($response);
            // debug_log($res);
        } catch (ClientException|ServerException $e) {
            throw new LlmFormatException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            throw new LlmRequesException(self::errorMessage($e), $e->getCode());
        }

        return $res;
    }

    /**
     * 发送请求
     *
     * @return Response
     * @throws LlmFormatException
     * @throws LlmRequesException|\GuzzleHttp\Exception\GuzzleException
     */
    public function send(): Response
    {
        $res = $this->request();
        if (!is_array($res)) throw new LlmRequesException("请求返回数据格式错误");
        return Response::from($res, $this->messages);
    }

    /**
     * 使用流模式输出消息
     *
     * @return StreamResponse
     * @throws LlmRequesException|LlmFormatException|\GuzzleHttp\Exception\GuzzleException
     */
    public function stream(): StreamResponse
    {
        $this->body['stream'] = true;
        $this->body['stream_options'] = ['include_usage' => true];
        $res = $this->request();
        if (!($res instanceof StreamInterface)) throw new LlmRequesException("请求返回数据格式错误");
        return new StreamResponse($res);
    }

    public function getBody(): array
    {
        return $this->body;
    }

    protected static function errorMessage(\Exception $e): string
    {
        return self::$httpCodeErrorMessage[$e->getCode()] ?? $e->getMessage();
    }

    /**
     * 能否处理远程文件
     *
     * @return bool
     */
    public static function hasFileFunc(): bool
    {
        return false;
    }
}
