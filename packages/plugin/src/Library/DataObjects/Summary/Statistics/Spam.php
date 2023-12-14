<?php

namespace Solspace\Freeform\Library\DataObjects\Summary\Statistics;

class Spam
{
    public ?bool $spamProtectionBehavior = false;
    public ?bool $spamFolder = false;
    public ?bool $purgeSpam = false;
    public ?int $purgeInterval = 0;
    public ?bool $blockEmail = false;
    public ?bool $blockKeywords = false;
    public ?bool $blockIp = false;
    public ?bool $submissionThrottling = false;
    public ?bool $minSubmitTime = false;
    public ?bool $submitExpiration = false;
    public ?bool $captcha = false;
    public ?string $captchaType = '';
    public ?bool $bypassSpamCheckOnLoggedInUsers = false;
    public ?int $submissionThrottlingCount = 0;
}
