<!-- Greater and Lesser Than search inputs -->

<div>
    <div id="search_{{ $input }}" name="search_{{ $input }}" class="flex flex-row">
        <x-input id="search_{{ $input }}_greater" class="block w-full mt-1 mr-2" type="number" step="any" value="{{ old('search_' . $input . '_greater') ?? 0 }}" name="search_{{ $input }}_greater" placeholder="Greater Than" />
        <x-input id="search_{{ $input }}_lesser" class="block w-full mt-1" type="number" step="any" value="{{ old('search_' . $input  . '_lesser') }}" name="search_{{ $input }}_lesser" placeholder="Less Than"/>
    </div>
</div>