<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/10 20:24
 */

namespace Tests\response;

use OpenAI\response\FileCollection;
use PHPUnit\Framework\TestCase;

class FileCollectionTest extends TestCase
{
    public function testGetIterator()
    {
        $fileInfo = json_decode('{"id": "file-fe-xxx", "bytes": 2055, "created_at": 1729065448, "filename": "FileManagerTest.php", "object": "file", "purpose": "file-extract", "status": "processed", "status_details": null}', true);
        $files = FileCollection::init([$fileInfo, $fileInfo]);

        foreach ($files as $file) {
            $this->assertEquals($fileInfo['id'], $file->getId());
        }
    }
}
