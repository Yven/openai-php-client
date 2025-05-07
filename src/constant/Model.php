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
 * AI 模型枚举
 *
 * @Class  : Model
 * @Package: extend\slms\llm\constant
 */
class Model extends Enum
{
    const DEEPSEEK_CHAT = 0;
    const DEEPSEEK_REASONER = 1;
    const QWEN_PLUS = 2;
    const QWEN_VL_PLUS = 3;
    const QWEN_LONG = 4;
    const QWEN_QWQ = 5;
    const QWEN_3 = 6;

    /**
     * 模型对应名称
     * @var array
     */
    protected static array $nameList = [
        LLM::DEEPSEEK => [
            self::DEEPSEEK_CHAT => 'DeepSeek-快速答疑',
            self::DEEPSEEK_REASONER => 'DeepSeek-深度思考',
        ],
        LLM::QWEN_ALI => [
            self::QWEN_PLUS => '通义千问-Plus',
            self::QWEN_VL_PLUS => '通义千问-视觉理解Plus',
            self::QWEN_LONG => '通义千问-Long',
            self::QWEN_QWQ => '通义千问-QwQ推理模型',
            self::QWEN_3 => '通义千问-Qwen3',
        ],
    ];

    /**
     * 模型对应值
     * @var array
     */
    protected static array $valueList = [
        self::DEEPSEEK_CHAT => 'deepseek-chat',
        self::DEEPSEEK_REASONER => 'deepseek-reasoner',

        self::QWEN_PLUS => 'qwen-plus',
        self::QWEN_VL_PLUS => 'qwen-vl-plus',
        self::QWEN_LONG => 'qwen-long',
        self::QWEN_QWQ => 'qwq-plus',
        self::QWEN_3 => 'qwen3-32b',
    ];

    /**
     * 强制使用流模式的模型
     * @var array
     */
    protected static array $streamModel = [
        Model::QWEN_LONG,
        Model::QWEN_QWQ,
        Model::QWEN_3
    ];

    /**
     * 通过 model 获取 LLM 类型
     *
     * @param string $code
     *
     * @return int
     */
    public static function getLlmByModel(string $code): int
    {
        try {
            $modelCode = self::getValue($code);
        } catch (\Exception $e) {
            return -1;
        }

        foreach (self::$nameList as $llm => $item) {
            if (in_array($modelCode, array_keys($item))) {
                return $llm;
            }
        }

        return -1;
    }

    /**
     * 判断模型是否强制使用流模式
     *
     * @param string $model
     *
     * @return bool
     */
    public static function isStreamForce(string $model): bool
    {
        return in_array($model, self::$streamModel);
    }

    /**
     * Model与LLM是否匹配
     *
     * @param string $model
     * @param string $llmClass
     *
     * @return bool
     */
    public static function isMatch(string $model, string $llmClass): bool
    {
        if (($llmType = self::getLlmByModel($model)) === -1) return false;
        try {
            if (($llmType2 = LLM::getValue($llmClass)) === -1) return false;
        } catch (\Exception $e) {
            return false;
        }

        return $llmType === $llmType2;
    }
}
