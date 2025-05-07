<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/23 15:12
 */

namespace DeepSeek\constant;

/**
 * @Description
 * 大语言类型枚举
 *
 * @Class  : LlmType
 * @Package: extend\slms\llm\constant
 */
class LLM extends Enum
{
    const DEEPSEEK = 0;
    const QWEN_ALI = 1;

    protected static array $valueList = [
        self::DEEPSEEK => 'DeepSeek',
        self::QWEN_ALI => 'Qwen',
    ];

    protected static array $nameList = [
        self::DEEPSEEK => 'DeepSeek',
        self::QWEN_ALI => '通义千问',
    ];
}
