# Remove an image

Using the [Docker client](../basic.md) you run

```php
$imageManager = $docker->getImageManager();

$image1 = $manager->find('ubuntu', 'vivid');

$manager->remove($image1);

// OR

$image2 = new Docker\Image();
$image2->setId('69c02692b0c1');

$manager->remove($image2);
```

The `remove` method has a second argument `$force` and a third one `$noprune`, both default to `false`.

# Remove multiple images

You can remove multiple images at once by passing an array of `Docker\Image` instances or strings (image id or image repository name and repository tag) to the `removeImages()` method.

```php
$manager->removeImages([$image, 'ubuntu:vivid', '69c02692b0c1']);
```

The method has the same second (`$force`) and third (`$noprune`) argument as the `remove` method.

Keep in mind that all containers which are based on these images have to be removed before the image itself can be removed.