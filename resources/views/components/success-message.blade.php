@if (session('success'))
<div
    class="mb-12 overflow-hidden bg-white rounded-lg shadow-sm"
    id="success-message"
>
    <div class="p-6 bg-green-600 border-b border-gray-200">
        <div class="flex flex-col items-center justify-between sm:flex-row">
            <img
                style="max-width: 100px"
                src="{{ URL::to('/') }}/images/success.png"
            />
            <div {{ $attributes }}>
                <div class="text-white font-large">
                    {{ __("Success") }}
                </div>

                <ul class="mt-3 text-sm text-white list-disc list-inside">
                    {{
                        session("success")
                    }}
                </ul>
            </div>
        </div>
    </div>
</div>

@endif
