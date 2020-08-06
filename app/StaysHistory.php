<?php

namespace App;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Model;

class StaysHistory extends Model
{
  protected $guarded = [];
  protected $table = 'stays_history';
}