@php $adminFavoriteCount = $laptop->adminGetTotalFavoriteCount() == 1 ? "1 user" : $laptop->adminGetTotalFavoriteCount() . " users"; @endphp

<div
    class="flex flex-col justify-between w-full p-2 mt-2 border-2 border-gray-600 rounded-lg sm:flex-row"
>
    <!-- Main Information -->
    <div class="flex flex-col items-center justify-around md:flex-row">
        <div class="flex flex-row ml-2">
            @if (auth()->user()->admin)
                <!-- Logged in admin only row saying how many users have favorited this laptop -->
                <a class="mr-2"
                    href="{{ route('adminfavoriteslaptop', ['laptop_id' => $laptop->id]) }}">
                {{ $adminFavoriteCount }}</a>
            @endif
            <x-favorite :laptop="$laptop"/>
        </div>
        <h3 class="ml-0 md:ml-2">{{ $laptop->manufacturer->name }}</h3>
        <p class="ml-0 md:ml-2">{{ $laptop->make->name }}</p>
        <p class="ml-0 md:ml-2">{{ $laptop->model }}</p>
    </div>

    <!-- Main action buttons -->
    <div
        class="flex flex-row justify-around gap-2 mt-5 sm:flex-col md:flex-row sm:mt-0"
    >
        <form method="GET" action="{{ route('editlaptops', $laptop->id) }}">
            @method('GET')
            <x-button>
                {{ __("Edit") }}
            </x-button>
        </form>

        <form method="POST" action="{{ route('laptop.delete', $laptop->id) }}">
            @method('DELETE') @csrf
            <x-button class="bg-red-500" name="laptop_delete">
                {{ __("Delete") }}
            </x-button>
        </form>
    </div>
</div>
