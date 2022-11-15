<?php

namespace behzadsp\MailTracker\Tests;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use behzadsp\MailTracker\Model\SentEmail;
use behzadsp\MailTracker\RecordBounceJob;
use behzadsp\MailTracker\RecordDeliveryJob;
use behzadsp\MailTracker\RecordTrackingJob;
use behzadsp\MailTracker\RecordComplaintJob;
use behzadsp\MailTracker\RecordLinkClickJob;
use behzadsp\MailTracker\Events\ViewEmailEvent;
use behzadsp\MailTracker\Events\LinkClickedEvent;

class RecordTrackingJobTest extends SetUpTest
{
    /**
     * @test
     */
    public function it_records_views()
    {
        Event::fake();
        $track = \behzadsp\MailTracker\Model\SentEmail::create([
                'hash' => Str::random(32),
            ]);
        $job = new RecordTrackingJob($track, '127.0.0.1');

        $job->handle();

        Event::assertDispatched(ViewEmailEvent::class, function ($e) use ($track) {
            return $track->id == $e->sent_email->id &&
                $e->ip_address == '127.0.0.1';
        });
        $this->assertDatabaseHas('sent_emails', [
                'id' => $track->id,
                'opens' => 1,
            ]);
    }
}
