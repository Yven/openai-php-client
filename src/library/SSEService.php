<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/2 15:39
 */

namespace DeepSeek\library;

/**
 * @Description
 * SSE 服务类
 *
 * @Class  : SSEService
 * @Package: extend\slms\llm
 */
class SSEService
{
    /**
     * 初始化
     *
     * @return void
     */
    public static function init()
    {
        // 这行代码用于关闭输出缓冲。关闭后，脚本的输出将立即发送到浏览器，而不是等待缓冲区填满或脚本执行完毕。
        ini_set('output_buffering', 'off');
        // 这行代码禁用了 zlib 压缩。通常情况下，启用 zlib 压缩可以减小发送到浏览器的数据量，但对于服务器发送事件来说，实时性更重要，因此需要禁用压缩。
        ini_set('zlib.output_compression', 'false');
        // 这行代码使用循环来清空所有当前激活的输出缓冲区。ob_end_flush() 函数会刷新并关闭最内层的输出缓冲区，@ 符号用于抑制可能出现的错误或警告。
        while (@ob_end_flush()) { continue; }
        // 这行代码设置 HTTP 响应的 Content-Type 为 text/event-stream，这是服务器发送事件（SSE）的 MIME 类型。
        header('Content-Type: text/event-stream');
        // 这行代码设置 HTTP 响应的 Cache-Control 为 no-cache，告诉浏览器不要缓存此响应。
        header('Cache-Control: no-cache');
        // 这行代码设置 HTTP 响应的 Connection 为 keep-alive，保持长连接，以便服务器可以持续发送事件到客户端。
        header('Connection: keep-alive');
        // 这行代码设置 HTTP 响应的自定义头部 X-Accel-Buffering 为 no，用于禁用某些代理或 Web 服务器（如 Nginx）的缓冲。这有助于确保服务器发送事件在传输过程中不会受到缓冲影响
        header('X-Accel-Buffering: no');

        // 跨域配置
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authori-zation,Authorization,Content-Type, If-Match,If-Modified-Since,If-None-Match,If-Unmodified-Since,X-Requested-With,Accept,Origin,User-Agent,DNT,Cache-Control,X-Mx-ReqToken,X-Requested-With');
        header('Access-Control-Allow-Methods: GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE');

        // 开启输出缓冲
        ob_start();
    }

    /**
     * 输出 SSE 格式内容，如果只传一个参数则不设置 event
     *
     * @param string      $event 事件名称|数据内容
     * @param string|null $data  数据内容
     *
     * @return void
     */
    public static function echo(string $event, ?string $data = null)
    {
        // 转义换行符，直接输出会导致格式错误
        $event = str_replace("\n", '\\n', $event);
        $data = str_replace("\n", '\\n', $data);
        if (is_null($data)) {
            echo "data: ".$event."\n\n";
        } else {
            echo "event: ".$event."\ndata: ".$data."\n\n";
        }

        //刷新缓冲区
        ob_flush();
        //将输出缓冲区的内容立即发送到客户端
        flush();
    }

    /**
     * 输出格式化成功消息
     *
     * @param string $data
     *
     * @return void
     */
    public static function success(string $data)
    {
        self::echo('end_success', $data);
    }

    /**
     * 输出格式化失败消息
     *
     * @param string $msg
     *
     * @return void
     */
    public static function fail(string $msg)
    {
        self::echo('end_error', $msg);
    }
}
