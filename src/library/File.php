<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/27 11:38
 */

namespace DeepSeek\library;

use DeepSeek\exception\LlmFormatException;
use DeepSeek\exception\LlmRequesException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Utils;

trait File
{
    public function uploadFile(array $fileInfo): array
    {
        if (!file_exists($fileInfo['tmp_name'])) {
            throw new \Exception("文件不存在");
        }

        try {
            $response = $this->client->post('files', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => Utils::tryFopen($fileInfo['tmp_name'], 'r'),
                        'filename' => $fileInfo['name'],
                    ],
                    [
                        'name'     => 'purpose',
                        'contents' => 'file-extract',
                    ],
                ],
                'headers' => $this->header,
            ]);

            $res = Helper::httpResponseOutput($response);
        } catch (ClientException|ServerException $e) {
            throw new LlmFormatException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            throw new LlmRequesException(self::errorMessage($e), $e->getCode());
        }

        return $res;
    }

    private function fileRequest(string $method, string $path): array
    {
        try {
            $path = ltrim($path, '/');
            $method = strtoupper($method);
            if (!in_array($method, ['GET', 'DELETE'])) throw new \Exception("请求方法错误");

            // debug_log($this->body);
            $response = $this->client->request($method, $path, [
                'headers' => $this->header,
            ]);

            $res = Helper::httpResponseOutput($response);
            // debug_log($res);
        } catch (ClientException|ServerException $e) {
            throw new LlmFormatException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            throw new LlmRequesException(self::errorMessage($e), $e->getCode());
        }

        return $res;
    }

    public function getFileInfo(string $fileId): array
    {
        return $this->fileRequest('get', 'files/'.$fileId);
    }
    public function getFileList(): array
    {
        return $this->fileRequest('get', 'files');
    }
    public function deleteFile(string $fileId): array
    {
        return $this->fileRequest('delete', 'files/'.$fileId);
    }
}
