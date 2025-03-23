<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, mixed>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'identification_card' => ['required', 'max:12', 'min:12', Rule::unique('users')->ignore($user->id)],
            'exequatur' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'specialty' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'max:14', 'min:14'],
            'address' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date', 'before:' . Carbon::now()->subYears(18)->format('Y-m-d')],
            'position' => ['required', 'string', 'max:255'],
            'comment' => ['string'],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if (
            $input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail
        ) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'last_name' => $input['last_name'],
                'email' => $input['email'],
                'identification_card' => $input['identification_card'],
                'exequatur' => $input['exequatur'],
                'specialty' => $input['specialty'],
                'area' => $input['area'],
                'phone' => $input['phone'],
                'address' => $input['address'],
                'birthdate' => $input['birthdate'],
                'position' => $input['position'],
                'comment' => $input['comment'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
