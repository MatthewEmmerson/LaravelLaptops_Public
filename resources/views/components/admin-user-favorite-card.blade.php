<div class="flex flex-col justify-center mt-4 sm:flex-row">
    <!-- Display Laptop Card -->
    <div class="mr-4">
        <x-laptop-card :laptop="$userFavorite"/>
    </div>

    <!-- Display which user(s) have favorited this laptop -->
    <div class="overflow-y-auto text-center max-h-13 sm:text-left">
        <p>Favorited by:</p>
        <div class="flex flex-col mt-4">
            <ul>
                @if ($userFavorite->users->isEmpty())
                    <p>No-one</p>
                @else
                    @foreach($userFavorite->users as $user)
                        <li>
                            <a
                                href="{{ route('adminfavoritesuser', ['user_id' => $user->id]) }}">
                                {{ $user->name }}
                            </a>
                        </li>
                    @endforeach
                @endif
             </ul>
        </div>
    </div>
</div>