<?php

namespace Zenstruck\Utilities\Url;

use function Zenstruck\Utilities\Functions\value;

/**
 * @experimental
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Query implements \Stringable
{
    /** @var array|string */
    private $value;

    /**
     * @param array|string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    public function __toString(): string
    {
        return \http_build_query($this->all(), null, '&', PHP_QUERY_RFC3986);
    }

    public function all(): array
    {
        if (\is_array($this->value)) {
            return $this->value;
        }

        // convert string to array
        \parse_str($this->value, $array);

        return $this->value = $array;
    }

    public function has(string $param): bool
    {
        // todo dot notation
        return \array_key_exists($param, $this->all());
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $param, $default = null)
    {
        // todo dot notation
        return $this->all()[$param] ?? value($default);
    }

    public function withoutQueryParams(string ...$params): self
    {
        // todo dot notation?
        $array = $this->all();

        foreach ($params as $key) {
            unset($array[$key]);
        }

        return new self($array);
    }

    public function withOnlyQueryParams(string ...$params): self
    {
        // todo dot notation?
        $array = $this->all();

        foreach (\array_keys($array) as $param) {
            if (!\in_array($param, $params, true)) {
                unset($array[$param]);
            }
        }

        return new self($array);
    }

    /**
     * @param mixed $value
     */
    public function withQueryParam(string $param, $value): self
    {
        $array = $this->all();
        $array[$param] = $value;

        return new self($array);
    }
}