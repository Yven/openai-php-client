# openai-php-client

openai-php-client is a community-maintained PHP API client that allows you to interact with OpenAI like API, supports php7.4+.

### install
```shell
composer require yven/openai-php-client
```

### usage
```php
$apiKeys = "sk-xxx...";
$service = \OpenAI\Client::build(\OpenAI\constant\LLM::QWEN_ALI, $apiKeys);

// http 请求
$data = $service->query("你好，请问你可以做什么？")->send();
echo $data->getContent();

// SSE 请求
$data = $service->query("你好，请问你可以做什么？")->stream();
$content = '';
/** @var \OpenAI\response\Response $item */
foreach ($streamItor as $item) {
    $content .= $item->getContent();
}
echo $content;
```

### develop
支持模型：
1. DeepSeek:
   1. 普通模型`deepseek-chat`
   2. 推理模型`deepseek-reasoner`
2. 通义千问: 
   1. 普通模型`qwen-plus`
   2. 视觉模型`qwen-vl-plus`
   3. 长文本模型`qwen-long`
   4. 推理模型`qwq-plus`
   5. Qwen3`qwen3-32b`
