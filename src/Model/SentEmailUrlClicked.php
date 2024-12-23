<?php

namespace behzadsp\MailTracker\Model;

use Illuminate\Database\Eloquent\Model;
use behzadsp\MailTracker\Concerns\IsSentEmailUrlClickedModel;
use behzadsp\MailTracker\Contracts\SentEmailUrlClickedModel;


class SentEmailUrlClicked extends Model implements SentEmailUrlClickedModel
{
    use IsSentEmailUrlClickedModel;

    protected $table = 'sent_emails_url_clicked';

    protected $fillable = [
        'sent_email_id',
        'url',
        'hash',
        'clicks',
    ];
}
