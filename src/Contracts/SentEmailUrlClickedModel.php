<?php

namespace behzadsp\MailTracker\Contracts;

interface SentEmailUrlClickedModel
{
    public function getConnectionName();
    public function email();
}
