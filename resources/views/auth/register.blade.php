<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="emp_id" :value="__('Employee ID')" />
            <x-text-input id="emp_id" class="block mt-1 w-full" type="text" name="emp_id" :value="old('emp_id')" required />
            <x-input-error :messages="$errors->get('emp_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="grade" :value="__('Grade')" />
            <x-text-input id="grade" class="block mt-1 w-full" type="text" name="grade" :value="old('grade')" required />
            <x-input-error :messages="$errors->get('grade')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="departement_id" :value="__('Departement ID')" />
            <x-text-input id="departement_id" class="block mt-1 w-full" type="text" name="departement_id" :value="old('departement_id')" required />
            <x-input-error :messages="$errors->get('departement_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="department" :value="__('Department')" />
            <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department')" required />
            <x-input-error :messages="$errors->get('department')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="section_id" :value="__('Section ID')" />
            <x-text-input id="section_id" class="block mt-1 w-full" type="text" name="section_id" :value="old('section_id')" required />
            <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="section" :value="__('Section')" />
            <x-text-input id="section" class="block mt-1 w-full" type="text" name="section" :value="old('section')" required />
            <x-input-error :messages="$errors->get('section')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
