<?php
$apiKeys = '';

$service = \DeepSeek\Client::build(\DeepSeek\constant\LLM::QWEN_ALI, $apiKeys);

// 构建消息对象
$message = new \DeepSeek\model\ChatMessage();
$message->append('你好，请问你可以做什么？');
// 添加图片
// $message->appendImg(["https://..."]);
// 添加文件，使用 file-id
// $message->appendFile('file-id://...');
// 添加提示词
// $message->appendPrompt('You are a helpful assistant.');

// HTTP 模式直接请求
$data = $service->query($message)->send();
echo $data->getContent();

// 模型切换，使用 Qwq 模型获取推理信息
// 使用 withModel 方法强制切换，如果设置的参数不正确可能会出错
// 使用 withAutoModel 方法自动切换，如果无法切换会使用默认模型
$data1 = $service->query($message)->withModel(\DeepSeek\constant\Model::QWEN_QWQ)->send();
echo $data->getReasoningContent();
echo "\n";
echo $data->getContent();

// SSE 模式请求
$streamItor = $service->query($message)->withModel(\DeepSeek\constant\Model::QWEN_QWQ)->stream();
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

// 使用通义上传文件
if ($service::hasFileFunc()) {
    /** @var \DeepSeek\model\Qwen $service */
    $remoteFileInfo = $service->uploadFile($_FILES['file']);
    $message->appendFile($remoteFileInfo['filename']);
    $service->query($message)->withModel(\DeepSeek\constant\Model::QWEN_LONG)->stream();
    // ...
}
