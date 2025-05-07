<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/23 15:12
 */

namespace OpenAI\constant;

/**
 * @Description
 * 对话停止结果枚举
 *
 * @Class  : Result
 * @Package: extend\slms\llm\constant
 */
class Result extends Enum
{
    const NORMAL_STOP = 'stop';
    const LENGTH = 'length';
    const CONTENT_FILTER = 'content_filter';
    const TOOL_CALLS = 'tool_calls';
    const INSUFFICIENT_SYSTEM_RESOURCE = 'insufficient_system_resource';

    protected static array $nameList = [
        self::NORMAL_STOP => '正常结束',
        self::LENGTH => '输出长度达到限制',
        self::CONTENT_FILTER => '输出内容因触发过滤策略而被过滤',
        self::TOOL_CALLS => '工具调用',
        self::INSUFFICIENT_SYSTEM_RESOURCE => '系统推理资源不足，生成被打断',
    ];
}
