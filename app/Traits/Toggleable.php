<?php

namespace App\Traits;

trait Toggleable
{
    public function toggleFeature()
    {
        $this->featured = !$this->featured;
        if (!$this->published) {
            return false; // Can't feature an unpublished item
        }

        $this->save();
        return $this->featured;
    }

    public function togglePublished()
    {
        $this->published = !$this->published;
        if (!$this->published) {
            $this->featured = false; // Unfeature when unpublishing
        }
        $this->save();
        return $this->published;
    }
}
