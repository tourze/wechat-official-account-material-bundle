<?php

namespace WechatOfficialAccountMaterialBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountMaterialBundle\Repository\MaterialCountRepository;

#[ORM\Entity(repositoryClass: MaterialCountRepository::class)]
#[ORM\Table(name: 'wechat_official_account_material_count', options: ['comment' => '素材总数'])]
class MaterialCount implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return sprintf('素材总数-%s', $this->date?->format('Y-m-d') ?? 'Unknown');
    }

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Account $account;

    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    #[Assert\NotNull]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(nullable: true, options: ['comment' => '语音总数量'])]
    #[Assert\PositiveOrZero]
    private ?int $voiceCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '视频总数量'])]
    #[Assert\PositiveOrZero]
    private ?int $videoCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图片总数量'])]
    #[Assert\PositiveOrZero]
    private ?int $imageCount = null;

    #[ORM\Column(nullable: true, options: ['comment' => '图文总数量'])]
    #[Assert\PositiveOrZero]
    private ?int $newsCount = null;

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getVoiceCount(): ?int
    {
        return $this->voiceCount;
    }

    public function setVoiceCount(?int $voiceCount): void
    {
        $this->voiceCount = $voiceCount;
    }

    public function getVideoCount(): ?int
    {
        return $this->videoCount;
    }

    public function setVideoCount(?int $videoCount): void
    {
        $this->videoCount = $videoCount;
    }

    public function getImageCount(): ?int
    {
        return $this->imageCount;
    }

    public function setImageCount(?int $imageCount): void
    {
        $this->imageCount = $imageCount;
    }

    public function getNewsCount(): ?int
    {
        return $this->newsCount;
    }

    public function setNewsCount(?int $newsCount): void
    {
        $this->newsCount = $newsCount;
    }
}
