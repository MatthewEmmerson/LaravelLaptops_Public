<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Favorited Laptops') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">

            <!-- Display any messages -->
            <x-message-component />

            <!-- View Your Favorited Laptops -->
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Your Favorited Laptops
            </h2>
            <br>

            <!-- Search Menu -->
            <x-search-menu :previousPage="'favorited'" />

            <!-- Grid with all laptops -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <div class="flex items-center justify-center">

                                    <!-- Laptop Cards -->
                                    @if ($laptops->isEmpty())
                                        @if (Request::is('searchlaptops*'))
                                            <p class="text-center">None of your favorited laptops match that search criteria</p>
                                        @else
                                            <p class="text-center">You have no favorited laptops</p>
                                        @endif
                                    @else
                                        <div class="grid grid-cols-1 gap-10 sm:grid-cols-2 md:grid-cols-3">
                                            @foreach ($laptops as $laptop)
                                                <x-laptop-card :laptop="$laptop"/>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>

                                <!-- Pagination Links -->
                                @if (!$laptops->isEmpty())
                                    <div class="mt-4">
                                        {{ $laptops->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
