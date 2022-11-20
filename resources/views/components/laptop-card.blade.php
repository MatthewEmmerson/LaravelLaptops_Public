@php $adminFavoriteCount = $laptop->adminGetTotalFavoriteCount() == 1 ? "1 user" : $laptop->adminGetTotalFavoriteCount() . " users"; @endphp

<div
    class="flex flex-row items-center p-3 overflow-hidden bg-white border-2 border-gray-600 rounded-lg shadow-lg sm:flex-col"
>
    <div class="flex flex-col lg:flex-row">
        <!-- The image for this laptop -->
        <x-laptop-image :laptop="$laptop"/>

        <!-- Main information -->
        <div class="right-0 px-6 py-4 text-center lg:text-left">
            <h3 class="text-lg font-semibold text-gray-800">
                {{ $laptop->manufacturer->name }}
            </h3>
            <p class="text-gray-800">{{ $laptop->make->name }}</p>
            <p class="text-gray-600">{{ $laptop->model }}</p>
            <p class="text-gray-600">£{{ $laptop->price }}</p>
        </div>
    </div>

    <!-- Less important information -->
    <div class="grid grid-cols-1 gap-2 sm:gap-5 sm:grid-cols-3">
        <p class="text-gray-600">{{ $laptop->screen_size }} Inches</p>
        <p class="text-gray-600">{{ $laptop->default_os }}</p>
        <x-edit :laptop="$laptop"/>
        <p class="text-gray-600">{{ $laptop->ram }}gb RAM</p>
        <p class="text-gray-600">{{ $laptop->ssd }}gb SSD</p>
        <x-favorite :laptop="$laptop"/>
        @auth
            @if (auth()->user()->admin)
                <!-- Logged in admin only row saying how many users have favorited this laptop -->
                <a class="text-gray-600 sm:hidden"
                    href="{{ route('adminfavoriteslaptop', ['laptop_id' => $laptop->id]) }}">
                    Favorited by {{ $adminFavoriteCount }}
                </a>
            @endif
        @endauth
    </div>

    @auth
        @if (auth()->user()->admin)
            <!-- Logged in admin only row saying how many users have favorited this laptop -->
            <a class="hidden col-span-3 mt-2 text-gray-600 sm:block"
                href="{{ route('adminfavoriteslaptop', ['laptop_id' => $laptop->id]) }}">
                Favorited by {{ $adminFavoriteCount }}
            </a>
        @endif
    @endauth
 </div>