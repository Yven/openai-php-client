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

$data = $service->query("你好，请问你可以做什么？")->send();
echo $data->getContent();
```

### develop
