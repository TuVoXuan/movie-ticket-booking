import dayjs from "dayjs";
import { usePage } from "@inertiajs/vue3";

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

export function getRemovedItems(original, newArray) {
  return original.filter(item => !newArray.includes(item));
}

export function getNewItems(original, newArray) {
  return newArray.filter(item => !original.includes(item));
}

export function removeEmptyFields(obj) {
  return Object.fromEntries(
      Object.entries(obj).filter(([_, value]) => 
          value !== null && value !== undefined && value !== ''
      )
  );
}