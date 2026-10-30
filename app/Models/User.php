<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Services\RolePermissionService;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATION ENTREPRISE
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /*
    |--------------------------------------------------------------------------
    | RÔLES
    |--------------------------------------------------------------------------
    */

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isCompanyAdmin(): bool
    {
        return $this->role === 'companyadmin';
    }

    public function isDirector(): bool
    {
        return $this->role === 'director';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isStockManager(): bool
    {
        return $this->role === 'stock_manager';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /*
    |--------------------------------------------------------------------------
    | NOM DU RÔLE
    |--------------------------------------------------------------------------
    */

    public function roleLabel(): string
    {
        return match ($this->role) {

            'super_admin' =>
                'Super Administrateur',

            'companyadmin' =>
                'Administrateur entreprise',

            'director' =>
                'Directeur',

            'manager' =>
                'Manager',

            'seller' =>
                'Vendeur',

            'cashier' =>
                'Caissier',

            'stock_manager' =>
                'Gestionnaire de stock',

            'accountant' =>
                'Comptable',

            'employee' =>
                'Employé',

            default =>
                'Utilisateur',

        'school_teacher' => 
                'Enseignant',
        };

        
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSIONS
    |--------------------------------------------------------------------------
    */

    public function hasPermission(string $permission): bool
    {
        return app(RolePermissionService::class)
            ->can($this, $permission);
    }

    /*
    |--------------------------------------------------------------------------
    | PERMISSIONS PERSONNALISÉES
    |--------------------------------------------------------------------------
    */

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }
}