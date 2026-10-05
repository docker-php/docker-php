<?php

declare(strict_types=1);

$directory = dirname(__DIR__);
$pages = new RecursiveIteratorIterator(new RecursiveCallbackFilterIterator(
    new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
    static fn (SplFileInfo $entry): bool => !$entry->isDir()
        || !in_array($entry->getFilename(), ['node_modules', '.mintlify'], true)
));
$count = 0;

foreach ($pages as $page) {
    if (!$page->isFile() || $page->getExtension() !== 'mdx') {
        continue;
    }

    $relativePath = substr($page->getPathname(), strlen($directory) + 1);
    preg_match_all('/^```php[^\r\n]*\R(.*?)^```/ms', file_get_contents($page->getPathname()), $blocks);

    foreach ($blocks[1] as $index => $code) {
        $code = preg_replace('/^\s*<\?php\s*/', '', $code);

        try {
            token_get_all("<?php\n".$code, TOKEN_PARSE);
        } catch (ParseError $error) {
            fwrite(STDERR, sprintf("%s, PHP example %d: %s\n", $relativePath, $index + 1, $error->getMessage()));
            exit(1);
        }

        ++$count;
    }
}

if ($count === 0) {
    fwrite(STDERR, "No PHP documentation examples found.\n");
    exit(1);
}

printf("Checked the syntax of %d PHP documentation examples on PHP %s.\n", $count, PHP_VERSION);
