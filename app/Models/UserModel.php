<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
	use Authenticatable, Authorizable;
	use SoftDeletes;
	protected $table   = 'users';
	public $primarykey = 'id';
	public $timestamps = true;

	protected $fillable = [
		'fullname', 'email','password','gender','status','photo','token'
	];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password','deleted_at','updated_at'
	];

	public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
	public function getAuthIdentifierName(){

	}
	public function getAuthIdentifier(){

	}
	public function getAuthPassword(){}
	public function getRememberToken(){}
	public function setRememberToken($value){}
	public function getRememberTokenName(){}
	}
