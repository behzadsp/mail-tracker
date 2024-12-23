<?php

namespace behzadsp\MailTracker\Events;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use behzadsp\MailTracker\Contracts\SentEmailModel;

class ValidActionEvent
{
    use Dispatchable;

    public $skip = false;

    public function __construct(Model|SentEmailModel $sent_email)
    {
        $this->sent_email = $sent_email;
    }
}
