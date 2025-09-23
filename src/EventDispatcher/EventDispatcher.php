<?php

declare(strict_types=1);

namespace Stasis\EventDispatcher;

/**
 * @internal
 */
class EventDispatcher
{
    /** @var \SplObjectStorage<ListenerInterface, null> */
    private readonly \SplObjectStorage $listeners;

    /** @var array<class-string<EventInterface>, ListenerInterface[]> */
    private array $cache = [];

    public function __construct()
    {
        $this->listeners = new \SplObjectStorage();
    }

    public function add(ListenerInterface $listener): void
    {
        $this->listeners->attach($listener);
        $this->clearCache();
    }

    public function remove(ListenerInterface $listener): void
    {
        $this->listeners->detach($listener);
        $this->clearCache();
    }

    public function dispatch(EventInterface $event): void
    {
        $listeners = $this->getCached($event);
        if ($listeners !== null) {
            foreach ($listeners as $listener) {
                $event->accept($listener);
            }

            return;
        }

        $this->cache[$event::class] = [];
        foreach ($this->listeners as $listener) {
            $isAccepted = $event->accept($listener);

            if ($isAccepted) {
                $this->cache($event, $listener);
            }
        }
    }

    /** @return array<ListenerInterface>|null */
    private function getCached(EventInterface $event): ?array
    {
        return $this->cache[$event::class] ?? null;
    }

    private function cache(EventInterface $event, ListenerInterface $listener): void
    {
        $this->cache[$event::class][] = $listener;
    }

    private function clearCache(): void
    {
        $this->cache = [];
    }
}
