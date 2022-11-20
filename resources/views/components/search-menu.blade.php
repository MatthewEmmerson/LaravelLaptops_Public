<div>
    <!-- Search Menu -->
    <div class="mb-4">
        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="GET" action="{{ route('laptop.search') }}" id="searchForm">
                    @method('GET') @csrf
                    <!-- Default Search Options -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-10">
                        <!-- Search Field -->
                        <x-input id="search_make" class="block w-full sm:col-span-5" type="text" value="{{ old('search_make') }}" placeholder="Search Make (e.g. 'Thinkpad')" name="search_make" />

                        <!-- Advanced Options button -->
                        <x-button type="button"
                            id="toggleAdvanced"
                            class="justify-center mt-5 sm:mt-0 sm:col-span-3"
                        >
                            {{ __("Advanced") }}
                        </x-button>

                        <!-- Search button -->
                        <x-button type="submit"
                            class="justify-center mt-5 bg-green-500 sm:mt-0 sm:col-span-2"
                        >
                            {{ __("Search") }}
                        </x-button>
                    </div>

                    <!-- Advanced (default hidden) Search Options -->
                    <div id="advancedDiv" class="hidden mt-4">
                        <hr/>

                        <div class="grid grid-cols-1 gap-5 mt-4 sm:grid-cols-2">
                            <div>
                                <x-label :value="__('Manufacturer')" />
                                <x-input id="search_manufacturer" class="block w-full mt-1" type="text" value="{{ old('search_manufacturer') }}" name="search_manufacturer" placeholder="Search Manufacturer (e.g. 'Lenovo')" />
                            </div>
                            <div>
                                <x-label :value="__('Model')" />
                                <x-input id="search_model" class="block w-full mt-1" type="text"  value="{{ old('search_model') }}" name="search_model" placeholder="Search Model (e.g. X1 'Carbon')" />
                            </div>
                            <div>
                                <x-label for="search_price" :value="__('Price (£)')" />
                                <x-search-menu-greater-lesser-input :input="'price'" />
                            </div>
                            <div>
                                <x-label for="search_ram" :value="__('RAM (GB)')" />
                                <x-search-menu-greater-lesser-input :input="'ram'" />
                            </div>
                            <div>
                                <x-label for="search_ssd" :value="__('SSD (GB)')" />
                                <x-search-menu-greater-lesser-input :input="'ssd'" />
                            </div>
                            <div>
                                <x-label for="search_screen_size" :value="__('Screen Size (Inches)')" />
                                <x-search-menu-greater-lesser-input :input="'screen_size'" />
                            </div>
                            <div>
                                <x-label :value="__('Default OS')" />
                                <x-input id="search_default_os" class="block w-full mt-1" type="text" value="{{ old('search_default_os') }}" name="search_default_os" placeholder="Search OS (e.g. 'Windows')" />
                            </div>
                            <!-- Clear Inputs button. Not reset as need custom functionality -->
                            <x-button type="button" id="clearSearchInputs" class="bg-red-500 sm:mt-6" >
                                {{ __("Clear Inputs") }}
                            </x-button>
                        </div>
                    </div>

                    <x-input id="search_previous" class="hidden" type="text" value="laptops.{{ $previousPage }}" name="search_previous" />
                </form>
            </div>
        </div>
    </div>

    <!-- Show and hide on button click -->
    <script>
        // Get elements
        let advancedButton = document.getElementById('toggleAdvanced');
        let clearButton = document.getElementById('clearSearchInputs');
        let advancedDiv = document.getElementById('advancedDiv');

        // Toggle the visibility of the 'Advanced Options' div
        advancedButton.addEventListener('click', () => {
            if (advancedDiv.classList.contains("hidden")) {
                advancedDiv.classList.remove("hidden");
                advancedDiv.classList.add("block");
            } else {
                advancedDiv.classList.remove("block");
                advancedDiv.classList.add("hidden");
            }
        })

        // Clear all inputs. Cannot just use button type 'reset' as need to clear a input outside of the DIV and reset whilst overwriting Laravel Old() functionality.
        clearButton.addEventListener('click', () => {
            // Get the 'previous' page
            let previousPageInput = document.getElementById('search_previous');
            let previousValue = previousPageInput.value;

            // Get all inputs in the search form
            let form = document.getElementById('searchForm');
            let inputs = form.getElementsByTagName('input');

            // Clear their contents
            for (i = 0; i < inputs.length; i++) {
                inputs[i].value = '';
            }

            // Set the previous page status back to how it was
            previousPageInput.value = previousValue;
        })
    </script>
</div>