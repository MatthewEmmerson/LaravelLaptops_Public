<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Manage Laptops') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">

            <!-- Display any messages -->
            <x-message-component />

            <!-- Add New Laptop Button -->
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Add New Laptop
                </h2>
                <br>

                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col items-center justify-between sm:flex-row">
                            <x-label for="laptop_add" :value="__('This will add a new laptop into the website')" />
                            <form method="GET" action="{{ route('addlaptop') }}">
                                <x-button class="justify-end mt-5 bg-green-500 sm:mt-0" name="laptop_add">
                                    {{ __("Add") }}
                                </x-button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Manage Laptops -->
            <div class="mt-12">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Manage Laptops
                </h2>
                <br>

                <x-search-menu :previousPage="'manage'" />

                <!-- List with all laptops -->
                <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                    <div class="flex items-center justify-center">

                                        <!-- Manage Laptop Cards -->
                                        <div class="w-full pr-5 overflow-y-auto max-h-64">
                                            @if ($laptops->isEmpty())
                                                @if (Request::is('searchlaptops*'))
                                                    <p class="text-center">No laptops match that search criteria</p>
                                                @else
                                                    <p class="text-center">There are no laptops in the system</p>
                                                @endif
                                            @else
                                                @foreach ($laptops as $laptop)
                                                    <x-manage-laptop-card :laptop="$laptop"/>
                                                @endforeach
                                            @endif
                                        </div>
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
    </div>
</x-app-layout>