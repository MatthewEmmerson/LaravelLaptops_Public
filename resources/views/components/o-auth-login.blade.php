<!-- Log in With ... Option -->
<div>
    <div class="overflow-hidden bg-white rounded-lg shadow-sm">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex flex-row justify-between justify-center w-full p-2 mt-2 border-2 border-gray-600 rounded-lg sm:flex-row">
                <!-- Provider Image --> 
                <img class="w-16 h-16" src="{{ URL::to('/') }}/images/{{ $provider }}-oauth-logo.png" />

                <!-- Provider 'Log In With ...' message --> 
                <a href="/login/{{ $provider }}" class="m-auto">Log in with {{ UCFirst($provider) }}</a>
            </div>
        </div>
    </div>
</div>