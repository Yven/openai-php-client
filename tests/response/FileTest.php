<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/10 20:24
 */

namespace Tests\response;

use OpenAI\response\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetFunc()
    {
        $fileInfo = json_decode('{"id": "file-fe-xxx", "bytes": 2055, "created_at": 1729065448, "filename": "FileManagerTest.php", "object": "file", "purpose": "file-extract", "status": "processed", "status_details": null}', true);
        $file = File::init($fileInfo);

        $this->assertEquals($fileInfo['id'], $file->getId());
        $this->assertEquals($fileInfo['bytes'], $file->getBytes());
        $this->assertEquals($fileInfo['created_at'], $file->getCreatedAt());
        $this->assertEquals($fileInfo['filename'], $file->getFilename());
        $this->assertEquals($fileInfo['object'], $file->getObject());
        $this->assertEquals($fileInfo['purpose'], $file->getPurpose());
        $this->assertEquals($fileInfo['status'], $file->getStatus());
        $this->assertEquals($fileInfo['status_details'], $file->getStatusDetails());
    }

    public function testToArray()
    {
        $fileInfo = json_decode('{"id": "file-fe-xxx", "bytes": 2055, "created_at": 1729065448, "filename": "FileManagerTest.php", "object": "file", "purpose": "file-extract", "status": "processed", "status_details": null}', true);
        $file = File::init($fileInfo);

        $fileArr = $file->toArray();
        $this->assertEquals($fileInfo['id'], $fileArr['id']);
    }
}
