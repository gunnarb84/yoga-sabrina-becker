<?php

declare(strict_types=1);

namespace Yoga\Platform\NumberSequence\Persistence;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Query\Builder;
use Yoga\Platform\NumberSequence\Application\NextNumber;
use Yoga\Platform\NumberSequence\Application\NumberSequenceResolver;
use Yoga\Platform\NumberSequence\Domain\NumberSequenceDefinition;

final readonly class DatabaseNextNumber implements NextNumber
{
    public function __construct(
        private ConnectionResolverInterface $db,
        private NumberSequenceResolver $resolver,
    ) {
    }

    public function next(string $code): string
    {
        $definition = NumberSequenceDefinition::where('code', $code)->firstOrFail();
        $tableName = $this->resolver->resolve($code);

        $next = $this->transaction(function () use ($definition, $tableName, $code): int {
            $query = $this->table($tableName)
                ->lockForUpdate()
                ->where('jahr', $definition->currentYear())
                ->where('code', $code);

            $row = $query->first();

            if ($row === null) {
                $this->table($tableName)->insert([
                    'code' => $code,
                    'jahr' => $definition->currentYear(),
                    'letzte_nummer' => 1,
                    'angelegt_am' => now(),
                    'geaendert_am' => now(),
                ]);

                return 1;
            }

            if (! is_int($row->letzte_nummer)) {
                throw new \RuntimeException('Sequence counter must be an integer');
            }

            $next = $row->letzte_nummer + 1;

            $query->update([
                'letzte_nummer' => $next,
                'geaendert_am' => now(),
            ]);

            return $next;
        });

        return $definition->formatNumber($next);
    }

    public function advance(string $code, int $letzteNummer, ?int $jahr = null): void
    {
        $definition = NumberSequenceDefinition::where('code', $code)->firstOrFail();
        $tableName = $this->resolver->resolve($code);
        $jahr = $jahr ?? $definition->currentYear();

        $this->transaction(function () use ($tableName, $code, $letzteNummer, $jahr): void {
            $query = $this->table($tableName)
                ->lockForUpdate()
                ->where('jahr', $jahr)
                ->where('code', $code);

            $row = $query->first();

            if ($row === null) {
                $this->table($tableName)->insert([
                    'code' => $code,
                    'jahr' => $jahr,
                    'letzte_nummer' => $letzteNummer,
                    'angelegt_am' => now(),
                    'geaendert_am' => now(),
                ]);

                return;
            }

            $current = is_int($row->letzte_nummer) ? $row->letzte_nummer : 0;

            if ($letzteNummer <= $current) {
                return;
            }

            $query->update([
                'letzte_nummer' => $letzteNummer,
                'geaendert_am' => now(),
            ]);
        });
    }

    private function table(string $name): Builder
    {
        return $this->db->connection()->table($name);
    }

    /**
     * @template T
     * @param  \Closure(): T  $callback
     * @return T
     */
    private function transaction(\Closure $callback): mixed
    {
        return $this->db->connection()->transaction($callback);
    }
}
