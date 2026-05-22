<?php

namespace App\Services;

use App\Models\AdCampaign;

class AdServingService
{
    public function pickForInterstitial(): array
    {
        return [
            'top' => $this->pickOne('top'),
            'side' => $this->pickOne('side'),
            'bottom' => $this->pickOne('bottom'),
        ];
    }

    private function pickOne(string $placement): ?AdCampaign
    {
        $candidates = AdCampaign::active()->where('placement', $placement)->get();
        if ($candidates->isEmpty()) {
            return null;
        }

        $totalWeight = $candidates->sum(fn ($c) => max(1, (int) $c->weight));
        $pick = random_int(1, $totalWeight);
        $cum = 0;
        foreach ($candidates as $c) {
            $cum += max(1, (int) $c->weight);
            if ($pick <= $cum) {
                return $c;
            }
        }

        return $candidates->last();
    }
}
