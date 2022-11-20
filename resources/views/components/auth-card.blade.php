<div
    class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0"
>
    <!-- Header -->
    <div class="mb-5 text-5xl">
        <h1>Laptop Site</h1>
    </div>

    <div class="mb-6">
        {{ $logo }}
    </div>

    <!-- Display any messages -->
    <x-message-component />

    <div
        class="w-full px-6 py-4 overflow-hidden bg-white rounded-lg shadow-md sm:max-w-md"
    >
        {{ $slot }}
    </div>
</div>
