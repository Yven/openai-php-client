<?php
$apiKeys = '';

$service = \DeepSeek\Client::build(\DeepSeek\constant\LLM::QWEN_ALI, $apiKeys);

// HTTP 模式直接请求
$data = $service->query()->send();
echo $data->getContent();

// 模型切换，使用 Qwq 模型获取推理信息
// 使用 withModel 方法强制切换，如果设置的参数不正确可能会出错
// 使用 withAutoModel 方法自动切换，如果无法切换会使用默认模型
$data1 = $service->query()->withModel(\DeepSeek\constant\Model::QWEN_QWQ)->send();
echo $data->getReasoningContent();
echo "\n";
echo $data->getContent();

// SSE 模式请求
$streamItor = $service->query()->withModel(\DeepSeek\constant\Model::QWEN_QWQ)->stream();
$content = '';
$reasonContent = '';
// 遍历输出
/** @var \DeepSeek\response\Response $item */
foreach ($streamItor as $item) {
    $content .= $item->getContent();
    $reasonContent .= $item->getReasoningContent();
}
echo $content;
echo "\n";
echo $reasonContent;
