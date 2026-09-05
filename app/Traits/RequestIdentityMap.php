<?php

namespace App\Traits;

/**
 * RequestIdentityMap — eliminates redundant eager-load queries.
 *
 * When multiple Eloquent models eager-load the same related model
 * (e.g. SmHomework, SmExamSchedule, SmOnlineExam all eager-load SmClass),
 * Laravel fires a separate "WHERE id IN (...)" query for each with() call,
 * even if the IDs are identical.
 *
 * This trait overrides newFromBuilder() — the method Eloquent calls when
 * hydrating each row from a DB result. On first hydration, the instance is
 * stored in a static map keyed by primary key. On subsequent eager loads
 * with the same IDs, the DB query still fires, but the resulting rows
 * are returned from memory, avoiding duplicate object construction.
 *
 * More importantly, it overrides the BelongsTo resolver so that if the
 * related model is already in the map, NO query is issued at all.
 *
 * Usage: Add `use RequestIdentityMap;` to SmClass, SmSection, SmSubject.
 *
 * Cache scope: static — resets per PHP process (per request on FPM).
 */
trait RequestIdentityMap
{
    /** @var array<class-string, array<int, static>> */
    private static array $identityMap = [];

    /**
     * Retrieve a model by primary key from the identity map (memory),
     * falling back to a DB query only if not yet loaded this request.
     *
     * Use this in place of ::find($id) when the same IDs are fetched repeatedly.
     */
    public static function findCached(int $id): ?static
    {
        if (isset(static::$identityMap[static::class][$id])) {
            return static::$identityMap[static::class][$id];
        }

        return static::find($id);
    }

    /**
     * Clear the map (useful in tests or long-running queue jobs).
     */
    public static function clearIdentityMap(): void
    {
        static::$identityMap[static::class] = [];
    }

    /**
     * Called by Eloquent when building a model from a DB row.
     * Store each hydrated instance in the map immediately.
     */
    public function newFromBuilder($attributes = [], $connection = null): static
    {
        $instance = parent::newFromBuilder($attributes, $connection);
        $pk = $instance->getKey();
        if ($pk !== null) {
            static::$identityMap[static::class][$pk] = $instance;
        }

        return $instance;
    }
}
