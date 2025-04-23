<?php

namespace WechatOfficialAccountMaterialBundle\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use WechatOfficialAccountBundle\Request\WithAccountRequest;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

/**
 * 新增永久素材
 *
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Adding_Permanent_Assets.html
 */
class AddMaterialRequest extends WithAccountRequest
{
    private UploadedFile $file;

    private MaterialType $type;

    public function getRequestPath(): string
    {
        return 'https://api.weixin.qq.com/cgi-bin/material/add_material';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => fopen($this->getFile()->getPath(), 'r'),
                ],
                [
                    'name' => 'type',
                    'contents' => $this->getType()->value,
                ],
            ],
        ];
    }

    public function getFile(): UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): void
    {
        $this->file = $file;
    }

    public function getType(): MaterialType
    {
        return $this->type;
    }

    public function setType(MaterialType $type): void
    {
        $this->type = $type;
    }
}
