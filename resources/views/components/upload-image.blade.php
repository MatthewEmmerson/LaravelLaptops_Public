<div>
    <div class="mt-12">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Edit Laptop Image
        </h2>
        <br>

        <div class="overflow-hidden bg-white rounded-lg shadow-sm">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex flex-col items-center justify-between sm:flex-row">
                    <x-label for="image" :value="__('Change this Laptops image?')" />

                    <!-- The image for this laptop -->
                    <x-laptop-image :laptop="$laptop"/>

                    <!-- The rest of the form -->
                    <form method="POST" action="{{ route('laptop.update_image') }}" enctype="multipart/form-data">
                        @method('PUT') @csrf
                        <div class="flex flex-col items-center justify-between sm:flex-row">
                            <x-input id="id" class="hidden" type="text" value="{{ $laptop->id }}" name="id" />
                            <input id="image" class="block w-full mt-1" type="file" name="image" />
                            <x-button>
                                {{ __("Update") }}
                            </x-button>
                        </div>
                    </form>

                    <form class="mt-5 ml-0 sm:ml-4 sm:mt-0" method="POST" action="{{ route('laptop.delete_image', ['id' => $laptop->id])}}">
                        @method('DELETE') @csrf
                        <x-label for="delete" :value="__('Delete image?')" />
                        <x-button name="delete" class="mt-2 bg-red-500">
                            {{ __("Delete") }}
                        </x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>