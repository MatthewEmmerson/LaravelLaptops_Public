<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
             {{ __("Admin") }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">

            <!-- Display any messages -->
            <x-message-component />

            <!-- View all User Favorites -->
            <div>
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        View all User Favorites
                    </h2>
                    <br>
                    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <!-- Filter -->
                            <div class="grid items-center gap-4 grid-row-3 sm:grid-cols-10 justify-evenly sm:flex-row">
                                <!-- Search Users -->
                                <div class="flex flex-col justify-center col-span-4">
                                    <x-label for="manufacturer_id" :value="__('User')" />
                                    <form class="justify-center" action="{{ route('adminfavoritesuser') }}">
                                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="user_id">
                                            @foreach ($usersSelect as $user)
                                                <option value="{{ $user->id }}">{{ $user->email }} </option>
                                            @endforeach
                                        </select>
                                        <x-button
                                            class="justify-center w-full mt-5 bg-green-500"
                                        >
                                            {{ __("Filter User") }}
                                        </x-button>
                                    </form>
                                </div>
                                <!-- Search Laptops -->
                                <div class="flex flex-col justify-center col-span-4 mt-4 sm:mt-0">
                                    <x-label for="manufacturer_id" :value="__('Laptop')" />
                                    <form class="justify-center" action="{{ route('adminfavoriteslaptop') }}">
                                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="laptop_id">
                                            @foreach ($laptopsSelect as $laptop)
                                                <option value="{{ $laptop->id }}">{{ $laptop->manufacturer->name }} {{ $laptop->make->name }} {{ $laptop->model }}</option>
                                            @endforeach
                                        </select>
                                        <x-button
                                            class="justify-center w-full mt-5 bg-green-500"
                                        >
                                            {{ __("Filter Laptop") }}
                                        </x-button>
                                    </form>
                                </div>
                                <!-- Show all and disclaimer -->
                                <div class="flex flex-col justify-center col-span-2 mt-4 sm:mt-0">
                                    <form class="justify-center" action="{{ route('adminfavorites') }}">
                                        <x-button
                                            class="w-full bg-gray-500"
                                        >
                                            {{ __("Show All") }}
                                        </x-button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Main List of User Favorites -->
                <div class="grid grid-cols-1 mt-4 overflow-y-auto lg:grid-cols-2 max-h-64">
                    @if ($previousPage == 'laptop')
                        <!-- Display individual laptop's details -->
                        <x-admin-user-favorite-card :userFavorite="$userFavorites"/>
                    @else
                        <!-- Display all favorites for this user -->
                        @if ($userFavorites->isEmpty())
                            <div class="col-span-1 sm:col-span-2 text-center">
                                <p>This user has no favorites</p>
                            </div>
                        @else
                            @foreach ($userFavorites as $userFavorite)
                                <x-admin-user-favorite-card :userFavorite="$userFavorite"/>
                            @endforeach
                        @endif
                    @endif
                </div>
                <!-- Pagination Links -->
                @if ($previousPage != 'laptop')
                    @if (!$userFavorites->isEmpty())
                        <div class="mt-4">
                            {{ $userFavorites->links() }}
                        </div>
                    @endif
                @endif
            </div>

            <!-- Change other user's admin status -->
            <div class="mt-12">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Toggle User Admin Status
                </h2>
                <br>

                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form method="post" class="justify-center" action="{{ route('admin.toggle_user_admin') }}">
                            @method('PUT') @csrf
                            <div class="grid items-center gap-4 grid-row-3 sm:grid-cols-10 justify-between sm:flex-row">

                                <div class="flex flex-col justify-center col-span-6 sm:col-span-2">
                                    <x-label for="make_admin" :value="__('Toggle this user\'s admin status?')" />
                                </div>

                                <div class="flex flex-col justify-center col-span-6 mt-4 sm:mt-0">
                                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="user_id">
                                        @foreach ($usersSelect as $user)
                                            @if ($user->id != auth()->user()->id)
                                                <option value="{{ $user->id }}">{{ $user->email }} : {{ $user->admin ? 'Admin' : 'Regular' }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex flex-col justify-center col-span-6 sm:col-span-2 mt-4 sm:mt-0">
                                    <x-button class="bg-green-500" name="laptop_delete">
                                        {{ __("Toggle Admin Status") }}
                                    </x-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
