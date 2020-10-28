<?php

namespace App;

use DB;
use App\System;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
// Social login
// use Schedula\Laravel\PassportSocialite\User\UserSocialAccount;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Attributes
     */
    protected $table = 'users';

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'lastname', 'email', 'phone', 'dni', 'password', 'image',
    ];

    public $timestamps = true;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function getByEmail($email)
    {
        $user = User::where('email', '=', $email)->get();
        
        if (count($user) == 0) {
            return false;
        } else {
            return $user->first();
        }
    }

    /**
     * Retorna el usuario correspondiente al médico
     */
    public function medic()
    {
        return $this->belongsTo('App\Medic', 'user_id', 'user_id')->get();
    }
    
    /**
     * Retorna los roles del usuario
     */
    public function roles()
    {
        return $this->belongsToMany('App\Role', 'role_user', 'user_id', 'role_id')->get();
    }


    /**
     * Retorna los permisos del usuario
     */
    public function permissions()
    {
        return Permission::where('role_user.user_id', '=', $this->user_id)
            ->join('permission_role', 'permission_role.permission_id', '=', 'permissions.permission_id')
            ->join('roles', 'roles.role_id', '=', 'permission_role.role_id')
            ->join('role_user', 'role_user.role_id', '=', 'roles.role_id')
            ->select('permissions.*')
            ->get();
    }

    /**
     * Retorna los sistemas en los que se encuentra el usuario
     */
    public function systems()
    {
        return $this->belongsToMany('App\System', 'system_user', 'user_id', 'system_id')->get();
    } 


    /**
     * Obtener los cambios de sistema que generó el usuario.
     * 
     */
    public function systemChanges()
    {
        return $this->hasMany('App\SystemChange');
    }


    /**
     * Agregar un nuevo rol a un usuario.
     * 
     */
    public function setRole($role)
    {
        // Get role_id
        $role_id = Role::where('role', $role)->get()->first()->role_id;
        // Saves the role
        DB::table('role_user')->insert([
            'user_id' => $this->user_id,
            'role_id' => $role_id,
        ]);
    }

    /**
     * Chequear si el usuario tiene rol.
     * 
     */
    public function hasRole($role)
    {
        $result = Role::where([['roles.role', '=', $role],['role_user.user_id', '=', $this->id]])
            ->join('role_user', 'role_user.role_id', '=', 'roles.role_id')
            ->count();
        
        return ($result > 0);
    }


    /**
     * Chequear si el usuario tien permiso.
     * 
     */
    public function hasPermission($permission)
    {
        $result = Permission::where([
            ['permissions.permission', '=', $permission],
            ['role_user.user_id', '=', $this->user_id]
            ])
            ->join('permission_role', 'permission_role.permission_id', '=', 'permissions.permission_id')
            ->join('roles', 'roles.role_id', '=', 'permission_role.role_id')
            ->join('role_user', 'role_user.role_id', '=', 'roles.role_id')
            ->count();
        
        return ($result > 0);
    }


    /**
     * Chequea si el usuario esta asginado a un sistema:
     * 
     * La funcion se usa porque los médicos y jefes de sistema tienen asignado
     * Los administradores y administradores de reglas no lo necesitan, ya que administran TODOS los sistemas
     * 
     */
    public function hasSystem()
    {
        $result = System::where('system_user.user_id', '=', $this->user_id)
            ->join('system_user', 'system_user.system_id', '=', 'systems.system_id')->count();
        return ($result > 0);
    }


    /**
     * Agregar un usuario a un sistema
     * 
     */
    public function setSystem($system)
    {
        // Get system_id
        $system_id = System::where('system', $system)->get()->first()->system_id;
        // Saves the system
        DB::table('system_user')->insert([
            'user_id' => $this->user_id,
            'system_id' => $system_id,
        ]);
    }


    /**
     * Obtiene todos los médicos.
     * 
     */
    public static function medics()
    {
        return User::where('roles.role', '=', Role::ROLE_MEDIC)
            ->join('role_user', 'role_user.user_id', '=', 'users.user_id')
            ->join('roles', 'roles.role_id', '=', 'role_user.role_id')
            ->leftJoin('system_user', 'system_user.user_id', '=', 'users.user_id')
            ->leftJoin('systems', 'systems.system_id', '=', 'system_user.system_id')
            ->get();
    }

    /**
     * Obtiene todos los médicos de un sistema
     */
    
    public static function medicsBySystem($system_id)
    {
        return User::where([
            ['roles.role', '=', Role::ROLE_MEDIC],
            ['systems.system_id', '=', $system_id]
            ])
            ->join('role_user', 'role_user.user_id', '=', 'users.user_id')
            ->join('roles', 'roles.role_id', '=', 'role_user.role_id')
            ->leftJoin('system_user', 'system_user.user_id', '=', 'users.user_id')
            ->leftJoin('systems', 'systems.system_id', '=', 'system_user.system_id')
            ->get();
    }

}
