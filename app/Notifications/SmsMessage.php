<?php

declare(strict_types=1);

namespace App\Notifications;

class SmsMessage
{
    public string $content = '';

    public function content(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
