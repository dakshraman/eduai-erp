<?php

namespace Modules\MenuManage\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultMenu extends Model
{
    use HasFactory;

    protected $table = 'default_menus';

    protected $guarded = [];
}
