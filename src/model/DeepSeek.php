<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/3/19 17:32
 */

namespace OpenAI\model;

/**
 * @Description
 * DeepSeek请求类
 *
 * @Class  : DeepSeek
 * @Package: extend\slms\llm
 */
class DeepSeek extends OpenAI
{
    protected string $host = 'https://api.deepseek.com/';
    protected string $defaultModel = 'deepseek-chat';
    protected static array $httpCodeErrorMessage = [
        400 => '请求格式错误',
        401 => '认证失败',
        402 => '账户余额不足，请联系管理员',
        422 => '请求参数错误',
        429 => '请求速率达到上限',
        500 => 'DeepSeek服务器故障',
        503 => 'DeepSeek服务繁忙',
    ];
}
