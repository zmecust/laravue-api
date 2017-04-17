<?php

namespace App;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    public function menus()
    {
        return $this->belongsToMany(Menu::class)->withTimestamps();
    }
}