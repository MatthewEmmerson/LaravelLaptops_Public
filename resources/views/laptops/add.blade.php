<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
             {{ __("Add New Laptop") }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="px-6 mx-auto max-w-7xl lg:px-8">

            <!-- Display any messages -->
            <x-message-component />

            <!-- Add New Laptops -->
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Add New Laptop
            </h2>
            <br />

            <!-- Add New Laptop -->
            <div class="overflow-hidden bg-white rounded-lg shadow-sm">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col">
                        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8" >
                                <form method="POST" action="{{ route('laptop.add') }}" enctype="multipart/form-data">
                                    @method('PUT')
                                    @csrf

                                    <!-- Add New Laptop Form -->
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <div>
                                            <x-label for="manufacturer_id" :value="__('Manufacturer')" />
                                            <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="manufacturer_id">
                                                @foreach ($manufacturers as $manufacturer)
                                                    <option value="{{ $manufacturer->id }}">{{ $manufacturer->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <x-label for="make_id" :value="__('Make')" />
                                            <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="make_id">
                                                @foreach ($laptop_makes as $make)
                                                    <option value="{{ $make->id }}">{{ $make->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <x-label for="model" :value="__('Model')" />
                                            <x-input id="model" class="block w-full mt-1" type="text" value="" name="model" />
                                        </div>
                                        <div>
                                            <x-label for="price" :value="__('Price (£)')" />
                                            <x-input id="price" class="block w-full mt-1" type="number" step="any" value="" name="price" />
                                        </div>
                                        <div>
                                            <x-label for="RAM" :value="__('RAM (GB)')" />
                                            <x-input id="RAM" class="block w-full mt-1" type="number" value="" name="ram" />
                                        </div>
                                        <div>
                                            <x-label for="ssd" :value="__('SSD (GB)')" />
                                            <x-input id="ssd" class="block w-full mt-1" type="number" value="" name="ssd" />
                                        </div>
                                        <div>
                                            <x-label for="screen_size" :value="__('Screen Size (Inches)')" />
                                            <x-input id="screen_size" class="block w-full mt-1" type="number" value="" name="screen_size" />
                                        </div>
                                        <div>
                                            <x-label for="default_os" :value="__('Default OS')" />
                                            <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="default_os">
                                                <option value="Windows">Windows</option>
                                                <option value="ChromeOS">ChromeOS</option>
                                                <option value="MacOS">MacOS</option>
                                                <option value="Linux">Linux</option>
                                            </select>
                                        </div>
                                        <div>
                                            <x-label for="image" :value="__('Add an image (.png) for this laptop?')" />
                                            <input id="image" class="block w-full mt-1" type="file" name="image" />
                                        </div>

                                        <x-button class="ml-3">
                                             {{ __('Add New Laptop') }}
                                        </x-button>
                                    </div>

                                    <x-input id="id" class="hidden" type="text" value="0" name="id" />

                                    <!-- Add New Laptop Button -->
                                    <div class="flex items-center justify-center mt-4 sm:justify-end">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
