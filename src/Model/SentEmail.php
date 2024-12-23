<?php

namespace behzadsp\MailTracker\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\Scenario;
use behzadsp\MailTracker\Concerns\IsSentEmailModel;
use behzadsp\MailTracker\Contracts\SentEmailModel;

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
 * @property Collection $meta
 */
class SentEmail extends Model implements SentEmailModel
{
    use IsSentEmailModel;

    protected $fillable = [
        'hash',
        'sender_id',
        'template_id',
        'emailable_type',
        'emailable_id',
        'scenario_id',
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

    protected $appends = [
        'domains_in_context'
    ];

    public function getDomainsInContextAttribute(){
        preg_match_all("/(<a[^>]*href=[\"])([^\"]*)/", $this->content, $matches);
        if ( ! isset($matches[2]) ) return [];
        $domains = [];
        foreach($matches[2] as $url){
            $domain = parse_url($url, PHP_URL_HOST);
            if ( ! in_array($domain, $domains) ){
                $domains[] = $domain;
            }
        }

        return $domains;
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
