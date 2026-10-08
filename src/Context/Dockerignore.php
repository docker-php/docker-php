<?php

declare(strict_types=1);

namespace Docker\Context;

/**
 * Apply .dockerignore patterns the way the Docker CLI does.
 *
 * Patterns use Go's filepath.Match syntax plus `**`, a leading `!` re-includes
 * paths, and the last matching pattern wins. A pattern that matches a directory
 * also matches everything inside it.
 *
 * @see https://docs.docker.com/build/concepts/context/#dockerignore-files
 */
final class Dockerignore
{
    /** @var list<array{regex: string, exclusion: bool}> */
    private array $patterns = [];

    private bool $hasExclusions = false;

    /**
     * @param list<string> $keep Paths the daemon always needs, re-included as the Docker CLI does
     */
    public function __construct(string $content, array $keep = ['Dockerfile', '.dockerignore'])
    {
        foreach (preg_split('/\r\n|\n|\r/', $content) as $line) {
            // Comments are only recognised at the very start of a line.
            if (str_starts_with($line, '#')) {
                continue;
            }
            $this->addPattern(trim($line));
        }
        foreach ($keep as $path) {
            $this->addPattern('!'.$path);
        }
    }

    public static function fromFile(string $file): self
    {
        $content = file_get_contents($file);
        if (false === $content) {
            throw new \RuntimeException(\sprintf('Cannot read %s.', $file));
        }

        // Ignore a UTF-8 byte order mark, as Docker does.
        return new self(preg_replace('/^\xEF\xBB\xBF/', '', $content));
    }

    /**
     * Whether a path, relative to the context root with `/` separators, is excluded.
     */
    public function excludes(string $path): bool
    {
        $parts = explode('/', $path);
        $excluded = false;
        foreach ($this->patterns as $pattern) {
            $matches = 1 === preg_match($pattern['regex'], $path);
            // A pattern that matches a parent directory also matches its contents.
            for ($i = 1; !$matches && $i < \count($parts); ++$i) {
                $matches = 1 === preg_match($pattern['regex'], implode('/', \array_slice($parts, 0, $i)));
            }
            if ($matches) {
                $excluded = !$pattern['exclusion'];
            }
        }

        return $excluded;
    }

    /**
     * List the paths to archive, relative to $directory and sorted.
     *
     * Directories are listed before their contents. An excluded directory is
     * left out, but its re-included contents are still listed.
     *
     * @return list<string>
     */
    public function paths(string $directory): array
    {
        $paths = [];
        $this->walk(rtrim($directory, '/'), '', $paths);

        return $paths;
    }

    private function walk(string $directory, string $relative, array &$paths): void
    {
        $entries = scandir('' === $relative ? $directory : $directory.'/'.$relative);
        if (false === $entries) {
            throw new \RuntimeException(\sprintf('Cannot read the build context directory %s.', $directory.'/'.$relative));
        }
        sort($entries, \SORT_STRING);

        foreach ($entries as $name) {
            if ('.' === $name || '..' === $name) {
                continue;
            }
            $path = '' === $relative ? $name : $relative.'/'.$name;
            $full = $directory.'/'.$path;
            // Symbolic links are archived as links, not followed.
            $isDirectory = is_dir($full) && !is_link($full);

            if ($this->excludes($path)) {
                if ($isDirectory && $this->hasExclusions) {
                    $this->walk($directory, $path, $paths);
                }
                continue;
            }

            $paths[] = $path;
            if ($isDirectory) {
                $this->walk($directory, $path, $paths);
            }
        }
    }

    private function addPattern(string $pattern): void
    {
        $exclusion = str_starts_with($pattern, '!');
        if ($exclusion) {
            $pattern = trim(substr($pattern, 1));
        }
        if ('' === $pattern) {
            return;
        }

        $pattern = self::clean($pattern);
        if ($exclusion) {
            $this->hasExclusions = true;
        }
        $this->patterns[] = ['regex' => self::compile($pattern), 'exclusion' => $exclusion];
    }

    /**
     * Go's filepath.Clean, then strip a leading `/`: patterns are relative to the context root.
     */
    private static function clean(string $pattern): string
    {
        $parts = [];
        foreach (explode('/', $pattern) as $part) {
            if ('' === $part || '.' === $part) {
                continue;
            }
            if ('..' === $part) {
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }

        return [] === $parts ? '.' : implode('/', $parts);
    }

    /**
     * Translate a pattern to a regular expression, following moby/patternmatcher.
     */
    private static function compile(string $pattern): string
    {
        $regex = '';
        $inClass = false;
        $length = \strlen($pattern);
        for ($i = 0; $i < $length; ++$i) {
            $char = $pattern[$i];
            if ($inClass) {
                // Character classes pass through, including ranges and `^` negation.
                if (']' === $char) {
                    $inClass = false;
                } elseif ('#' === $char) {
                    $char = '\\#';
                } elseif ('\\' === $char && $i + 1 < $length) {
                    $char .= $pattern[++$i];
                }
                $regex .= $char;
            } elseif ('[' === $char) {
                $inClass = true;
                $regex .= $char;
            } elseif ('*' === $char) {
                if ('*' === ($pattern[$i + 1] ?? '')) {
                    ++$i;
                    // Treat "**/" as "**".
                    if ('/' === ($pattern[$i + 1] ?? '')) {
                        ++$i;
                    }
                    // A trailing "**" matches everything; otherwise any number of directories.
                    $regex .= $i + 1 >= $length ? '.*' : '(.*/)?';
                } else {
                    $regex .= '[^/]*';
                }
            } elseif ('?' === $char) {
                $regex .= '[^/]';
            } elseif ('\\' === $char) {
                $regex .= $i + 1 < $length ? preg_quote($pattern[++$i], '#') : '\\\\';
            } else {
                $regex .= preg_quote($char, '#');
            }
        }

        $regex = '#^'.$regex.'$#';
        if ($inClass || false === @preg_match($regex, '')) {
            throw new \InvalidArgumentException(\sprintf('Invalid .dockerignore pattern "%s".', $pattern));
        }

        return $regex;
    }
}
