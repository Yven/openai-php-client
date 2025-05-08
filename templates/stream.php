<?php
$apiKeys = '';
$service = \OpenAI\Client::build(\OpenAI\constant\LLM::QWEN_ALI, $apiKeys);

$message = new OpenAI\ChatMessage;
$message->append('你好，请问你可以做什么？');

// SSE 模式请求
$streamItor = $service->query($message)->withModel(\OpenAI\constant\Model::QWEN_QWQ)->stream();

$content = '';
$reasonContent = '';
$save = null;
$stopStatus = null;

// 如果要使用 SSE 模式输出，初始化
\OpenAI\library\SSEService::init();

// 遍历输出
/** @var \OpenAI\response\Response $item */
foreach ($streamItor as $item) {
    // 内容
    $content .= $item->getContent();
    // 推理
    $reasonContent .= $item->getReasoningContent();
    // 获取终止的对象，可能在最后一句返回也可能在倒数第二句返回
    if (!is_null($item->getUsage())) $save = $item;
    // 获取停止状态
    if (!is_null($item->getResultStatus())) $stopStatus = $item->getResultStatus();

    // 直接使用流式输出
    if (!empty($item->getContent())) {
        \OpenAI\library\SSEService::echo("chat", $item->getContent());
    } elseif (!empty($item->getReasoningContent())) {
        \OpenAI\library\SSEService::echo("reasoner", $item->getReasoningContent());
    } elseif ($item->isKeepAlive()) {
        \OpenAI\library\SSEService::echo("waiting", "服务繁忙，等待响应中...");
    }
}

// 完善结果对象
$save->setByMessage($message);
$save->setContent($content);
$save->setReasoningContent($reasonContent);
$save->setResultStatus($stopStatus);
$save->setReqData($service->getBody());
