<?php
$apiKeys = '';
$service = \OpenAI\Client::build(\OpenAI\constant\LLM::QWEN_ALI, $apiKeys);

// HTTP 模式直接请求
$data = $service->query("你好，请问你可以做什么？")->send();
echo $data->getContent();

// 构建消息对象，传递复杂的消息，或者进行上下文构建
$message = new OpenAI\ChatMessage;
// 设置 uuid 来实现标记聊天历史对话
$message = new OpenAI\ChatMessage("chat-1234567890");
$message->append('你好，请问你可以做什么？');
// 构建上下文
// $message->append('你好，请问你可以做什么？', "我是您的人工智能助手...")->append("你叫什么名字");
// 添加图片
// $message->appendImg(["https://..."]);
// 添加文件，使用 file-id
// $message->appendFile('file-id://...');
// 添加提示词
// $message->appendPrompt('You are a helpful assistant.');

$data = $service->query($message)->send();
echo $data->getContent();

// 模型切换，使用 Qwq 模型获取推理信息
// 使用 withModel 方法强制切换，如果设置的参数不正确可能会出错
// 使用 withAutoModel 方法自动切换，如果无法切换会使用默认模型
$data1 = $service->query($message)->withAutoModel(\OpenAI\constant\Model::QWEN_QWQ)->send();
echo $data->getReasoningContent();
echo "\n";
echo $data->getContent();
