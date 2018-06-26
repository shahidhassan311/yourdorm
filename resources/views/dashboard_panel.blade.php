@if(\Illuminate\Support\Facades\Auth::user()->role == "admin")
    @include('adminpanel.dashboard')

@elseif(\Illuminate\Support\Facades\Auth::user()->role == "member")
    @include('website.index')
@else
    <p>please login</p>
@endif