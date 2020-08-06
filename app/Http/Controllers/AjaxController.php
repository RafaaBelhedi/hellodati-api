<?php


namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

use App\Http\Requests;
use App\Http\Utils;
use App\User;
use App\Http\Resources\UserTemplate;
use Mail;

/**
 * @group User
 *
 * APIs for managing users
 */
class AjaxController extends LoggedController
{
 
}