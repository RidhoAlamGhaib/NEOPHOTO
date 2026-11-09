<?php

namespace App\Services;

use App\Models\customer;
use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * "My Journey" purchase streak.
 *
 * - Every paid purchase moves a member one step along the journey.
 * - The journey resets every 12 months from the earliest matching customer, or membership registration.
 * - Tier (by step reached): Silver 1-5, Gold 6-9, Diamond 10+.
 * - Rewards are unlocked at specific steps (see REWARDS).
 */
class MembershipJourney
{
    /** Number of steps drawn on the path (the last one is "11 and beyond"). */
    public const VISIBLE_STEPS = 11;

    /** Months after registration at which the journey restarts. */
    public const CYCLE_MONTHS = 12;

    /** Reward unlocked when a member reaches the given step. */
    public const REWARDS = [
        1 => 'Free extra print',
        3 => 'Free keychain',
        5 => 'Free 1× regular / visa photo',
        8 => 'Free high-angle photo & extra print',
        10 => 'Free doll merchandise',
    ];

    public const TIERS = [
        'silver' => [
            'label' => 'Silver',
            'from' => 1,
            'to' => 5,
            'discount' => 0,
            'perk' => 'Rewards at purchases 1, 3 & 5',
            'summary' => 'No extra benefits besides the journey rewards at purchases 1, 3 and 5.',
        ],
        'gold' => [
            'label' => 'Gold',
            'from' => 6,
            'to' => 9,
            'discount' => 20,
            'perk' => '20% off every transaction',
            'summary' => '20% off every transaction, plus the journey reward at purchase 8.',
        ],
        'diamond' => [
            'label' => 'Diamond',
            'from' => 10,
            'to' => null,
            'discount' => 40,
            'perk' => '40% off every transaction',
            'summary' => '40% off every transaction, plus the journey reward at purchase 10.',
        ],
    ];

    /**
     * Build a member's journey from paid orders linked to the member's customer.
     *
     * @return array<string, mixed>
     */
    public function forCustomerId(?int $customerId, ?CarbonInterface $registeredAt, ?Carbon $now = null): array
    {
        $now ??= Carbon::now();

        $registrationDate = $customerId === null
            ? $registeredAt
            : customer::query()->whereKey($customerId)->value('created_at') ?? $registeredAt;

        [$cycleStart, $cycleEnd] = $this->cycleFor($registrationDate, $now);

        $count = $customerId === null
            ? 0
            : Order::query()
                ->where('customer_id', $customerId)
                ->where('status', 'paid')
                ->where('created_at', '>=', $cycleStart)
                ->where('created_at', '<', $cycleEnd)
                ->count();

        return $this->describe($count, $cycleStart, $cycleEnd, $now);
    }

    /**
     * Current 12-month cycle [start, end) for a registration date.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function cycleFor(mixed $registeredAt, Carbon $now): array
    {
        $registered = $registeredAt ? Carbon::parse($registeredAt) : $now->copy();
        $cycle = 0;

        while ($registered->copy()->addMonthsNoOverflow(self::CYCLE_MONTHS * ($cycle + 1))->lte($now)) {
            $cycle++;
        }

        return [
            $registered->copy()->addMonthsNoOverflow(self::CYCLE_MONTHS * $cycle),
            $registered->copy()->addMonthsNoOverflow(self::CYCLE_MONTHS * ($cycle + 1)),
        ];
    }

    public function tierFor(int $step): string
    {
        return match (true) {
            $step >= 10 => 'diamond',
            $step >= 6 => 'gold',
            default => 'silver',
        };
    }

    /**
     * Everything the view needs, given the number of paid purchases in the cycle.
     *
     * @return array<string, mixed>
     */
    public function describe(int $count, Carbon $cycleStart, Carbon $cycleEnd, ?Carbon $now = null): array
    {
        $now ??= Carbon::now();
        $tierKey = $this->tierFor($count);
        $tier = self::TIERS[$tierKey];
        $currentStep = min($count, self::VISIBLE_STEPS);

        $steps = [];
        for ($n = 1; $n <= self::VISIBLE_STEPS; $n++) {
            $steps[] = [
                'number' => $n,
                'tier' => $this->tierFor($n),
                'status' => $n <= $count ? 'done' : ($n === $count + 1 ? 'next' : 'upcoming'),
                'current' => $count > 0 && $n === $currentStep,
                'reward' => self::REWARDS[$n] ?? null,
            ];
        }

        $rewards = [];
        $nextReward = null;
        foreach (self::REWARDS as $step => $label) {
            $reward = [
                'step' => $step,
                'label' => $label,
                'tier' => $this->tierFor($step),
                'unlocked' => $count >= $step,
                'remaining' => max(0, $step - $count),
            ];
            $rewards[] = $reward;

            if ($nextReward === null && ! $reward['unlocked']) {
                $nextReward = $reward;
            }
        }

        $nextTier = match ($tierKey) {
            'silver' => ['label' => 'Gold', 'step' => 6, 'remaining' => 6 - $count],
            'gold' => ['label' => 'Diamond', 'step' => 10, 'remaining' => 10 - $count],
            default => null,
        };

        $tiers = [];
        foreach (self::TIERS as $key => $definition) {
            $tiers[] = $definition + [
                'key' => $key,
                'current' => $key === $tierKey,
                'range' => $definition['to'] !== null
                    ? $definition['from'].'–'.$definition['to']
                    : $definition['from'].'+',
            ];
        }

        return [
            'count' => $count,
            'tier' => $tierKey,
            'tierLabel' => $tier['label'],
            'discount' => $tier['discount'],
            'perk' => $tier['perk'],
            'steps' => $steps,
            'rewards' => $rewards,
            'nextReward' => $nextReward,
            'nextTier' => $nextTier,
            'tiers' => $tiers,
            'cycleStart' => $cycleStart,
            'cycleEnd' => $cycleEnd,
            'daysLeft' => max(0, (int) $now->copy()->startOfDay()->diffInDays($cycleEnd->copy()->startOfDay(), false)),
        ];
    }
}
