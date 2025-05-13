<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/13 09:45
 */

namespace Tests;

use OpenAI\ChatMessage;
use PHPUnit\Framework\TestCase;

class ChatMessageTest extends TestCase
{
    public function testGetLastQuestion() {
        $message = new ChatMessage;

        $this->assertNull($message->getLastQuestion());
    }

    public function testIsNew() {
        $message = new ChatMessage;

        $message->append('你是谁');
        $this->assertEquals(true, $message->isNew());
    }

    public function testAppendPrompt() {
        $message = new ChatMessage;
        $prompt = 'You are a good man';
        $question = '你是谁';

        $message->appendPrompt($prompt)->append($question);
        $msgOutput = $message->toArray();
        $this->assertEquals('system', $msgOutput[0]['role']);
        $this->assertEquals($prompt, $msgOutput[0]['content']);
        $this->assertEquals('user', $msgOutput[1]['role']);
        $this->assertEquals($question, $msgOutput[1]['content']);
    }

    public function testAppend() {
        $message = new ChatMessage;

        $dbMessageData = [
            [
                'question' => '你好，请问你可以做什么？',
                'content' => '我是阿里云开发的一款超大规模语言模型，我叫通义千问。',
                'seq' => 1,
                'index' => 1,
            ],
            [
                'question' => '你有什么特点？',
                'content' => '我是一个智能语言模型，你可以向我提问任何问题，我将尽力回答。',
                'seq' => 2,
                'index' => 1,
            ],
            [
                'question' => '你有什么特点？',
                'content' => '你可以向我提问任何问题，我是一个智能语言模型。',
                'seq' => 2,
                'index' => 2,
            ],
            [
                'question' => '你有什么优势？',
                'content' => '没有',
                'seq' => 3,
                'index' => 1,
            ],
        ];

        foreach ($dbMessageData as $item) {
            $message->append($item['question'], $item['content'], $item['seq'], $item['index']);
        }
        $message->append('感谢你');

        $msgOutput = $message->toArray();
        $this->assertCount(9, $msgOutput);
        $this->assertStringContainsString('你可以向我提问任何问题，我是一个智能语言模型。', $msgOutput[5]['content']);

        $message = new ChatMessage;

        $message->appendImg(['https://img/template.png'], '解释图中内容', '我也不懂')->append('感谢你');
        $msgOutput = $message->toArray();
        $this->assertStringContainsString('image_url', $msgOutput[0]['content'][0]['type']);
    }
}
