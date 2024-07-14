<script>
  console.log('hello world');
</script>
<x-layout>
  <x-breadcrumbs class="mb-4" :links="['Films' => route('films.index'), 'Create' => '#']" />

  <x-box class="px-10 py-8">
    <form action="" method="post" class="grid grid-cols-2 gap-3">
      @csrf
      <div>
        <x-label for="title" :required="true">Title</x-label>
        <x-text-field type='text' name="title" />
      </div>
      <div>
        <x-label for="release_date" :required="true">Release Date</x-label>
        <x-text-field type='date' name="release_date" />
      </div>
      <div>
        <x-label for="duration" :required="true">Duration</x-label>
        <x-text-field type='number' name="duration" />
      </div>
      <div>
        <x-label for="age_restricted">Age restricted</x-label>
        <x-text-field type='number' name="age_restricted" />
      </div>
      <div>
        <x-label for="trailer" :required="true">Trailer Link</x-label>
        <x-text-field type='number' name="trailer" />
      </div>
      <div>
        <x-label for="thumbnail" :required="true">Thumbnail</x-label>
        <x-text-field type='file' name="thumbnail" />
      </div>
      <div>
        <x-label for="thumbnail_bg">Thumbnail Background</x-label>
        <x-text-field type='file' name="thumbnail_bg" />
      </div>

      <div>
        <x-label for="actor">Actors</x-label>
        <x-text-field type='string' name="actor" />
      </div>

      <div>
        <x-label for="actor">Directors</x-label>
        <x-text-field type='string' name="actor" />
      </div>

      <div>
        <x-label for="actor">Producers</x-label>
        <x-text-field type='string' name="actor" />
      </div>

      <div class="col-span-2">
        <x-label for="description" :required="true">Description</x-label>
        <x-text-field type='textarea' name="description" />
      </div>
    </form>
  </x-box>
</x-layout>
