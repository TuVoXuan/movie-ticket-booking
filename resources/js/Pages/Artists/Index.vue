<template>
  <Box>
    <div class="flex justify-end">
      <Button>
        <Link :href="route('artists.create')">Add New Artist</Link>
      </Button>
    </div>

    <DataTable v-model:selection="selectedRow" dataKey="id" :value="artists.data" lazy paginator :first="first"
      :totalRecords="artists.total" :rows="rows" :rowsPerPageOptions="[5, 10, 20, 30]" @page="onChangePage"
      @update:rows="onChangeRows">
      <Column selectionMode="single" headerStyle="width: 3rem"></Column>
      <Column field="name" header="Name" />
      <Column field="birthday" header="Birthday">
        <template #body="slotProps">
          {{ getDate(slotProps.data.birthday, 'DD-MM-YYYY') }}
        </template>
      </Column>
      <Column header="Actions">
        <template #body="slotProps">
          <div class="flex items-center gap-x-3">
            <button class="h-8 w-8 text-blue-400 bg-white hover:bg-blue-100 transition-colors ease-linear rounded-full">
              <i class="pi pi-pencil"></i>
            </button>
            <button class="h-8 w-8 text-red-400 bg-white hover:bg-red-100 transition-colors ease-linear rounded-full">
              <i class="pi pi-trash"></i>
            </button>
          </div>
        </template>
      </Column>
    </DataTable>
    <!-- <Paginator :rows="rows" v-model="first" :totalRecords="artists.total" :rowsPerPageOptions="[10, 20, 30]"
      @page="changePage" @update:rows="changeRows" @update:first="changeFirst">
    </Paginator> -->
  </Box>
</template>

<script>
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import { getDate } from '../../utils/Date';
import { router } from '@inertiajs/vue3';
import { getQuery } from '../../utils/Query';

export default {
  name: "ArtistsPage",
  props: ['artists'],
  components: {
    DataTable,
    Column,
    Button,
    Paginator
  },
  data() {
    return {
      rows: 10,
      selectedRow: null,
      first: 0,
      loading: false,
    }
  },
  methods: {
    getDate,
    onChangePage(event) {
      const query = getQuery();
      router.get(route('artists.index'), {
        ...query,
        page: event.page + 1,
        page_size: event.rows
      })
    },
    onChangeRows(value) {
      this.rows = value;
    }
  },
  mounted() {
    this.first = (this.artists.current_page - 1) * this.artists.per_page;
    this.rows = this.artists.per_page;
  },
}

</script>
