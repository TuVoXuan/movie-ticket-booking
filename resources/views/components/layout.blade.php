<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Movie Ticket Dashboard</title>
  @vite('resources/css/app.css')

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
</head>

<body class="m-0 flex h-screen overflow-hidden">
  <div class="w-[200px] shrink-0 h-screen bg-white overflow-y-auto px-3">
    <div class="py-1 h-12 mb-10">
      <a href="/">
        <img class="object-contain h-full mx-auto"
          src="{{ asset('/assets/images/logo.png') }}" alt="logo">
      </a>
    </div>

    <x-sidebar.index />
  </div>
  <div class="min-h-screen w-[calc(100%-200px)]">
    <x-topbar />
    <div id="main-section"
      class="px-8 py-4 bg-[#FAFBFC] h-[calc(100%-48px)] overflow-y-auto">
      <div>
        {{ $slot }}
      </div>
    </div>

  </div>
</body>

</html>
