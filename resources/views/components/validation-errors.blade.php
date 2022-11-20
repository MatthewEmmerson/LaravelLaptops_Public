@if ($errors->any())
<div class="mb-12 overflow-hidden bg-white rounded-lg shadow-sm">
    <div class="p-6 bg-red-600 border-b border-gray-200">
        <div class="flex flex-col items-center justify-between sm:flex-row">
            <img
                style="max-width: 100px"
                src="{{ URL::to('/') }}/images/error.png"
            />
            <div class="ml-3" {{ $attributes }}>
                <div class="text-white font-large">
                    {{ __("Error") }}
                </div>

                <ul class="mt-3 text-sm text-white list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endif
