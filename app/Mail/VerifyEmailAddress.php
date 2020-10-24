<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\User;
use URL;

class VerifyEmailAddress extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }//end constructor

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = URL::temporarySignedRoute(
            'verifyEmail', now()->addDays(2), ['user' => $this->user->id]
        );

        return $this->view('mail.users.VerifyEmailAddress')
                    ->with(["user" => $this->user, "url" => $url]);
    }//end method build
}
