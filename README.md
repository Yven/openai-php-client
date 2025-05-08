# openai-php-client

openai-php-client is a community-maintained PHP API client that allows you to interact with OpenAI like API, supports php7.4+.

### Install
```shell
composer require yven/openai-php-client
```

### Usage
```php
$apiKeys = "sk-xxx...";
$service = \OpenAI\Client::build(\OpenAI\constant\LLM::QWEN_ALI, $apiKeys);

// HTTP 请求
$data = $service->query("你好，请问你可以做什么？")->send();
echo $data->getContent();

// SSE 请求
$data = $service->query("你好，请问你可以做什么？")->stream();
$content = '';
/** @var \OpenAI\response\Response $item */
foreach ($data as $item) {
    $content .= $item->getContent();
}
echo $content;
```

### Develop
支持模型：

| 厂商       | 模型类型     | 模型名称          |
|------------|--------------|-------------------|
| DeepSeek   | 普通模型     | `deepseek-chat`   |
| DeepSeek   | 推理模型     | `deepseek-reasoner` |
| 通义千问   | 普通模型     | `qwen-plus`       |
| 通义千问   | 视觉模型     | `qwen-vl-plus`    |
| 通义千问   | 长文本模型   | `qwen-long`       |
| 通义千问   | 推理模型     | `qwq-plus`        |
| 通义千问   | Qwen3        | `qwen3-32b`       |
