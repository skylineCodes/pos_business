<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenceCount extends Model
{
    /**
     * The attribute that aren't mass assignable
     *
     * @var array
     */
    protected $guarded = ['id'];
}
