<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/3/28 15:16
 */

namespace OpenAI;

/**
 * @Description
 * AI 接口请求消息类
 *
 * @Class  : AIMessageContent
 * @Package: extend\slms\llm\model
 */
class ChatMessage
{
    const QUESTION_ROLE = 'user';
    const ANSWER_ROLE = 'assistant';
    const SYSTEM_ROLE = 'system';

    /**
     * @var array 消息结构
     * [
     *      1 => [
     *              1 => [
     *                      ['role' => 'user' ...], ['role' => 'ass'. ..]
     *                  ]
     *          ]
     * ]
     */
    private array $messages = [];
    /** @var array 提示词 */
    private array $promptMessage = [];
    /** @var int 对话序号 */
    private int $lastSeq = 1;
    /** @var string|null 对话组（外部定义） */
    private ?string $uuid;
    /** @var bool 多模态 */
    private bool $multiModel = false;
    /** @var bool 长文本 */
    private bool $longModel = false;
    /** @var array 图片列表 */
    private array $imgList = [];
    /** @var array 文件列表 */
    private array $fileList = [];

    public function __construct(?string $uuid = null) {
        $this->uuid = $uuid;
    }

    /**
     * 添加提示词
     *
     * @param string $content
     *
     * @return $this
     */
    public function appendPrompt(string $content): self
    {
        $this->promptMessage[] = self::promptArray($content);

        return $this;
    }

    /**
     * 添加消息
     *
     * @param string|array $question 问题
     * @param string $content  答案
     * @param int    $seq      对应序号
     * @param int    $index    重答（如果需要）
     *
     * @return $this
     */
    private function appendMeta($question, string $content = '', int $seq = -1, int $index = -1): self
    {
        $index = $index === -1 ? count($this->messages[$seq]??[])+1 : $index;

        if ($seq !== -1) {
            $this->lastSeq = $seq;
        } else {
            $this->lastSeq = count($this->messages)+1;
        }

        $this->messages[$this->lastSeq][$index][0] = is_array($question) ? $question : self::questionArray($question);
        !empty($content) && $this->messages[$this->lastSeq][$index][1] = self::answerArray($content);

        return $this;
    }

    public function append(string $question, string $content = '', int $seq = -1, int $index = -1): self
    {
        return $this->appendMeta($question, $content, $seq, $index);
    }

    public function appendImg(array $imgUrl, string $question, string $content = '', int $seq = -1, int $index = -1): self
    {
        if (empty($content)) {
            $this->imgList = array_merge($this->imgList, $imgUrl);
       }

        return $this->appendMeta($this->imageArray($imgUrl, $question), $content, $seq, $index);
    }

    public function appendFile(string $file, bool $save = false): self
    {
        if ($save) {
            $this->fileList[] = $file;
        }

        $this->promptMessage[] = $this->fileArray($file);

        return $this;
    }

    private function buildContent($content, string $type = 'text')
    {
        if (!is_string($content)) return $content;

        return $this->multiModel ? [
            'type' => $type,
            $type => $content,
        ] : $content;
    }

    private static function questionArray(string $question): array
    {
        return [self::QUESTION_ROLE => $question];
    }

    private function imageArray(array $imgUrls, string $question): array
    {
        $this->multiModel = true;

        $imgContent = [];
        foreach ($imgUrls as $imgUrl) {
            $imgContent[] = self::buildContent($imgUrl, 'image_url');
        }

        return [
            self::QUESTION_ROLE => [
                ...$imgContent,
                self::buildContent($question),
            ],
        ];
    }

    private function fileArray(string $file): array
    {
        $this->longModel = true;
        return [self::SYSTEM_ROLE  => 'fileid://'.$file];
    }

    private static function answerArray(string $answer): array
    {
        return [self::ANSWER_ROLE => $answer];
    }

    private static function promptArray(string $content): array
    {
        return [self::SYSTEM_ROLE => $content];
    }

    public function toArray(): array
    {
        $res = $this->promptMessage;
        foreach ($this->messages as $message) {
            foreach ($message as $item) {
                $res = array_merge($res, $item);
            }
        }

        foreach ($res as &$elem) {
            foreach ($elem as $k => $v) {
                $content = self::buildContent($v);
                if (is_array($content) && !is_array(current($content))) {
                    $content = [$content];
                }
                $elem = ['role' => $k, 'content' => $content];
            }
        }

        return $res;
    }

    public function getLastSeq(): int
    {
        return $this->lastSeq;
    }

    public function getLastIndex(): int
    {
        return intval(max(array_keys($this->messages[$this->getLastSeq()])));
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * 是否为新问题（没有上下文，且不是重答）
     *
     * @return bool
     */
    public function isNew(): bool
    {
        return $this->getLastSeq() === 1 && $this->getLastIndex() === 1;
    }

    /**
     * 获取最后一个问题
     *
     * @return string|null
     */
    public function getLastQuestion(): ?string
    {
        $lastQuestion = current($this->messages[$this->getLastSeq()][$this->getLastIndex()][0]);
        if (is_array($lastQuestion)) {
            foreach ($lastQuestion as $item) {
                if ($item['type'] === 'text') {
                    return $item['text'];
                }
            }
        } else {
            return $lastQuestion;
        }

        return null;
    }

    public function getFile(): array
    {
        return $this->fileList;
    }

    public function getImg(): array
    {
        return $this->imgList;
    }

    public function isVisionModel(): bool
    {
        return $this->multiModel;
    }

    public function isFileModel(): bool
    {
        return $this->longModel;
    }
}
