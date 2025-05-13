<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/27 11:38
 */

namespace OpenAI\response;

class File
{
    private string $fileId;
    private int $bytes;
    private int $createdAt;
    private string $filename;
    private string $object;
    private string $purpose;
    private string $status;
    private ?string $statusDetails = null;

    public function getId(): string
    {
        return $this->fileId;
    }

    public function getBytes(): int
    {
        return $this->bytes;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStatusDetails(): ?string
    {
        return $this->statusDetails;
    }

    private function __construct() {}

    public static function init(array $data): self
    {
        if (!isset($data['id']) || !isset($data['bytes']) || !isset($data['created_at']) || !isset($data['filename']) || !isset($data['object']) || !isset($data['purpose']) || !isset($data['status'])) throw new \Exception("缺少参数");

        $obj = new File;
        $obj->fileId = $data['id'];
        $obj->bytes = $data['bytes'];
        $obj->createdAt = $data['created_at'];
        $obj->filename = $data['filename'];
        $obj->object = $data['object'];
        $obj->purpose = $data['purpose'];
        $obj->status = $data['status'];
        $obj->statusDetails = $data['status_details'] ?? null;

        return $obj;
    }
}
