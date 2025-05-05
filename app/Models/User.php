<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;
use Storage;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    use LogsActivity;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'identification_card',
        'exequatur',
        'specialty',
        'area',
        'phone',
        'address',
        'birthdate',
        'position',
        'comment',
        'active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Override the default profile photo URL method.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string|null {
            return $this->profile_photo_path
                ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
                : null; // No más UI Avatars
        });
    }


    // Método para validar si se puede cambiar el rol
    public function canChangeRole()
    {
        // Lista de IDs de usuarios protegidos (administradores principales)
        $protectedUserIds = [1, 2];

        return !in_array($this->id, $protectedUserIds);
    }

    // Método para actualizar rol con validación
    public function updateRole($newRole)
    {
        if (!$this->canChangeRole()) {
            throw new \Exception('No puedes modificar el rol de este usuario administrador');
        }

        $this->syncRoles([$newRole]);
    }

    // LOGS
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('nurseRecords.show, ' . $this->id)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    // Modificar la descripción del evento si es 'updated' y 'active' cambió
    public function tapActivity(Activity $activity, string $eventName)
    {
        $properties = $activity->properties->toArray();
        if ($eventName === 'updated' && $this->isDirty('active')) {
            $activity->description = $this->active ? 'activated' : 'deactivated';
        } else {
            $activity->description = $eventName;
        }
        $activity->properties = collect($properties);
    }

    // RELATIONS

    public function medicalOrder(): HasMany
    {
        return $this->hasMany(MedicalOrder::class);
    }

    public function admission(): HasMany
    {
        return $this->hasMany(Admission::class);
    }

    public function nurseRecord(): HasMany
    {
        return $this->hasMany(NurseRecord::class);
    }

    public function temperatureDetail(): HasMany
    {
        return $this->hasMany(TemperatureDetail::class);
    }

    public function medicationNotification(): HasMany
    {
        return $this->hasMany(MedicationNotification::class);
    }

    public function medicationRecord(): HasMany
    {
        return $this->hasMany(MedicationRecord::class);
    }

    public function temperatureRecord(): HasMany
    {
        return $this->hasMany(TemperatureRecord::class);
    }
}
