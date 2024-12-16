<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): \Illuminate\Http\RedirectResponse
    {
        // Валидация данных
        $validator = Validator::make($input, [
            'name' => ['required', 'string', 'max:255', 'min:4'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        // Если валидация не прошла, возвращаем ошибки
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Проверяем изменения в полях
        $changes = [];

        // Проверяем, если имя изменилось
        if ($user->name !== $input['name']) {
            $changes['name'] = $input['name'];
        }

        // Проверяем, если email изменился
        if ($user->email !== $input['email']) {
            $changes['email'] = $input['email'];
        }

        // Если изменения есть, сохраняем
        if (!empty($changes)) {
            $user->forceFill($changes)->save();

            // Редирект с сообщением об успешном обновлении
            return redirect()->back()
                ->with('status_update', 'Профиль успешно обновлён')
                ->with('status_code', 'success');
        }

        // Если изменений нет, показываем сообщение
        return redirect()->back()
            ->with('status_update', 'Нет изменений для обновления')
            ->with('status_code', 'warning');
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
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
