<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Movie Ticket Dashboard</title>
  @vite('resources/css/app.css')
</head>

<body class="m-0 flex h-screen overflow-hidden">
  <div class="w-[200px] shrink-0 h-screen bg-red-400 overflow-y-auto">
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
    <h1 class="h-10">hello</h1>
  </div>
  <div class="w-full min-h-screen">
    <div class="h-[48px] bg-yellow-300">top bar</div>
    <div id="main-section" class="p-3 bg-slate-200 h-[calc(100%-48px)] overflow-y-auto">
      <div>breadcrumbs</div>
      <div>
        {{ $slot }}
      </div>
    </div>

  </div>
</body>

</html>
