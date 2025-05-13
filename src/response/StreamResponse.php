<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/3/19 17:32
 */

namespace OpenAI\response;

use Generator;
use Psr\Http\Message\StreamInterface;

/**
 * @Description
 * SSE 模式响应类
 *
 * @Class  : StreamResponse
 * @Package: extend\slms\llm
 */
class StreamResponse implements \IteratorAggregate
{
    private StreamInterface $response;

    public function __construct(StreamInterface $response)
    {
        $this->response = $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator(): Generator
    {
        while (!$this->response->eof()) {
            $line = $this->readLine($this->response);

            if (strpos($line, ': keep-alive') !== false) {
                yield Response::fromKeepAlive();
            }

            // $event = null;
            // if (str_starts_with($line, 'event:')) {
            //     $event = trim(substr($line, strlen('event:')));
            //     $line = $this->readLine($this->response);
            // }

            if (!str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, strlen('data:')));

            if ($data === '[DONE]') {
                break;
            }

            $response = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            yield Response::from($response);
        }
    }

    /**
     * 逐行读取
     *
     * @param StreamInterface $stream
     *
     * @return string
     */
    private function readLine(StreamInterface $stream): string
    {
        $buffer = '';

        while (! $stream->eof()) {
            if ('' === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }
}
