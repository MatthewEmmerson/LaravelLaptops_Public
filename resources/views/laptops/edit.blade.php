@php $adminFavoriteCount = $laptop->adminGetTotalFavoriteCount() == 1 ? "1 user" : $laptop->adminGetTotalFavoriteCount() . " users"; @endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __("Edit Laptop") }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">

            <!-- Display any messages -->
            <x-message-component />

            <!-- Edit Laptop -->
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Laptop
                </h2>
                <br />

                <!-- Form to edit details -->
                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8" >
                                    <form method="POST" action="{{ route('laptop.update') }}">
                                        @method('PUT')
                                        @csrf

                                        <!-- Edit Laptop Form inputs -->
                                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                            <div>
                                                <x-label for="manufacturer_id" :value="__('Manufacturer')" />
                                                <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="manufacturer_id">
                                                    @foreach ($manufacturers as $manufacturer)
                                                        <option value="{{ $manufacturer->id }}" {{ $manufacturer->id === $laptop->manufacturer_id ? 'selected' : '' }}>{{ $manufacturer->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <x-label for="make_id" :value="__('Make')" />
                                                <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="make_id">
                                                    @foreach ($laptop_makes as $make)
                                                        <option value="{{ $make->id }}" {{ $make->id === $laptop->make_id ? 'selected' : '' }}>{{ $make->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <x-label for="model" :value="__('Model')" />
                                                <x-input id="model" class="block w-full mt-1" type="text" value="{{ $laptop->model }}" name="model" />
                                            </div>
                                            <div>
                                                <x-label for="price" :value="__('Price (£)')" />
                                                <x-input id="price" class="block w-full mt-1" type="number" step="any" value="{{ $laptop->price }}" name="price" />
                                            </div>
                                            <div>
                                                <x-label for="RAM" :value="__('RAM (GB)')" />
                                                <x-input id="RAM" class="block w-full mt-1" type="number" value="{{ $laptop->ram }}" name="ram" />
                                            </div>
                                            <div>
                                                <x-label for="ssd" :value="__('SSD (GB)')" />
                                                <x-input id="ssd" class="block w-full mt-1" type="number" value="{{ $laptop->ssd }}" name="ssd" />
                                            </div>
                                            <div>
                                                <x-label for="screen_size" :value="__('Screen Size (Inches)')" />
                                                <x-input id="screen_size" class="block w-full mt-1" type="number" value="{{ $laptop->screen_size }}" name="screen_size" />
                                            </div>
                                            <div>
                                                <x-label for="default_os" :value="__('Default OS')" />
                                                <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="default_os">
                                                    <option value="Windows" {{ $laptop->default_os === "Windows" ? 'selected' : '' }}>Windows</option>
                                                    <option value="ChromeOS" {{ $laptop->default_os === "ChromeOS" ? 'selected' : '' }}>ChromeOS</option>
                                                    <option value="MacOS" {{ $laptop->default_os === "MacOS" ? 'selected' : '' }}>MacOS</option>
                                                    <option value="Linux" {{ $laptop->default_os === "Linux" ? 'selected' : '' }}>Linux</option>
                                                </select>
                                            </div>
                                        </div>

                                        <x-input id="id" class="hidden" type="text" value="{{ $laptop->id }}" name="id" />

                                        <!-- Edit Laptop Form button -->
                                        <div class="flex items-center justify-center mt-4 sm:justify-end">
                                            <x-button class="ml-3">
                                                {{ __('Update') }}
                                            </x-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Laptop Image -->
            <x-upload-image :laptop="$laptop" />

            <!-- Favorite this Laptop -->
            <div class="mt-12">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    @if ($laptop->favoritedByCurrentUser())
                        Unfavorite This Laptop
                    @else
                        Favorite This Laptop
                    @endif
                </h2>
                <br>

                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col items-center justify-between sm:flex-row">

                            @auth
                                @if (auth()->user()->admin)
                                    <!-- Logged in admin only row saying how many users have favorited this laptop -->
                                    <x-label>Favorited by {{ $adminFavoriteCount }}</x-label>
                                @endif
                            @endauth

                            <x-label for="laptop_favorite" :value="__('Do you want to toggle this laptops favorited status?')" />
                            <form method="GET" action="{{ route('laptop.togglefavorite', $laptop->id) }}">
                                @method('GET')
                                <x-button name="laptop_favorite">
                                    @if ($laptop->favoritedByCurrentUser())
                                        Unfavorite This Laptop
                                    @else
                                        Favorite This Laptop
                                    @endif
                                </x-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete this Laptop -->
            <div class="mt-12">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Delete This Laptop
                </h2>
                <br>

                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col items-center justify-between sm:flex-row">
                            <x-label for="laptop_delete" :value="__('This will permanently delete this laptop- are you sure?')" />
                            <form method="POST" action="{{ route('laptop.delete', $laptop->id) }}">
                                @method('DELETE') @csrf
                                <x-button class="bg-red-500" name="laptop_delete">
                                    {{ __("Delete") }}
                                </x-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
