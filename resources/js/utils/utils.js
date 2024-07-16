import dayjs from "dayjs";
import { usePage } from "@inertiajs/vue3";

export function getDate(date, format){
  return dayjs(date).format(format);
}

export function getQuery(){
  const page = usePage();
  return {...page.props.query};
}

export function convertSortOrder(sortOrder){
  switch (sortOrder) {
    case 1:
      return 'asc';
    case -1:
      return 'desc';
    case "asc":
      return 1;
    case 'desc':
      return -1;
    default:
      return null;
  }
}