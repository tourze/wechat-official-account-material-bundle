<?php

namespace WechatOfficialAccountMaterialBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Repository\MaterialCountRepository;

#[AsPermission(title: '素材总数')]
#[Deletable]
#[ORM\Entity(repositoryClass: MaterialCountRepository::class)]
#[ORM\Table(name: 'wechat_official_account_material_count', options: ['comment' => '素材总数'])]
class MaterialCount
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: true, options: ['comment' => '语音总数量'])]
    private ?int $voiceCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '视频总数量'])]
    private ?int $videoCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图片总数量'])]
    private ?int $imageCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图文总数量'])]
    private ?int $newsCount = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getVoiceCount(): ?int
    {
        return $this->voiceCount;
    }

    public function setVoiceCount(?int $voiceCount): static
    {
        $this->voiceCount = $voiceCount;

        return $this;
    }

    public function getVideoCount(): ?int
    {
        return $this->videoCount;
    }

    public function setVideoCount(?int $videoCount): static
    {
        $this->videoCount = $videoCount;

        return $this;
    }

    public function getImageCount(): ?int
    {
        return $this->imageCount;
    }

    public function setImageCount(?int $imageCount): static
    {
        $this->imageCount = $imageCount;

        return $this;
    }

    public function getNewsCount(): ?int
    {
        return $this->newsCount;
    }

    public function setNewsCount(?int $newsCount): static
    {
        $this->newsCount = $newsCount;

        return $this;
    }

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }
}
