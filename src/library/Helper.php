<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/30 11:33
 */

namespace OpenAI\library;

use Psr\Http\Message\ResponseInterface;

class Helper
{
    /**
     * 数组各项相加
     *
     * @param array $oldArr
     * @param array $newArr
     *
     * @return array
     */
    public static function arrayItemAdd(array $oldArr, array $newArr): array
    {
        foreach ($newArr as $key => $item) {
            if (is_array($newArr[$key])) {
                foreach ($item as $k => $v) {
                    $newArr[$key][$k] = intval($v) + intval(@$oldArr[$key][$k]??0);
                }
            } else {
                $newArr[$key] = intval($item) + intval(@$oldArr[$key]??0);
            }
        }

        return $newArr;
    }

    /**
     * 雪花算法生成 ID
     *
     * @return string
     * @throws \Exception
     */
    public static function snowflakeId(): string
    {
        $snowflake = new \Godruoyi\Snowflake\Snowflake();
        return $snowflake
            ->setStartTimeStamp(strtotime('2025-03-20') * 1000)
            ->id();
    }

    /**
     * @param ResponseInterface $response
     *
     * @return array|\Psr\Http\Message\StreamInterface
     * @throws \Exception
     * @throws \JsonException
     */
    public static function httpResponseOutput(ResponseInterface $response)
    {
        $contentType = $response->getHeader('Content-Type')[0] ?? '';

        if (strpos($contentType, 'json') !== false) {
            // json格式返回
            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($data)) throw new \Exception('响应格式错误:'.$response->getBody()->getContents());
            return $data;
        } elseif (strpos($contentType, 'stream') !== false) {
            // 流返回
            return $response->getBody();
        } else {
            throw new \Exception('响应格式错误:'.$response->getBody()->getContents());
        }
    }
}
