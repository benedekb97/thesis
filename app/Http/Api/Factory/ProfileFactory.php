<?php

declare(strict_types=1);

namespace App\Http\Api\Factory;

use App\Http\Api\Entity\Profile;
use App\Http\Api\Entity\ProfileInterface;
use Illuminate\Support\Arr;

class ProfileFactory implements ProfileFactoryInterface
{
    public function createFromAuthSchResponse(array $response): ProfileInterface
    {
        $profile = new Profile();

        $profile->setInternalId($response['internal_id']);
        $profile->setDisplayName($response['displayName']);
        $profile->setSurname($response['sn']);
        $profile->setGivenNames($response['givenName']);
        $profile->setEmailAddress($response['mail']);

        $profile->setEmbroideryGroupStatus(
            $this->getEmbroideryGroupStatus($response['eduPersonEntitlement'])
        );

        return $profile;
    }

    private function getEmbroideryGroupStatus(array $eduPersonEntitlement): ?string
    {
        $groups = array_filter(
            $eduPersonEntitlement,
            static function ($group) {
                return $group['id'] === self::AUTH_SCH_EMBROIDERY_GROUP_ID;
            }
        );

        if (empty($groups)) {
            return null;
        }

        $embroideryGroup = Arr::first($groups);

        return in_array(
            $status = (string)$embroideryGroup['status'],
            ProfileInterface::GROUP_STATUSES,
            true
        )
            ? $status
            : null;
    }
}
