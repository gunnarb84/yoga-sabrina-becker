<?php

declare(strict_types=1);

namespace Yoga\Platform\Shared\Application;

/**
 * @template T
 */
final readonly class Result
{
    /**
     * @param  T|null  $value
     * @param  array{code: string, errors: list<string>}|null  $error
     */
    private function __construct(
        private mixed $value,
        private ?array $error,
    ) {
    }

    /**
     * @template U
     * @param  U  $value
     * @return self<U>
     */
    public static function success(mixed $value = null): self
    {
        return new self($value, null);
    }

    /**
     * @return self<null>
     */
    public static function failure(string $code, array $errors = []): self
    {
        return new self(null, ['code' => $code, 'errors' => $errors]);
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function isFailure(): bool
    {
        return $this->error !== null;
    }

    /**
     * @return T|null
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * @return array{code: string, errors: list<string>}|null
     */
    public function error(): ?array
    {
        return $this->error;
    }
}
