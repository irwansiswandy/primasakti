@if (count($errors) > 0)
    @foreach ($errors->all() as $error)
      <li style="list-style: none; color: red">* {{ $error }}</li>
    @endforeach
@endif
