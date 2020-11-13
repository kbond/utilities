<?php

namespace Zenstruck\Utilities;

use Zenstruck\Utilities\Url\Authority;
use Zenstruck\Utilities\Url\Host;
use Zenstruck\Utilities\Url\Path;
use Zenstruck\Utilities\Url\Query;
use Zenstruck\Utilities\Url\Scheme;

/**
 * Wrapper for parse_url().
 *
 * @experimental
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Url implements \Stringable
{
    /** @var Scheme */
    private $scheme;

    /** @var Authority */
    private $authority;

    /** @var Path */
    private $path;

    /** @var Query */
    private $query;

    /** @var string */
    private $fragment;

    public function __construct(?string $value = null)
    {
        if (false === $components = \parse_url($value)) {
            throw new \InvalidArgumentException("Unable to parse \"{$value}\".");
        }

        $this->scheme = new Scheme($components['scheme'] ?? '');
        $this->path = new Path($components['path'] ?? '');
        $this->query = new Query($components['query'] ?? []);
        $this->fragment = \rawurldecode($components['fragment'] ?? '');
        $this->authority = new Authority(
            $components['host'] ?? '',
            $components['user'] ?? null,
            $components['pass'] ?? null,
            $components['port'] ?? null
        );
    }

    public function __toString(): string
    {
        $ret = '';

        if ('' !== $scheme = $this->scheme->value()) {
            $ret .= "{$scheme}:";
        }

        if ($authority = (string) $this->authority) {
            $ret .= "//{$authority}";
        }

        $ret .= $this->path->encoded();

        if ('' !== $query = (string) $this->query) {
            $ret .= "?{$query}";
        }

        if ('' !== $this->fragment) {
            $ret .= '#'.\rawurlencode($this->fragment);
        }

        return $ret;
    }

    public function scheme(): Scheme
    {
        return $this->scheme;
    }

    public function host(): Host
    {
        return $this->authority->host();
    }

    public function port(): ?int
    {
        return $this->authority->port();
    }

    public function user(): ?string
    {
        return $this->authority->username();
    }

    public function pass(): ?string
    {
        return $this->authority->password();
    }

    public function path(): Path
    {
        return $this->path;
    }

    public function query(): Query
    {
        return $this->query;
    }

    public function fragment(): string
    {
        return $this->fragment;
    }

    public function authority(): Authority
    {
        return $this->authority;
    }

    public function isAbsolute(): bool
    {
        return '' !== (string) $this->scheme;
    }

    public function withHost(?string $host): self
    {
        $dsn = clone $this;
        $dsn->authority = $this->authority->withHost($host);

        return $dsn;
    }

    public function withoutHost(): self
    {
        return $this->withHost(null);
    }

    public function withScheme(?string $scheme): self
    {
        $dsn = clone $this;
        $dsn->scheme = new Scheme((string) $scheme);

        return $dsn;
    }

    public function withoutScheme(): self
    {
        return $this->withScheme(null);
    }

    public function withPort(?int $port): self
    {
        $dsn = clone $this;
        $dsn->authority = $this->authority->withPort($port);

        return $dsn;
    }

    public function withoutPort(): self
    {
        return $this->withPort(null);
    }

    public function withUser(?string $user): self
    {
        $dsn = clone $this;
        $dsn->authority = $this->authority->withUsername($user);

        return $dsn;
    }

    public function withoutUser(): self
    {
        return $this->withUser(null);
    }

    public function withPass(?string $pass): self
    {
        $dsn = clone $this;
        $dsn->authority = $this->authority->withPassword($pass);

        return $dsn;
    }

    public function withoutPass(): self
    {
        return $this->withPass(null);
    }

    public function withPath(?string $path): self
    {
        $dsn = clone $this;
        $dsn->path = new Path((string) $path);

        return $dsn;
    }

    public function withoutPath(): self
    {
        return $this->withPath(null);
    }

    public function withQuery(?array $query): self
    {
        $dsn = clone $this;
        $dsn->query = new Query($query ?? []);

        return $dsn;
    }

    /**
     * @param mixed $value
     */
    public function withQueryParam(string $param, $value): self
    {
        $dsn = clone $this;
        $dsn->query = $this->query->withQueryParam($param, $value);

        return $dsn;
    }

    public function withOnlyQueryParams(string ...$params): self
    {
        $dsn = clone $this;
        $dsn->query = $this->query->withOnlyQueryParams(...$params);

        return $dsn;
    }

    public function withoutQuery(): self
    {
        return $this->withQuery(null);
    }

    public function withoutQueryParams(string ...$params): self
    {
        $dsn = clone $this;
        $dsn->query = $this->query->withoutQueryParams(...$params);

        return $dsn;
    }

    public function withFragment(?string $fragment): self
    {
        $dsn = clone $this;
        $dsn->fragment = \ltrim($fragment, '#');

        return $dsn;
    }

    public function withoutFragment(): self
    {
        return $this->withFragment(null);
    }
}