<template>
  <Box>
    <div class="flex justify-end">
      <Button>
        <Link :href="route('artists.create')">Add New Artist</Link>
      </Button>
    </div>

    <DataTable v-model:selection="selectedRow" dataKey="id" :value="artists.data" removableSort lazy paginator
      :first="first" :totalRecords="artists.total" :rows="rows" :sortField="sortField" :sortOrder="sortOrder"
      :rowsPerPageOptions="[5, 10, 20, 30]" @page="onChangePage" @sort="onSort"
      paginatorTemplate="CurrentPageReport FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"
      currentPageReportTemplate="Showing {first} to {last} of {totalRecords} artists">
      <template #header>
        <div class="flex flex-wrap gap-2 items-center justify-between">
          <h4 class="text-xl font-medium">Manage Artists</h4>

          <IconField>
            <InputIcon class="pi pi-search" />
            <InputText :value="search" @input="onSearchChange" class="w-full" placeholder="Search" />
          </IconField>
        </div>
      </template>

      <Column selectionMode="single" headerStyle="width: 3rem"></Column>
      <Column field="name" header="Name" sortable></Column>
      <Column field="birthday" header="Birthday" sortable>
        <template #body="slotProps">
          {{ getDate(slotProps.data.birthday, 'DD-MM-YYYY') }}
        </template>
      </Column>
      <Column field="created_at" header="Created At" sortable>
        <template #body="slotProps">
          {{ getDate(slotProps.data.created_at, 'DD-MM-YYYY') }}
        </template>
      </Column>
      <Column header="Actions">
        <template #body="slotProps">
          <div class="flex items-center gap-x-3">
            <button class="h-8 w-8 text-blue-400 bg-white hover:bg-blue-100 transition-colors ease-linear rounded-full">
              <Link :href="route('artists.edit', slotProps.data.id)">
              <i class="pi pi-pencil"></i>
              </Link>
            </button>
            <button @click="confirmDelete(slotProps.data.id)"
              class="h-8 w-8 text-red-400 bg-white hover:bg-red-100 transition-colors ease-linear rounded-full">
              <i class="pi pi-trash"></i>
            </button>
          </div>
        </template>
      </Column>
    </DataTable>
  </Box>
</template>

<script>
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import { router } from '@inertiajs/vue3';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { getDate, getQuery, convertSortOrder } from '../../utils/utils';
import { debounce } from 'lodash';

export default {
  name: "ArtistsPage",
  props: ['artists'],
  components: {
    DataTable,
    Column,
    Button,
    InputIcon,
    IconField,
    InputText
  },
  data() {
    return {
      search: '',
      rows: 10,
      selectedRow: null,
      first: 0,
      loading: false,
      sortField: null,
      sortOrder: null,
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
    onSort(event) {
      const query = getQuery();
      router.get(route('artists.index'), {
        ...query,
        page: 1,
        sort: event.sortField,
        sort_order: convertSortOrder(event.sortOrder)
      });
    },
    onSearchChange: debounce(function (event) {
      this.handleSearch(event.target.value);
    }, 1000),
    handleSearch(searchText) {
      this.search = searchText;
      const query = getQuery();
      router.get(route('artists.index'), {
        ...query,
        search: searchText
      });
    },
    confirmDelete(id) {
      this.$confirm.require({
        message: 'Are you sure you want to delete it?',
        header: 'Warning',
        icon: 'pi pi-info-circle',
        rejectProps: {
          label: "Cancel",
          severity: 'secondary',
          outlined: true
        },
        acceptProps: {
          label: 'Delete',
          severity: 'danger'
        },
        accept: () => {
          router.delete(route('artists.destroy', id));
        }
      })
    }
  },
  mounted() {
    const query = getQuery();
    this.first = (this.artists.current_page - 1) * this.artists.per_page;
    this.rows = this.artists.per_page;
    this.sortField = query.sort || null;
    this.sortOrder = convertSortOrder(query.sort_order);
    this.search = query.search || null;
  },
}

</script>
