<?php

namespace behzadsp\MailTracker\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Scenario;

/**
 * @property string $hash
 * @property string $headers
 * @property string $sender
 * @property string $recipient
 * @property string $subject
 * @property string $content
 * @property int $opens
 * @property int $clicks
 * @property int|null $message_id
 * @property string $meta
 */
class SentEmail extends Model
{
    protected $fillable = [
        'hash',
        'sender_id',
        'template_id',
        'scenario_id',
        'emailable_type',
        'emailable_id',
        'headers',
        'sender_name',
        'sender_email',
        'recipient_name',
        'recipient_email',
        'subject',
        'content',
        'opens',
        'clicks',
        'message_id',
        'meta',
        'opened_at',
        'clicked_at',
    ];

    protected $casts = [
        'meta' => 'collection',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];

    public function getConnectionName()
    {
        $connName = config('mail-tracker.connection');
        return $connName ?: config('database.default');
    }

    /**
     * Returns a bootstrap class about the success/failure of the message
     * @return [type] [description]
     */
    public function getReportClassAttribute()
    {
        if (!empty($this->meta) && $this->meta->has('success')) {
            if ($this->meta->get('success')) {
                return 'success';
            } else {
                return 'danger';
            }
        } else {
            return '';
        }
    }

    public function getSenderAttribute()
    {
        return $this->sender_name.' <'.$this->sender_email.'>';
    }

    public function getRecipientAttribute()
    {
        return $this->recipient_name.' <'.$this->recipient_email.'>';
    }

    /**
     * Returns the smtp detail for this message ()
     * @return [type] [description]
     */
    public function getSmtpInfoAttribute()
    {
        if (empty($this->meta)) {
            return '';
        }
        $meta = $this->meta;
        $responses = [];
        if ($meta->has('smtpResponse')) {
            $response = $meta->get('smtpResponse');
            $delivered_at = $meta->get('delivered_at');
            $responses[] = $response.' - Delivered '.$delivered_at;
        }
        if ($meta->has('failures')) {
            foreach ($meta->get('failures') as $failure) {
                if (!empty($failure['status'])) {
                    $responses[] = $failure['status'].' ('.$failure['action'].'): '.$failure['diagnosticCode'].' ('.$failure['emailAddress'].')';
                } else {
                    $responses[] = 'Generic Failure ('.$failure['emailAddress'].')';
                }
            }
        } elseif ($meta->has('complaint')) {
            $complaint_time = $meta->get('complaint_time');
            if ($meta->get('complaint_type')) {
                $responses[] = 'Complaint: '.$meta->get('complaint_type').' at '.$complaint_time;
            } else {
                $responses[] = 'Complaint at '.$complaint_time->format("n/d/y g:i a");
            }
        }
        return implode(" | ", $responses);
    }

    /**
     * Returns a collection of all headers requested from our stored header info
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllHeaders()
    {
        return collect(preg_split("/(\r\n)(?!\s)/", $this->headers))
            ->filter(function ($header) {
                return preg_match("/:/", $header);
            })
            ->transform(function ($header) {
                $header = Str::replace("\r\n", '', $header);
                list($key, $value) = explode(":", $header, 2);
                return collect([
                    'key' => trim($key),
                    'value' => trim($value)
                ]);
            })->filter(function ($header) {
                return $header->get('key');
            })->keyBy('key')
            ->transform(function ($header) {
                return $header->get('value');
            });
    }

    /**
     * Returns the header requested from our stored header info
     */
    public function getHeader($key)
    {
        return $this->getAllHeaders()->get($key);
    }

    public function urlClicks()
    {
        return $this->hasMany(SentEmailUrlClicked::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function template()
    {
        return $this->belongsTo(EmailTemplate::class, 'template_id', 'id');
    }

    public function scenario()
    {
        return $this->belongsTo(Scenario::class, 'scenario_id', 'id');
    }

    public function emailable()
    {
        return $this->morphTo();
    }
}
