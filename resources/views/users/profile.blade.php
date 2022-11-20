<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">

            <!-- Display any messages -->
            <x-message-component />

            <!-- Edit User Details -->
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Profile Details
                </h2>
                <br>

                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @method('PUT')
                            @csrf

                            <!-- Update Form Inputs -->
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div class="grid grid-rows-2 gap-6">
                                    <div>
                                        <x-label for="name" :value="__('Name')" />
                                        <x-input id="name" class="block w-full mt-1" type="text" name="name" value="{{ auth()->user()->name }}" autofocus />
                                    </div>
                                    <div>
                                        <x-label for="email" :value="__('Email')" />
                                        <x-input id="email" class="block w-full mt-1" type="email" name="email" value="{{ auth()->user()->email }}" autofocus />
                                    </div>
                                </div>
                                <div class="grid grid-rows-2 gap-6">
                                    <div>
                                        <x-label for="new_password" :value="__('New password')" />
                                        <x-input id="new_password" class="block w-full mt-1"
                                                type="password"
                                                name="password"
                                                autocomplete="new-password" />
                                    </div>
                                    <div>
                                        <x-label for="confirm_password" :value="__('Confirm password')" />
                                        <x-input id="confirm_password" class="block w-full mt-1"
                                                type="password"
                                                name="password_confirmation"
                                                autocomplete="confirm-password" />
                                    </div>
                                </div>
                            </div>

                            <!-- Update Form Button -->
                            <div class="flex items-center justify-center mt-4 sm:justify-end">
                                <x-button class="ml-3">
                                    {{ __('Update') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete User Account -->
            <div class="mt-12">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Delete Your Account
                </h2>
                <br>

                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form method="POST" action="{{ route('profile.delete') }}">
                            @method('DELETE')
                            @csrf
                            <div class="flex flex-col items-center justify-between sm:flex-row">
                                <x-label for="account_delete" :value="__('This will permanently delete your account- are you sure?')" />
                                <x-button class="justify-end mt-5 bg-red-500 sm:mt-0" name="account_delete">
                                    {{ __('Delete') }}
                                </x-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
