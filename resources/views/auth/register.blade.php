{{--
|--------------------------------------------------------------------------
| resources/views/auth/register.blade.php
|--------------------------------------------------------------------------
| Esta es la página de "Crear gratis" (Registro).
| La hemos modificado para que:
| 1. Extienda tu layout público (con el header y footer correctos).
| 2. Use las clases de Tailwind de tu diseño.
|
--}}

@extends('layouts.public') @section('content')

<section class"pt-32 pb-20 px-4">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                Crea tu cuenta gratis
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                ¿Ya tienes una?
                <a href="{{ route('login') }}" class="font-medium text-purple-600 hover:text-purple-500">
                    Inicia sesión aquí
                </a>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-2xl sm:px-10">
                <form class"space-y-6" action="{{ route('register') }}" method="POST">
                    @csrf <div>
                        <label for="full_name" class="block text-sm font-medium leading-6 text-gray-900">Nombre completo</label>
                        <div class="mt-2">
                            <input id="full_name" name="full_name" type="text" autocomplete="name" required
                                   class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6 @error('full_name') ring-red-500 @enderror"
                                   value="{{ old('full_name') }}">
                        </div>
                        @error('full_name')
                            <span class="text-sm text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6 @error('email') ring-red-500 @enderror"
                                   value="{{ old('email') }}">
                        </div>
                        @error('email')
                            <span class="text-sm text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Contraseña</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="new-password" required
                                   class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6 @error('password') ring-red-500 @enderror">
                        </div>
                         @error('password')
                            <span class="text-sm text-red-600" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div>
                        <label for="password-confirm" class="block text-sm font-medium leading-6 text-gray-900">Confirmar contraseña</label>
                        <div class="mt-2">
                            <input id="password-confirm" name="password_confirmation" type="password" autocomplete="new-password" required
                                   class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-purple-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="flex w-full justify-center rounded-full gradient-primary px-6 py-3 text-base font-semibold text-white shadow-sm hover:shadow-lg transition transform hover:scale-105">
                            Crear mi cuenta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection