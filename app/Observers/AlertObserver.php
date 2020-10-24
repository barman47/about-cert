<?php

namespace App\Observers;

use App\Alert;

use App\Jobs\AlertCreatedJob;

class AlertObserver
{
    /**
     * Handle the alert "created" event.
     *
     * @param  \App\Alert  $alert
     * @return void
     */
    public function created(Alert $alert)
    {
        AlertCreatedJob::dispatch($alert);
    }

    /**
     * Handle the alert "updated" event.
     *
     * @param  \App\Alert  $alert
     * @return void
     */
    public function updated(Alert $alert)
    {
        //
    }

    /**
     * Handle the alert "deleted" event.
     *
     * @param  \App\Alert  $alert
     * @return void
     */
    public function deleted(Alert $alert)
    {
        //
    }

    /**
     * Handle the alert "restored" event.
     *
     * @param  \App\Alert  $alert
     * @return void
     */
    public function restored(Alert $alert)
    {
        //
    }

    /**
     * Handle the alert "force deleted" event.
     *
     * @param  \App\Alert  $alert
     * @return void
     */
    public function forceDeleted(Alert $alert)
    {
        //
    }
}
