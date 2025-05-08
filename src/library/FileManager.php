<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/27 11:38
 */

namespace OpenAI\library;

use GuzzleHttp\Exception\GuzzleException;
use OpenAI\exception\LlmFormatException;
use OpenAI\exception\LlmRequesException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Utils;
use OpenAI\model\OpenAI;
use OpenAI\response\File;
use OpenAI\response\FileCollection;

class FileManager
{
    private OpenAI $service;

    public function __construct(OpenAI $service)
    {
        $this->service = $service;
    }

    /**
     * 上传文件
     *
     * @param string $path
     * @param string $name
     *
     * @return File
     * @throws GuzzleException
     * @throws LlmFormatException
     * @throws LlmRequesException
     */
    public function upload(string $path, string $name): File
    {
        if (!file_exists($path)) {
            throw new \Exception("文件不存在");
        }

        try {
            $response = $this->service->getClient()->post('files', [
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => Utils::tryFopen($path, 'r'),
                        'filename' => $name,
                    ],
                    [
                        'name'     => 'purpose',
                        'contents' => 'file-extract',
                    ],
                ],
                'headers' => $this->service->getHeader(),
            ]);

            $res = Helper::httpResponseOutput($response);
            if (!is_array($res)) throw new LlmRequesException("请求返回数据格式错误");
        } catch (ClientException|ServerException $e) {
            throw new LlmFormatException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            throw new LlmRequesException($this->service::errorMessage($e), $e->getCode());
        }

        return File::init($res);
    }

    /**
     * 文件操作通用请求
     *
     * @param string $method
     * @param string $path
     *
     * @return array
     * @throws LlmFormatException
     * @throws LlmRequesException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(string $method, string $path, array $query = []): array
    {
        try {
            $path = ltrim($path, '/');
            $method = strtoupper($method);
            if (!in_array($method, ['GET', 'DELETE'])) throw new \Exception("请求方法错误");

            // debug_log($this->body);
            $response = $this->service->getClient()->request($method, $path, [
                'headers' => $this->service->getHeader(),
                'query' => $query,
            ]);

            $res = Helper::httpResponseOutput($response);
            if (!is_array($res)) throw new LlmRequesException("请求返回数据格式错误");
            // debug_log($res);
        } catch (ClientException|ServerException $e) {
            throw new LlmFormatException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            throw new LlmRequesException($this->service::errorMessage($e), $e->getCode());
        }

        return $res;
    }

    /**
     * 获取文件信息
     *
     * @param string $fileId
     *
     * @return File
     * @throws GuzzleException
     * @throws LlmFormatException
     * @throws LlmRequesException
     */
    public function info(string $fileId): File
    {
        $data = $this->request('get', 'files/'.$fileId);
        return File::init($data);
    }

    /**
     * 获取文件列表
     *
     * @return FileCollection
     * @throws GuzzleException
     * @throws LlmFormatException
     * @throws LlmRequesException
     */
    public function list(string $next = null): FileCollection
    {
        $data = $this->request('get', 'files', $next ? ['after' => $next] : []);
        $obj = FileCollection::init($data['data'] ?? []);
        // 递归获取所有数据
        if (isset($data['has_more']) && $data['has_more']) {
            $obj->append($this->list($obj->getLastId()));
        }
        return $obj;
    }

    /**
     * 删除文件
     *
     * @param string $fileId
     *
     * @throws GuzzleException
     * @throws LlmFormatException
     * @throws LlmRequesException
     */
    public function delete(string $fileId)
    {
        $this->request('delete', 'files/'.$fileId);
    }
}
