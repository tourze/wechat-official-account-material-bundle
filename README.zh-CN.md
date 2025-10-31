# 微信公众号素材管理包

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/php-monorepo/php-monorepo/ci.yml?style=flat-square)](https://github.com/php-monorepo/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/php-monorepo/php-monorepo?style=flat-square)](https://codecov.io/gh/php-monorepo/php-monorepo)

管理微信公众号永久素材，包括同步、上传、删除和统计功能。

## 功能特性

- 从微信同步永久素材到本地数据库
- 上传本地文件到微信作为永久素材
- 删除本地记录时自动删除远程素材
- 跟踪素材数量统计
- 支持多种素材类型：图片、语音、视频、缩略图
- 定时任务自动同步素材数量

## 系统要求

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM
- tourze/wechat-official-account-bundle

## 安装

```bash
composer require tourze/wechat-official-account-material-bundle
```

## 快速开始

### 1. 注册 Bundle

如果使用 Symfony Flex，Bundle 会自动注册。否则，需要手动添加到 `config/bundles.php`：

```php
return [
    // ...
    WechatOfficialAccountMaterialBundle\WechatOfficialAccountMaterialBundle::class => ['all' => true],
];
```

### 2. 更新数据库结构

```bash
php bin/console doctrine:schema:update --force
```

### 3. 同步素材

同步所有账号的所有素材：

```bash
php bin/console wechat-official-account:material:sync
```

同步指定账号的素材：

```bash
php bin/console wechat-official-account:material:sync --account-id=123
```

## 配置

该包使用默认配置，无需额外设置。但是，您可以自定义以下方面：

### 数据库配置

该包创建两个主要表：
- `wechat_official_account_material` - 存储素材记录
- `wechat_official_account_material_count` - 存储每日统计

### 素材类型设置

每种素材类型都有特定约束：
- **IMAGE（图片）**：支持 bmp、png、jpeg、jpg、gif，最大 10MB
- **VOICE（语音）**：支持 mp3、wma、wav、amr，最大 2MB
- **VIDEO（视频）**：支持 mp4，最大 10MB
- **THUMB（缩略图）**：支持 jpg，最大 64KB

### 定时任务配置

素材数量同步命令通过定时任务每2小时自动运行一次。定时表达式为 `0 */2 * * *`，在包的服务定义中配置。

## 可用命令

### wechat-official-account:material:sync

从微信公众号同步永久素材到本地数据库。

**用法：**
```bash
php bin/console wechat-official-account:material:sync [选项]
```

**选项：**
- `--account-id=ACCOUNT-ID`：仅同步指定账号的素材

**说明：**
该命令从微信公众号获取所有永久素材并存储到本地数据库。它会遍历所有素材类型（图片、语音、视频、
缩略图）并批量同步。

### wechat:official-account:sync-material-count

获取并保存所有活跃账号的素材数量统计。

**用法：**
```bash
php bin/console wechat:official-account:sync-material-count
```

**说明：**
该命令从微信获取每种素材类型的总数并将统计信息存储到数据库。通过定时任务每2小时自动运行一次
（`0 */2 * * *`）。

## 素材类型

该包支持以下素材类型：

- **IMAGE（图片）**：bmp、png、jpeg、jpg、gif（最大 10MB）
- **VOICE（语音）**：mp3、wma、wav、amr（最大 2MB）
- **VIDEO（视频）**：mp4（最大 10MB）
- **THUMB（缩略图）**：jpg（最大 64KB）

## 实体类

### Material（素材）

表示永久素材，包含以下属性：
- 账号关联
- 素材类型（MaterialType 枚举）
- 微信媒体 ID
- 名称和 URL
- 可选的内容和本地文件引用
- 时间戳和 IP 追踪

### MaterialCount（素材数量）

跟踪每日素材数量统计：
- 账号关联
- 日期
- 各类素材数量（语音、视频、图片、图文）
- 时间戳

## 事件监听器

该包包含一个 `MaterialListener`，用于：
- 本地保存素材时自动上传到微信（postPersist）
- 本地删除素材时自动删除远程素材（preRemove）

## API 集成

该包提供用于微信 API 集成的请求类：
- `AddMaterialRequest` - 新增永久素材
- `BatchGetMaterialRequest` - 获取素材列表
- `DeleteMaterialRequest` - 删除永久素材
- `GetMaterialCountRequest` - 获取素材数量统计
- `UploadImageRequest` - 上传图片

## 高级用法

### 自定义素材处理

您可以通过监听 Doctrine 事件来扩展素材处理：

```php
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use WechatOfficialAccountMaterialBundle\Entity\Material;

#[AsDoctrineListener(event: Events::postPersist)]
class CustomMaterialListener
{
    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();
        if ($entity instanceof Material) {
            // 素材保存后的自定义处理
        }
    }
}
```

### 编程式素材管理

```php
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

// 创建新的素材记录
$material = new Material();
$material->setAccount($account);
$material->setType(MaterialType::IMAGE);
$material->setName('我的图片');
$material->setLocalFile('/path/to/image.jpg');

$entityManager->persist($material);
$entityManager->flush(); // 这会触发自动上传到微信
```

### 批量操作

对于批量操作，您可以临时禁用自动监听器：

```php
// 禁用监听器
$entityManager->getEventManager()->removeEventListener(
    [Events::postPersist, Events::preRemove], 
    $materialListener
);

// 执行批量操作
foreach ($materials as $material) {
    $entityManager->persist($material);
}
$entityManager->flush();

// 重新启用监听器
$entityManager->getEventManager()->addEventListener(
    [Events::postPersist, Events::preRemove], 
    $materialListener
);
```

## 贡献

详情请参阅 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

MIT 许可证（MIT）。详情请参阅[许可证文件](LICENSE)。