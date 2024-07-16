import { usePage } from "@inertiajs/vue3";

export function getQuery(){
  const page = usePage();
  return {...page.props.query};
}