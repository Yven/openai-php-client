<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/13 12:01
 */

namespace Tests\library;

use OpenAI\library\Helper;
use PHPUnit\Framework\TestCase;

use Mockery as m;

class HelperTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testArrayItemAdd() {
        $arr1 = ['a' => 'a', 'c' => 1];
        $arr2 = ['b' => 'b'];

        $res = Helper::arrayItemAdd($arr2, $arr1);
        $this->assertCount(2, $res);
        $this->assertEquals(0, $res['a']);

        $arr1 = [
            'a' => [
                'b' => 1,
                'c' => 2,
            ],
            'd' => 3,
        ];
        $arr2 = [
            'a' => [
                'b' => 2,
            ],
            'd' => 4,
        ];

        $res = Helper::arrayItemAdd($arr2, $arr1);
        $this->assertCount(2, $res);
        $this->assertCount(2, $res['a']);
        $this->assertEquals(7, $res['d']);
    }
}
