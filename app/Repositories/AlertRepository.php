<?php
namespace App\Repositories;

use App\Alert;
use App\Events\AlertEvent;
use App\Exceptions\AlertRepositoryException;
use App\Handlers\Alerts\AlertHandlerAbstractClass;
use App\Interfaces\CanReceiverAlert;
use App\Interfaces\CanSendAlert;
use App\User;

class AlertRepository
{
    private $alert;
    private $user;

    public function __construct()
    {
        $this->alert = new Alert();
    } //end constructor

    public function handler(AlertHandlerAbstractClass $handler): AlertRepository
    {
        $this->alert->handler = get_class($handler);

        return $this;
    } //end method handler

    public function data($data): AlertRepository
    {
        $this->alert->data = \serialize($data);
        return $this;
    } //  end method date

    public function receiver(CanReceiverAlert $receiver): AlertRepository
    {
        $this->alert->receiver_id = $receiver->id;
        $this->alert->receiver_type = get_class($receiver);

        return $this;
    } //end method receiver

    public function sender(CanSendAlert $sender): AlertRepository
    {
        $this->alert->sender_id = $sender->id;
        $this->alert->sender_type = get_class($sender);

        return $this;
    } //end method sender

    public function forAdmin(): AlertRepository
    {
        $this->alert->for_admin = 1;
        return $this;
    } //end method forAdmin

    public function create()
    {
        $this->alert->save();
        broadcast(new AlertEvent($this->alert));
    } //end method create

    public function user(User $user): AlertRepository
    {
        $this->user = $user;
        return $this;
    } //end method user

    public function markAlertAsViewed(Alert $alert): bool
    {
        if (!$this->user) {
            throw new AlertRepositoryException("The user must be specified");
        }

        $user = $this->user;

        if (($alert->receiver_id == $user->id) && ($alert->receiver_type == User::class)) {
            if ($alert->viewed != 1) {
                $alert->viewed = 1;
                $alert->save();
            }

            return true;
        }

        return false;
    } //end method markAlertAsViewed

    public function getAlerts()
    {
        if (!$this->user) {
            throw new AlertRepositoryException("The user must be specified");
        }

        $user = $this->user;

        $returnData = [];

        $alerts = Alert::where([
            ["receiver_type", User::class],
            ["receiver_id", $user->id],
        ])->latest()->paginate();

        $alerts->each(function ($alert) use (&$returnData) {
            try {
                $returnData[] = $this->getHandledObjectData($alert);
            } catch (\Exception $e) {}
        });

        // return "This is the data";
        $alerts = json_decode($alerts->toJson());

        $alerts->data = $returnData;

        return $alerts;
    } //end method getAlerts

    // This method cannot be exposed to the outside API
    public function getHandledObjectData($alert)
    {
        return AlertHandlerAbstractClass::getHandledObjectData($alert);
    } //end method getHandledObjectData
} //end class AlertRepository
