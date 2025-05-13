<?php
/**
 * @Author   : Yven<yvenchang@163.com>
 * @Copyright: Yven<yvenchang@163.com>
 * @Link     : https://www.yvenchang.cn
 * @Created  : 2025/4/23 15:12
 */

namespace OpenAI\model;

use OpenAI\constant\Model;
use OpenAI\exception\LlmRequesException;

class Qwen extends OpenAI
{
    protected string $host = 'https://dashscope.aliyuncs.com/compatible-mode/v1/';
    protected int $defaultModel = Model::QWEN_PLUS;
    protected static array $httpCodeErrorMessage = [
        401 => '请求中的 API Key 错误。请重新获取API Key。',
        402 => '无权访问此 API，请前往百炼控制台查看具体问题。',
        404 => '模型不存在',
        408 => "请求超时",
        413 => '请求体过大错误',
        415 => '下载输入文件失败，可能是由于输入文件的格式不受支持、下载超时或者文件超过限额大小。',
        500 => 'Qwen服务器故障',
        503 => 'Qwen暂时无法提供服务',
    ];

    /**
     * 设置 AI 模型
     *
     * @param int $model
     *
     * @return $this
     * @throws \Exception
     */
    public function withModel(int $model): self
    {
        parent::withModel($model);

        if ($model === Model::QWEN_3) {
            // Qwen3 启用推理模式
            $this->body['enable_thinking'] = true;
        } else {
            unset($this->body['enable_thinking']);
        }

        return $this;
    }

    /**
     * 发送请求前的检查
     *
     * @return bool
     * @throws LlmRequesException
     */
    public function check(): bool
    {
        switch ($this->body['model']) {
            case Model::getCode(Model::QWEN_LONG):
                // 文件模型
                if (!$this->messages->isFileModel()) {
                    throw new LlmRequesException("请上传文件，或切换为文本模型");
                }
                break;
            case Model::getCode(Model::QWEN_VL_PLUS):
                // 图片模型
                if (!$this->messages->isVisionModel()) {
                    throw new LlmRequesException("请上传图片，或切换为文本模型");
                }
                break;
        }

        return true;
    }

    public function withAutoModel(int $model = null): self
    {
        // 特殊模式选择模型
        if ($this->messages->isVisionModel()) {
            $this->withModel(Model::QWEN_VL_PLUS);
        } else if ($this->messages->isFileModel()) {
            $this->withModel(Model::QWEN_LONG);
        } else {
            $this->withModel($model ?? $this->defaultModel);
        }

        return $this;
    }
}
