<?php

declare(strict_types=1);

namespace Yoga\Platform\Shared\Application;

/**
 * @template-covariant T
 */
abstract readonly class Result
{
    /**
     * @param  T|null  $value
     * @param  array{code: string, errors: list<string>}|null  $error
     */
    protected function __construct(
        protected mixed $value,
        protected ?array $error,
    ) {
    }

    /**
     * @template U
     * @param  U  $value
     * @return self<U>
     */
    public static function success(mixed $value = null): self
    {
        return new Success($value, null);
    }

    /**
     * @param  list<string>  $errors
     * @return self<never>
     */
    public static function failure(string $code, array $errors = []): self
    {
        return new Failure(['code' => $code, 'errors' => $errors]);
    }

    abstract public function isSuccess(): bool;

    abstract public function isFailure(): bool;

    /**
     * @return T|null
     */
    abstract public function value(): mixed;

    /**
     * @return T
     */
    abstract public function unwrap(): mixed;

    /**
     * @return array{code: string, errors: list<string>}|null
     */
    abstract public function error(): ?array;
}

/**
 * @template U
 * @extends Result<U>
 */
final readonly class Success extends Result
{
    /**
     * @param  U  $value
     * @param  array{code: string, errors: list<string>}|null  $error
     */
    public function __construct(
        mixed $value,
        ?array $error,
    ) {
        parent::__construct($value, $error);
    }

    public function isSuccess(): bool
    {
        return true;
    }

    public function isFailure(): bool
    {
        return false;
    }

    /**
     * @return U|null
     */
    public function value(): mixed
    {
        return $this->value;
    }

    /**
     * @return U
     */
    public function unwrap(): mixed
    {
        assert($this->value !== null);

        return $this->value;
    }

    /**
     * @return array{code: string, errors: list<string>}|null
     */
    public function error(): ?array
    {
        return null;
    }
}

/**
 * @extends Result<never>
 */
final readonly class Failure extends Result
{
    /**
     * @param  array{code: string, errors: list<string>}  $error
     */
    public function __construct(array $error)
    {
        parent::__construct(null, $error);
    }

    public function isSuccess(): bool
    {
        return false;
    }

    public function isFailure(): bool
    {
        return true;
    }

    public function value(): mixed
    {
        return null;
    }

    public function unwrap(): mixed
    {
        $error = $this->error;
        if ($error === null) {
            throw new \RuntimeException('Cannot unwrap failed result');
        }

        throw new \RuntimeException('Cannot unwrap failed result: '.$error['code']);
    }

    /**
     * @return array{code: string, errors: list<string>}|null
     */
    public function error(): ?array
    {
        return $this->error;
    }
}
