# WeChat Official Account Material Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-official-account-material-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-material-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/php-monorepo/php-monorepo/ci.yml?style=flat-square)](https://github.com/php-monorepo/php-monorepo/actions)
[![Code Coverage](https://img.shields.io/codecov/c/github/php-monorepo/php-monorepo?style=flat-square)](https://codecov.io/gh/php-monorepo/php-monorepo)

Manage WeChat Official Account permanent materials, including sync, upload, delete and statistics.

## Features

- Sync permanent materials from WeChat to local database
- Upload local files to WeChat as permanent materials
- Automatically delete remote materials when deleting local records
- Track material count statistics
- Support multiple material types: image, voice, video, thumbnail
- Scheduled task for automatic material count synchronization

## Requirements

- PHP 8.1+
- Symfony 6.4+
- Doctrine ORM
- tourze/wechat-official-account-bundle

## Installation

```bash
composer require tourze/wechat-official-account-material-bundle
```

## Quick Start

### 1. Register the Bundle

The bundle should be automatically registered if you're using Symfony Flex. Otherwise, add it to `config/bundles.php`:

```php
return [
    // ...
    WechatOfficialAccountMaterialBundle\WechatOfficialAccountMaterialBundle::class => ['all' => true],
];
```

### 2. Update Database Schema

```bash
php bin/console doctrine:schema:update --force
```

### 3. Sync Materials

Sync all materials from all accounts:

```bash
php bin/console wechat-official-account:material:sync
```

Sync materials from a specific account:

```bash
php bin/console wechat-official-account:material:sync --account-id=123
```

## Configuration

The bundle uses default configurations and doesn't require additional setup. However, you can customize 
the following aspects:

### Database Configuration

The bundle creates two main tables:
- `wechat_official_account_material` - Stores material records
- `wechat_official_account_material_count` - Stores daily statistics

### Material Type Settings

Each material type has specific constraints:
- **IMAGE**: Supports bmp, png, jpeg, jpg, gif with max 10MB
- **VOICE**: Supports mp3, wma, wav, amr with max 2MB  
- **VIDEO**: Supports mp4 with max 10MB
- **THUMB**: Supports jpg with max 64KB

### Cron Job Configuration

The material count sync command runs automatically every 2 hours via cron job. The cron expression 
is `0 */2 * * *` and is configured in the bundle's service definition.

## Available Commands

### wechat-official-account:material:sync

Synchronize permanent materials from WeChat Official Account to local database.

**Usage:**
```bash
php bin/console wechat-official-account:material:sync [options]
```

**Options:**
- `--account-id=ACCOUNT-ID`: Sync materials for a specific account only

**Description:**
This command fetches all permanent materials from WeChat Official Account(s) and stores them in the 
local database. It iterates through all material types (image, voice, video, thumb) and syncs them 
in batches.

### wechat:official-account:sync-material-count

Get and save material count statistics for all active accounts.

**Usage:**
```bash
php bin/console wechat:official-account:sync-material-count
```

**Description:**
This command retrieves the total count of each material type from WeChat and stores the statistics 
in the database. It runs automatically every 2 hours via cron job (`0 */2 * * *`).

## Material Types

The bundle supports the following material types:

- **IMAGE**: bmp, png, jpeg, jpg, gif (max 10MB)
- **VOICE**: mp3, wma, wav, amr (max 2MB)  
- **VIDEO**: mp4 (max 10MB)
- **THUMB**: jpg (max 64KB)

## Entity Classes

### Material

Represents a permanent material with the following properties:
- Account association
- Material type (MaterialType enum)
- Media ID from WeChat
- Name and URL
- Optional content and local file reference
- Timestamps and IP tracking

### MaterialCount

Tracks daily material count statistics:
- Account association
- Date
- Count for each material type (voice, video, image, news)
- Timestamps

## Event Listeners

The bundle includes a `MaterialListener` that:
- Automatically uploads materials to WeChat when saved locally (postPersist)
- Automatically deletes remote materials when deleted locally (preRemove)

## API Integration

The bundle provides request classes for WeChat API integration:
- `AddMaterialRequest` - Add new permanent material
- `BatchGetMaterialRequest` - Get material list
- `DeleteMaterialRequest` - Delete permanent material
- `GetMaterialCountRequest` - Get material count statistics
- `UploadImageRequest` - Upload image

## Advanced Usage

### Custom Material Processing

You can extend the material processing by listening to Doctrine events:

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
            // Custom processing after material is saved
        }
    }
}
```

### Programmatic Material Management

```php
use WechatOfficialAccountMaterialBundle\Entity\Material;
use WechatOfficialAccountMaterialBundle\Enum\MaterialType;

// Create a new material record
$material = new Material();
$material->setAccount($account);
$material->setType(MaterialType::IMAGE);
$material->setName('My Image');
$material->setLocalFile('/path/to/image.jpg');

$entityManager->persist($material);
$entityManager->flush(); // This triggers automatic upload to WeChat
```

### Batch Operations

For bulk operations, you can disable automatic listeners temporarily:

```php
// Disable listeners
$entityManager->getEventManager()->removeEventListener(
    [Events::postPersist, Events::preRemove], 
    $materialListener
);

// Perform batch operations
foreach ($materials as $material) {
    $entityManager->persist($material);
}
$entityManager->flush();

// Re-enable listeners
$entityManager->getEventManager()->addEventListener(
    [Events::postPersist, Events::preRemove], 
    $materialListener
);
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.