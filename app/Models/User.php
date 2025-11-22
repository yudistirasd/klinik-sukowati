<?php

namespace App\Models;

use Avatar;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Str;


class User extends Authenticatable
{
    use HasUuids, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
        'nik',
        'ihs_id',
        'dokter_external',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['name_plain'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function getNamePlainAttribute(): string
    {
        return Str::of($this->name)
            ->replaceMatches('/^(drg?|prof|apt|ns|ir|hj|h|bpk|ibu)\.?(\s+)/i', '')
            ->replaceMatches('/,\s*(S\.?\w+|M\.?\w+|A\.?Md\.?\w*|Sp\.?\w*|Ph\.?D|Drs\.?|Dra\.?)(\.|\s|$)/i', '')
            ->replaceMatches('/\s{2,}/', ' ')
            ->trim(' ,');
    }

    public function generateAvatar()
    {
        if (!Storage::disk('public')->exists('avatars')) {
            Storage::disk('public')->makeDirectory('avatars');
        }

        Avatar::create($this->name)->save(storage_path('app/public/avatars/' . $this->id . '.png'), 100);
        $this->avatar = $this->id . '.png';
        $this->save();
    }

    public function hasRole(string|array $roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    public function scopeDokter($query, $external = 'N')
    {
        return $query->where('role', 'dokter')
            ->where('dokter_external', $external);
    }

    /**
     * Get all of the ruangan for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ruangan(): BelongsToMany
    {
        return $this->belongsToMany(Ruangan::class, 'user_has_ruangan', 'user_id', 'ruangan_id');
    }
}
