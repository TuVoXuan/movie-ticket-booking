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
    case 'ascend':
      return 'asc';
    case 'descend':
      return 'desc';
    case "asc":
      return 'ascend';
    case 'desc':
      return 'descend';
    default:
      return null;
  }
}