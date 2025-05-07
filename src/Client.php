<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/6 17:38
 */

namespace OpenAI;

use OpenAI\constant\LLM;
use OpenAI\model\DeepSeek;
use OpenAI\model\OpenAI;
use OpenAI\model\Qwen;

class Client
{
    /**
     * @param int    $llmType
     * @param string $apiKeys
     *
     * @return OpenAI
     * @throws \Exception
     */
    public static function build(int $llmType, string $apiKeys): OpenAI
    {
        switch ($llmType) {
            case LLM::DEEPSEEK:
                return new DeepSeek($apiKeys);
            case LLM::QWEN_ALI:
                return new Qwen($apiKeys);
            default:
                throw new \Exception('不支持的AI模型');
        }
    }
}
