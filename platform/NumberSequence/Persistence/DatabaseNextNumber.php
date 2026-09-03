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

            $next = $row->letzte_nummer + 1;

            $query->update([
                'letzte_nummer' => $next,
                'geaendert_am' => now(),
            ]);

            return $next;
        });

        return $definition->formatNumber($next);
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
