import dayjs from "dayjs";
export function getDate(date, format){
  return dayjs(date).format(format);
}