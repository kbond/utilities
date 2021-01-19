<?php

namespace Zenstruck\Url;

use Zenstruck\Url\Exception\PathOutsideRoot;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Path extends Part
{
    private const DEFAULT_DELIMITER = '/';

    public function __construct(string $value)
    {
        parent::__construct(\implode('/', \array_map('rawurldecode', \explode('/', $value))));
    }

    /**
     * @return array The path exploded with $delimiter
     */
    public function segments(string $delimiter = self::DEFAULT_DELIMITER): array
    {
        return \array_filter(\explode($delimiter, $this->trim()));
    }

    /**
     * @param int $index 1-based
     */
    public function segment(int $index, ?string $default = null, string $delimiter = self::DEFAULT_DELIMITER): ?string
    {
        return $this->segments($delimiter)[$index - 1] ?? $default;
    }

    public function trim(): string
    {
        return \trim($this->toString(), '/');
    }

    public function rtrim(): string
    {
        return \rtrim($this->toString(), '/');
    }

    public function ltrim(): string
    {
        return \ltrim($this->toString(), '/');
    }

    public function absolute(): string
    {
        $path = \explode('/', $this->toString());
        $stack = [];

        foreach ($path as $segment) {
            $segment = \trim($segment);

            switch (true) {
                case '..' === $segment && empty($stack):
                    throw new PathOutsideRoot(\sprintf('Cannot resolve absolute path for "%s". It is outside of the root.', $this->toString()));
                case '..' === $segment:
                    \array_pop($stack);

                    continue 2;
                case '.' === $segment:
                case '' === $segment:
                    continue 2;
            }

            $stack[] = $segment;
        }

        $stack = \array_filter($stack);
        $trailingSlash = \count($stack) && '/' === \mb_substr($this->toString(), -1) ? '/' : '';

        return '/'.\ltrim(\implode('/', $stack), '/').$trailingSlash;
    }

    public function extension(): ?string
    {
        return \pathinfo($this->toString(), \PATHINFO_EXTENSION) ?: null;
    }

    public function encoded(): string
    {
        return \implode('/', \array_map('rawurlencode', \explode('/', $this->toString())));
    }

    public function isAbsolute(): bool
    {
        return 0 === \mb_strpos($this->toString(), '/');
    }

    public function append(string $path): string
    {
        if ('' === $path) {
            return $this->toString();
        }

        if ($this->isEmpty()) {
            return $path;
        }

        return $this->rtrim().'/'.\ltrim($path, '/');
    }

    public function prepend(string $path): string
    {
        if ('' === $path) {
            return $this->toString();
        }

        if ($this->isEmpty()) {
            return $path;
        }

        $ret = \rtrim($path, '/').'/'.$this->ltrim();

        if ('/' !== $ret[0] && $this->isAbsolute()) {
            // if current path is absolute, then returned path must also be absolute
            $ret = "/{$ret}";
        }

        return $ret;
    }
}
