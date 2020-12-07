<?php

namespace App\Observers;

use App\Actions\PrintResult;
use App\Model\Award;

class AwardObserver
{
     /**
     * Handle the User "updated" event.
     *
     * @param  Award  $candidate
     * @return void
     */
    public function updated(Award $award)
    {
        if ($award->wasChanged('number')) {
            (new PrintResult())->run();
        }
    }
}
