<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHierarchy extends Model
{
    use HasFactory;

    protected $table = 'role_hierarchy';

    protected $fillable = [
        'role_name',
        'level',
    ];

    // Level role
    const LEVEL_PENELITI = 1;
    const LEVEL_REVIEWER = 2;
    const LEVEL_SEKRETARIS = 3;
    const LEVEL_KETUA = 4;
    const LEVEL_ADMIN = 5;

    // ========== HELPER METHODS ==========
    
    // Mendapatkan level berdasarkan role name
    public static function getLevel($roleName)
    {
        $hierarchy = self::where('role_name', $roleName)->first();
        return $hierarchy ? $hierarchy->level : 0;
    }

    // Cek apakah role memiliki level lebih tinggi dari role lain
    public static function isHigher($roleName, $comparedRole)
    {
        return self::getLevel($roleName) > self::getLevel($comparedRole);
    }

    // Mendapatkan semua role yang levelnya lebih rendah
    public static function getLowerRoles($roleName)
    {
        $currentLevel = self::getLevel($roleName);
        return self::where('level', '<', $currentLevel)->pluck('role_name')->toArray();
    }

    // Mendapatkan semua role yang levelnya lebih tinggi
    public static function getHigherRoles($roleName)
    {
        $currentLevel = self::getLevel($roleName);
        return self::where('level', '>', $currentLevel)->pluck('role_name')->toArray();
    }
}