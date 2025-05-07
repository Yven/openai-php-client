<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2024/3/30 10:22
 */

namespace DeepSeek\exception;

class LlmRequesException extends \Exception
{
    public function __construct($message = "", $code = 0)
    {
        if (is_array($message)) {
            $errInfo = $message;
            $message = $errInfo[0] ?? '未知错误';
            $code = $errInfo[1] ?? 400;
        }

        parent::__construct($message, $code);
    }
}
