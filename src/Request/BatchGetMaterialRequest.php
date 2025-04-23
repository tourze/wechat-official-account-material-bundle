<?php

namespace WechatOfficialAccountMaterialBundle\Request;

use WechatOfficialAccountBundle\Request\WithAccountRequest;

/**
 * 获取素材列表
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_materials_list.html
 */
class BatchGetMaterialRequest extends WithAccountRequest
{
    /**
     * @var string 素材的类型，图片（image）、视频（video）、语音 （voice）、图文（news）
     */
    private string $type;

    /**
     * @var int 从全部素材的该偏移位置开始返回，0表示从第一个素材 返回
     */
    private int $offset = 0;

    /**
     * @var int 返回素材的数量，取值在1到20之间
     */
    private int $count = 20;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/cgi-bin/material/batchget_material';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'type' => $this->getType(),
                'offset' => $this->getOffset(),
                'count' => $this->getCount(),
            ],
        ];
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }
}
