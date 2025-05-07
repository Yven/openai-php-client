<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2024/3/30 10:22
 */

namespace OpenAI\exception;

class LlmFormatException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        if (is_array($message)) {
            $errInfo = $message;
            $message = $errInfo[0] ?? '未知错误';
            $code = $errInfo[1] ?? 400;
        }

        $errInfo = json_decode($message, true)['error'] ?? [];
        if (!$errInfo) debug_log($errInfo);
        $message = '['.($errInfo['type']??'').']:'.($errInfo['message']??'接口返回错误信息格式不正确');

        parent::__construct($message, $code);
    }
}
