<!-- The laptop's image. If no specific image for this laptop has been uploaded, default back to the laptop placeholder image. -->

<div>
    <img
        class="left-0 flex-shrink-0 w-32 h-32 sm:h-44 md:w-48 lg:h-32 lg:w-32"
        src="{{ URL::to('/') }}/laptops/images/{{$laptop->id}}.png"
        onerror="this.onerror=null;this.src='{{ URL::to('/') }}/images/laptop_placeholder.svg';"
        alt="Laptop Image"
    />
</div>
