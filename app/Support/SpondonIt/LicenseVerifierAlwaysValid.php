<?php

namespace App\Support\SpondonIt;

class LicenseVerifierAlwaysValid
{
    public function validateLicense(array $params): array
    {
        return ['status' => true, 'message' => 'License verified'];
    }

    public function checkLicense(): bool
    {
        return true;
    }
}
