<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'branch_id',
        'status_id',
        'department_id',
        'phone_no',
        'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getAvatarAttribute()
    {
        $words = explode(' ', trim($this->name));

        return collect($words)
            ->filter()
            ->map(fn ($word) => strtoupper(substr($word, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id')->withDefault();
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id')->withDefault();
    }

    public function branches(){
        return $this->belongsToMany(Branch::class,"user_branches");
    }

    public function categories(){
        return $this->belongsToMany(Category::class,"user_categories");
    }

    // Start Authentication & Authorization
    public function roles(){
        // return $this->belongsToMany(Role::class);
        return $this->belongsToMany(Role::class,"role_users");
    }

    public function permissions(){
        return $this->belongsToMany(Permission::class,"permission_roles");
    }

    // for single role from route
    // public function hasRole($rolename){
    //     return $this->roles()->where('name',$rolename)->exists();
    // }

    // for multi roles from route
    public function hasRoles($rolenames){
        return $this->roles()->whereIn('name',$rolenames)->exists();
    }

    public function hasPermission($permissionname){
        return $this->roles()->whereHas('permissions',function($query) use ($permissionname){
            $query->where('name',$permissionname);
        })->exists();
    }
    // End Authentication & Authorization

    public function isOwner($model){
        return $this->id === $model->user_id;
    }


}
