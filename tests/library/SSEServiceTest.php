<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/13 12:01
 */

namespace Tests\library;

use OpenAI\library\SSEService;
use PHPUnit\Framework\TestCase;

use Mockery as m;

class SSEServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * 独立进程运行，避免对全局 ini/header 影响其他测试
     *
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testIniSettingsAreApplied()
    {
        // 在调用前确保没有启动缓冲
        while (@ob_end_flush()) { continue; }
        // 清除旧头
        xdebug_get_headers();

        // 调用目标方法
        SSEService::init();

        // 验证 ini_set 的效果
        $this->assertSame('false', ini_get('zlib.output_compression'));
        $sent = implode(';', xdebug_get_headers());
        $this->assertStringContainsString('text/event-stream', $sent);

        // ob_start() 后，缓冲层级至少为 1
        $level = ob_get_level();
        $this->assertGreaterThanOrEqual(1, $level, "调用 init() 后 ob_get_level() 应 ≥ 1，实际为 {$level}");
    }

    public function testEcho() {
        ob_start();
        $this->expectOutputString("event: test_event\ndata: message_body\n\ndata: message_body\n\n");

        SSEService::echo('test_event', 'message_body');

        SSEService::echo('message_body');

        ob_end_flush();
    }

    public function testSuccess() {
        ob_start();
        $this->expectOutputString("event: end_success\ndata: message_body\n\n");

        SSEService::success('message_body');

        ob_end_flush();
    }

    public function testFail() {
        ob_start();
        $this->expectOutputString("event: end_error\ndata: message_body\n\n");

        SSEService::fail('message_body');

        ob_end_flush();
    }
}
