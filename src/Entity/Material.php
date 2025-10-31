<?php

namespace WechatOfficialAccountMaterialBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;
use WechatOfficialAccountMaterialBundle\Repository\MaterialRepository;

/**
 * @see https://developers.weixin.qq.com/doc/offiaccount/Asset_Management/Get_materials_list.html
 *
 * 最近更新：永久图片素材新增后，将带有URL返回给开发者，开发者可以在腾讯系域名内使用（腾讯系域名外使用，图片将被屏蔽）。
 * 公众号的素材库保存总数量有上限：图文消息素材、图片素材上限为100000，其他类型为1000。
 */
#[ORM\Entity(repositoryClass: MaterialRepository::class)]
#[ORM\Table(name: 'wechat_official_account_material', options: ['comment' => '永久素材'])]
class Material implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ORM\Column(length: 20, enumType: MaterialType::class, options: ['comment' => '媒体文件类型'])]
    #[Assert\Choice(callback: [MaterialType::class, 'cases'])]
    private ?MaterialType $type = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '永久素材的media_id'])]
    #[Assert\Length(max: 120)]
    private ?string $mediaId = null;

    #[ORM\Column(length: 120, nullable: true, options: ['comment' => '素材名称'])]
    #[Assert\Length(max: 120)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '图片素材的图片URL'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $url = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '素材内容'])]
    #[Assert\Type(type: 'array')]
    private ?array $content = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '本地文件路径'])]
    #[Assert\Length(max: 255)]
    private ?string $localFile = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否正在同步中', 'default' => false])]
    #[Assert\Type(type: 'bool')]
    private bool $syncing = false;

    public function __toString(): string
    {
        return $this->name ?? $this->mediaId ?? (string) $this->id;
    }

    public function isSyncing(): bool
    {
        return $this->syncing;
    }

    public function setSyncing(bool $syncing): void
    {
        $this->syncing = $syncing;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getType(): ?MaterialType
    {
        return $this->type;
    }

    public function setType(MaterialType $type): void
    {
        $this->type = $type;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function setMediaId(?string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getContent(): ?array
    {
        return $this->content;
    }

    /**
     * @param array<string, mixed>|null $content
     */
    public function setContent(?array $content): void
    {
        $this->content = $content;
    }

    public function getLocalFile(): ?string
    {
        return $this->localFile;
    }

    public function setLocalFile(?string $localFile): void
    {
        $this->localFile = $localFile;
    }
}
