<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Tavo\ValidadorEc;

class RegisteredUserController extends Controller
{

    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        #VALIDAMOS LA CEDULA, SI ES CORRECTA CREA EL USUARIO, SI NO ES CORRECTA MUESTRA UN MENSAJE DE ERROR
        $validador = new ValidadorEc;
        if ($validador->validarCedula($request->cedula)) {
            
            $request->validate([
                'cedula' => ['required', 'string', 'max:13','unique:'.User::class],
                'name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
    
            $user = User::create([
                'cedula' => $request->cedula,
                'name' => $request->name,
                'last_name' => $request->last_name,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));
    
            Auth::login($user);

            return redirect(RouteServiceProvider::HOME);        
        }else{
            return redirect()->back()->withInput(array(
                'name' => $request->name, 
                'email' => $request->email, 
                'last_name' => $request->last_name, 
                'phone_number' => $request->phone_number))
            ->withErrors(['cedula' => 'Cedula Invalida']);
        }
    }
}
