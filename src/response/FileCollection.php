<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/5/7 18:00
 */

namespace OpenAI\response;

class FileCollection implements \IteratorAggregate
{
    /** @var array[File] $file */
    private array $files;

    public function getFileList(): array
    {
        return $this->files;
    }

    private function __construct() {}

    public static function init(array $data): self
    {
        $obj = new FileCollection;

        foreach ($data as $item) {
            $obj->files[] = File::init($item);
        }

        return $obj;
    }

    public function getIterator(): \Generator
    {
        foreach ($this->files as $file) {
            yield $file;
        }
    }

    public function append(self $data)
    {
        $this->files = array_merge($this->files, $data->getFileList());
    }

    public function getLastId(): string
    {
        return $this->files[count($this->files) - 1]->getId();
    }
}
