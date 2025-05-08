<?php
$apiKeys = '';
$service = \OpenAI\Client::build(\OpenAI\constant\LLM::QWEN_ALI, $apiKeys);

// 使用通义上传文件
$fileService = new \OpenAI\library\FileManager($service);
$fileInfo = $fileService->upload($_FILES['file']['tmp_name'], $_FILES['file']['name']);

// 添加文件到对话中
$message = new OpenAI\ChatMessage;
$message->appendFile($fileInfo->getFilename())->append("请根据文件内容，总结出文件的内容");

$resposne = $service->query($message)->withModel(\OpenAI\constant\Model::QWEN_LONG)->stream();
// ...进行操作

// 获取文件列表
$fileList = $fileService->list();
/** @var \OpenAI\response\File $file */
foreach ($fileList as $file) {
    echo $file->getFilename()."\n";
}

// 获取文件信息
$file = $fileService->info($fileInfo->getId());

// 删除文件
$fileService->delete($fileInfo->getId());
