<x-layout>
  <x-breadcrumbs class="mb-4" :links="['Films' => route('films.index')]" />

  <x-box>
    <x-button variant="contained">
      <a href="{{ route('films.create') }}">Create New Film</a>
    </x-button>
    <x-table />
  </x-box>
</x-layout>
