<div class="bg-white h-12 flex justify-between items-center p-4">
  <h1 class="font-bold text-2xl">
    {{ $getTitle }}
  </h1>

  @auth
    <span>Hi, {{ auth()->user()->name }}</span>
  @endauth
  @guest
    <span class="font-medium">Hi, guest</span>
  @endguest
</div>
