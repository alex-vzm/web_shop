<?php

namespace App\Http\Controllers;


use App\Http\Requests\ForgotPasswordFormRequest;
use App\Http\Requests\ResetPasswordFormRequest;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function index(): Factory|View|Application
    {
        return view('auth.index');
    }

    public function signUp(): Factory|View|Application
    {
        return view('auth.sign-up');
    }

    public function forgot(): Factory|View|Application
    {
        return view('auth.forgot-password');
    }


    public function signIn(SignInFormRequest $request): RedirectResponse
    {
        //  TODO 3rd lesson  Rate limite

        if (!auth()->attempt($request->validated())) {
            return back()->withErrors([
                'email' => 'Учетная запись с таким логином и паролем отсутствует. ',
            ])->onlyInput('email');
        }

        request()->session()->regenerate();

        return redirect()->intended(route('home'));
    }


    public function store(SignUpFormRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
        ]);

        event(new Registered($user));

        auth()->login($user);

        return redirect()
            ->intended(route('home'));
    }


    public function logOut(): RedirectResponse
    {
        Auth()->logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()
            ->route('home');
    }


    public function forgotPassword(ForgotPasswordFormRequest $request): RedirectResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // TODO  3rd lesson Flash

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['message' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }


    public function reset(string $token): Factory|View|Application
    {
        {
            return view('auth.reset-password', [
                'token' => $token
            ]);
        }
    }


    public function resetPassword(ResetPasswordFormRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(str()->random(60));

                $user->save();
                event(new  PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('message', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }


    public function github()
    {
        return Socialite::driver('github')
            ->redirect();
    }

    public function githubCallback()
    {
        $githubUser = Socialite::driver('github')->user();

        //  TODO  3rd lesson  move to custom table

        $user = User::query()->updateOrCreate([
            'github_id' => $githubUser->id,
        ], [
            'name' => $githubUser->name,
            'email' => $githubUser->email,
            'password' => bcrypt(str()->random(20))
        ]);

        auth()->login($user);

        return redirect()->intended(route('home'));
    }

}
