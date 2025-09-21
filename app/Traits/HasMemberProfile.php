<?php

namespace App\Traits;

trait HasMemberProfile
{
    /**
     * Get the member's name
     */
    public function getNameAttribute()
    {
        return $this->member?->name;
    }

    /**
     * Get the member's email
     */
    public function getEmailAttribute()
    {
        return $this->member?->email;
    }

    /**
     * Get member's phone number without country code
     */
    public function getPhoneNumberAttribute()
    {
        return $this->member?->phone_number;
    }

    /**
     * Get member's country code
     */
    public function getCountryCodeAttribute()
    {
        return $this->member?->country_code;
    }

    /**
     * Get member's full phone number (with country code)
     */
    public function getFullPhoneAttribute()
    {
        return $this->member?->full_phone;
    }

    /**
     * Get member's preferred language
     */
    public function getPreferredLanguageAttribute()
    {
        return $this->member?->preferred_language;
    }

    /**
     * Get member's avatar
     */
    public function getAvatarAttribute()
    {
        return $this->member?->avatar;
    }

    /**
     * Get member's avatar path
     */
    public function getAvatarPathAttribute()
    {
        return $this->member?->avatar_path;
    }

    /**
     * Get member's active status
     */
    public function getIsActiveAttribute()
    {
        return $this->member?->active;
    }
}
