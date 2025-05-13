<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/13 15:46
 */

namespace Tests\constant;

use OpenAI\constant\Result;
use PHPUnit\Framework\TestCase;

class ResultTest extends TestCase
{
    public function testGetNameList() {
        $list = Result::getNameList();
        $this->assertCount(5, $list);
    }
}
