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
    protected string $errCode;

    public function __construct($message = "", $code = 0)
    {
        $errInfo = json_decode($message, true)['error'] ?? [];
        $this->errCode = $errInfo['type'] ?? '';
        $this->message =  $errInfo['message'] ?? '';
        $message = '['.($errInfo['type']??'').']:'.($errInfo['message']??'接口返回错误信息格式不正确');

        parent::__construct($message, $code);
    }

    public function getErrCode(): string
    {
        return $this->errCode;
    }
}
