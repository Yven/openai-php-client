<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/3/27 10:33
 */

namespace DeepSeek\response;

use DeepSeek\constant\Result;
use DeepSeek\exception\LlmRequesException;
use DeepSeek\library\Helper;
use DeepSeek\model\ChatMessage;

class Response
{
    private string $remoteId;
    private string $groupUuid;
    private string $llmType;
    private string $model;
    private int $chatSeq;
    private int $index;
    private ?string $question = null;
    private ?array $file = [];
    private ?array $imgUrl = [];
    private ?string $content = null;
    private ?string $reasoningContent = null;
    private ?string $toolCalls = null;
    private ?string $reqData = null;
    private int $inputToken;
    private int $outputToken;
    private ?string $usage = null;
    private ?string $resultStatus = null;

    private bool $isKeepAlive;

    public function __construct($isKeepAlive = false)
    {
        $this->isKeepAlive = $isKeepAlive;
    }

    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    public function setRemoteId(string $remoteId)
    {
        $this->remoteId = $remoteId;
    }

    public function getGroupUuid(): string
    {
        return $this->groupUuid;
    }

    public function setGroupUuid(?string $groupUuid)
    {
        $this->groupUuid = empty($groupUuid) ? $this->generateGroupUuid() : $groupUuid;
    }

    public function getLlmType(): string
    {
        return $this->llmType;
    }

    public function setLlmType(string $model)
    {
        $this->llmType = \DeepSeek\constant\Model::getLlmByModel($model);
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model)
    {
        $this->model = $model;
    }

    public function getSeq(): int
    {
        return $this->chatSeq;
    }

    public function setSeq(int $seq)
    {
        $this->chatSeq = $seq;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function setIndex(int $index)
    {
        $this->index = $index;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(?string $question)
    {
        $this->question = $question;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getImg()
    {
        return $this->imgUrl;
    }

    /**
     * @param mixed $img
     */
    public function setImg($img)
    {
        $this->imgUrl = $img;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content)
    {
        $this->content = $content;
    }

    public function getReasoningContent(): ?string
    {
        return $this->reasoningContent;
    }

    public function setReasoningContent(?string $reasoningContent)
    {
        $this->reasoningContent = $reasoningContent;
    }

    public function getToolCalls(): ?string
    {
        return $this->toolCalls;
    }

    public function setToolCalls(?string $toolCalls)
    {
        $this->toolCalls = $toolCalls;
    }

    public function getReqData(): ?string
    {
        return $this->reqData;
    }

    public function setReqData(array $reqData)
    {
        unset($reqData['messages']);
        $this->reqData = json_encode($reqData);
    }

    public function getInputToken(): int
    {
        return $this->inputToken;
    }

    public function setInputToken(int $inputToken)
    {
        $this->inputToken = $inputToken;
    }

    public function getOutputToken(): int
    {
        return $this->outputToken;
    }

    public function setOutputToken(int $outputToken)
    {
        $this->outputToken = $outputToken;
    }

    public function getUsage(): ?string
    {
        return $this->usage;
    }

    public function setUsage(?string $usage)
    {
        $this->usage = $usage;
    }

    public function getResultStatus(): ?string
    {
        return $this->resultStatus;
    }

    public function setResultStatus(?string $resultStatus)
    {
        $this->resultStatus = $resultStatus;
    }

    public function isKeepAlive(): bool
    {
        return $this->isKeepAlive;
    }

    /**
     * 额外加上其他的用量
     *
     * @param string $usage
     *
     * @return void
     */
    public function appendUsage(string $usage)
    {
        $oldUsage = @json_decode($this->usage, true) ?: [];
        $newUsage = @json_decode($usage, true) ?: [];

        if (!$this->usage) {
            $this->usage = $usage;
            return;
        }

        $nowUsage = Helper::arrayItemAdd($oldUsage, $newUsage);

        $this->setInputToken($nowUsage['prompt_tokens']??0);
        $this->setOutputToken($nowUsage['completion_tokens']??0);
        $this->usage = json_encode($nowUsage);
    }

    /**
     * 生成对话 UUID
     *
     * @return string
     * @throws \Exception
     */
    public function generateGroupUuid(): string
    {
        $this->groupUuid = Helper::snowflakeId();

        return $this->groupUuid;
    }

    /**
     * 通过请求消息对象进行赋值设置
     *
     * @param ChatMessage $message
     *
     * @return void
     */
    public function setByMessage(ChatMessage $message)
    {
        $this->setSeq($message->getLastSeq());
        $this->setIndex($message->getLastIndex());
        $this->setGroupUuid($message->getUuid());
        $this->setQuestion($message->getLastQuestion());
        $this->setFile($message->getFile());
        $this->setImg($message->getImg());
    }

    /**
     * 使用响应数据生成 AiChatRecord 对象
     *
     * @param array            $data
     * @param ChatMessage|null $message
     *
     * @return self
     * @throws LlmRequesException
     */
    public static function from(array $data, ChatMessage $message = null): self
    {
        $obj = new Response;

        if (!isset($data['id'])) throw new LlmRequesException("返回数据格式错误：缺少 id");
        $obj->setRemoteId($data['id']);

        if (!isset($data['model'])) throw new LlmRequesException("返回数据格式错误：缺少 model");
        $obj->setModel($data['model']);
        $obj->setLlmType($data['model']);

        if (!is_null($message)) {
            $obj->setByMessage($message);
        }

        if (!isset($data['choices'])) throw new LlmRequesException("返回数据格式错误：缺少 choices");
        $defaultChat = $data['choices'][0] ?? null;
        if ($defaultChat) {
            $obj->setResultStatus($defaultChat['finish_reason']);

            // 流和普通模式返回字段不同
            $msg = $defaultChat['delta'] ?? $defaultChat['message'] ?? null;
            if (is_null($msg)) throw new LlmRequesException("返回数据格式错误：缺少 message 或 delta");

            // if (!$msg['content']) throw new LlmRequesException("返回数据格式错误：缺少消息内容");
            $obj->setContent($msg['content']);

            $toolCalls = $msg['tool_calls'] ?? null;
            $obj->setToolCalls($toolCalls ? json_encode($toolCalls) : null);
            $obj->setReasoningContent($msg['reasoning_content']??null);
        }

        if (isset($data['usage'])) {
            $obj->setUsage(json_encode($data['usage']));
        }
        $obj->setInputToken(@$data['usage']['prompt_tokens']??0);
        $obj->setOutputToken(@$data['usage']['completion_tokens']??0);

        return $obj;
    }

    /**
     * 流模式下接口忙碌时生成一个空的 keepAlive 对象
     *
     * @return self
     */
    public static function fromKeepAlive(): self
    {
        return new Response(true);
    }

    /**
     * 返回输出结果停止的具体原因
     *
     * @return string
     * @throws LlmRequesException
     */
    public function getResultMessage(): string
    {
        try {
            return Result::getName($this->getResultStatus() ?? '');
        } catch (\Exception $e) {
            throw new LlmRequesException('未知停止原因:'.$e->getMessage());
        }
    }
}

