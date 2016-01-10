# Pull an image

Using the [Docker client](../basic.md) you run

```php
        
$imageManager = $docker->getImageManager();

$ubuntuLatestImage = $imageManager->pull('ubuntu');

$php55Image = $imageManager->pull('php', '5.5');

```