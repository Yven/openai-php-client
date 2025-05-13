<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/9 12:01
 */

namespace Tests\library;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mockery as m;
use OpenAI\Client;
use OpenAI\constant\LLM;
use OpenAI\exception\LlmFormatException;
use OpenAI\exception\LlmRequesException;
use OpenAI\library\FileManager;
use OpenAI\response\File;
use PHPUnit\Framework\TestCase;

class FileManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public static function buildService(): \OpenAI\model\OpenAI
    {
        $apiKeys = 'sk-23333333';
        $service = Client::build(LLM::QWEN_ALI, $apiKeys);

        // 创建模拟的 Guzzle Client
        $mockClient = m::mock(\GuzzleHttp\Client::class, [
            ['base_uri' => $service->getHost()],
        ]);

        // 定义期望的请求和虚拟响应
        $mockClient
            ->shouldReceive('post')
            ->with('files', m::type('array'))
            ->andReturnUsing(
                function () {
                    // 抛出其他异常，未记录
                    throw new RequestException(
                        "something is wrong",
                        new Request('post', 'chat/completions'),
                        new Response(
                            423,
                            ['Content-Type' => 'application/json'],
                            '{"error": {"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "type": "Throttling", "message": "Too many fine-tune job in running, please retry later."}}',
                        ),
                    );
                },
                function () {
                    // 抛出Client异常
                    throw new ClientException(
                        "Payload Too Large",
                        new Request('post', 'chat/completions'),
                        new Response(
                            400,
                            ['Content-Type' => 'application/json'],
                            '{"error": {"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "type": "invalid_request_error", "message": "Payload Too Large."}}',
                        ),
                    );
                },
                function () {
                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        '{"id": "file-fe-xxx", "bytes": 2055, "created_at": 1729065448, "filename": "FileManagerTest.php", "object": "file", "purpose": "file-extract", "status": "processed", "status_details": null}',
                    );
                }
            );
        $mockClient
            ->shouldReceive('request')
            ->with('GET', 'files', m::type('array'))
            ->andReturnUsing(
                function () {
                    // 抛出其他异常，未记录
                    throw new RequestException(
                        "something is wrong",
                        new Request('post', 'chat/completions'),
                        new Response(
                            423,
                            ['Content-Type' => 'application/json'],
                            '{"error": {"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "type": "Throttling", "message": Too many fine-tune job in running, please retry later."}}',
                        ),
                    );
                },
                function () {
                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        '{"object": "list", "has_more": true, "data": [{"id": "file-fe-xxx", "object": "file", "bytes": 84889, "created_at": 1715569225, "filename": "FileManagerTest.php", "purpose": "file-extract", "status": "processed"}, { "id": "file-fe-yyy", "object": "file", "bytes": 722355, "created_at": 1715413868, "filename": "Agent_survey.pdf", "purpose": "file-extract", "status": "processed"}]}',
                    );
                },
                function () {
                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        '{"object": "list", "has_more": false, "data": [{"id": "file-fe-zzz", "object": "file", "bytes": 84889, "created_at": 1715569225, "filename": "FileManagerTest.php", "purpose": "file-extract", "status": "processed"}]}',
                    );
                }
            );
        $mockClient
            ->shouldReceive('request')
            ->with('GET', m::pattern('/^files\/file\-fe\-\w+/'), m::type('array'))
            ->andReturnUsing(
                function () {
                    // 抛出Client异常
                    throw new ClientException(
                        "Too many fine-tune job in running",
                        new Request('post', 'chat/completions'),
                        new Response(
                            400,
                            ['Content-Type' => 'application/json'],
                            '{"error": {"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "type": "Throttling", "message": "Too many fine-tune job in running, please retry later."}}',
                        ),
                    );
                },
                function () {
                    // 抛出Client异常
                    throw new ClientException(
                        "No such File object",
                        new Request('post', 'chat/completions'),
                        new Response(
                            400,
                            ['Content-Type' => 'application/json'],
                            '{"error": {"request_id": "54dc32fd-968b-4aed-b6a8-ae63d6fda4d5", "type": "invalid_request_error", "message": "No such File object: file-fe-xxx."}}',
                        ),
                    );
                },
                function () {
                    return new Response(
                        200,
                        ['Content-Type' => 'application/json'],
                        '{"id": "file-fe-xxx", "bytes": 2055, "created_at": 1729065448, "filename": "FileManagerTest.php", "object": "file", "purpose": "file-extract", "status": "processed", "status_details": null}',
                    );
                }
            );
        $mockClient
            ->shouldReceive('request')
            ->with('DELETE', m::pattern('/^files\/file\-fe\-\w+/'), m::type('array'))
            ->andReturnUsing(function () {
                return new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    '{"object": "file", "deleted": true, "id": "file-fe-xxx"}',
                );
            });

        $service->setClient($mockClient);
        return $service;
    }

    public function testUpload() {
        $service = self::buildService();

        // 使用通义上传文件
        $fileService = new FileManager($service);
        $filepath = __DIR__.'/';
        $filename = 'FileManagerTest.php';

        try {
            $fileService->upload($filepath.$filename, $filename);
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertEquals(423, $e->getCode());
            $this->assertStringContainsString('something is wrong', $e->getMessage());
        }

        try {
            $fileService->upload($filepath.$filename, $filename);
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmFormatException::class, $e);
            $this->assertEquals(400, $e->getCode());
            $this->assertEquals('invalid_request_error', $e->getErrCode());
        }

        try {
            $fileService->upload('/'.$filename, $filename);
        } catch (\Exception $e) {
            $this->assertStringContainsString('文件不存在', $e->getMessage());
        }

        $fileInfo = $fileService->upload($filepath.$filename, $filename);
        $this->assertInstanceOf(File::class, $fileInfo);
    }

    public function testList() {
        $service = self::buildService();

        $fileService = new FileManager($service);

        try {
            $fileService->list();
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmRequesException::class, $e);
            $this->assertEquals(423, $e->getCode());
            $this->assertStringContainsString('something is wrong', $e->getMessage());
        }

        $list = $fileService->list();
        $this->assertEquals(3, $list->getCount());
    }

    public function testInfo() {
        $service = self::buildService();

        $fileService = new FileManager($service);

        try {
            $fileService->info('file-fe-xxx');
        } catch (\Exception $e) {
            $this->assertInstanceOf(LlmFormatException::class, $e);
            $this->assertEquals(400, $e->getCode());
            $this->assertStringContainsString("Throttling", $e->getMessage());
        }

        $info = $fileService->info('file-fe-xxx');
        $this->assertNull($info);

        $info = $fileService->info('file-fe-xxx');
        $this->assertInstanceOf(File::class, $info);
    }

    public function testDelete() {
        $service = self::buildService();

        $fileService = new FileManager($service);
        $res = $fileService->delete('file-fe-xxx');
        $this->assertEquals(true, $res);
    }
}
