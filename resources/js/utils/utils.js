import dayjs from "dayjs";
import { usePage } from "@inertiajs/vue3";
import { CellType } from "../constant/enum";

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

export function generateGridObject(numRows = 0, numColumns = 0) {
  const grid = {};

  if (numRows <= 0 || numColumns <= 0) {
    return grid;
  }
  
  // Generate row labels (A, B, C, ...)
  for (let i = 0; i < numRows; i++) {
      const rowLabel = String.fromCharCode(65 + i); // 65 is the ASCII value for 'A'
      grid[rowLabel] = Array.from({ length: numColumns }, () => ({
        seatLabel: null,
        type: CellType.Unset
      })); // Create an array filled with zeros
  }
  
  return grid;
}

export function getRangeData(array) {
  // Extract x and y values
  const xValues = array.map(item => item.x);
  const yValues = array.map(item => item.y);

  // Get the minimum and maximum values
  const yStart = Math.min(...yValues.map(x => x.charCodeAt(0)));
  const yEnd = Math.max(...yValues.map(x => x.charCodeAt(0)));
  const xStart = Math.min(...xValues);
  const xEnd = Math.max(...xValues);

  // Convert xStart and xEnd back to characters
  return {
    yStart: String.fromCharCode(yStart),
    yEnd: String.fromCharCode(yEnd),
    xStart,
    xEnd
  };
}

export function compareGridLayouts(gridLayoutOrigin, gridLayout) {
  const commonOrigin = {};
  const commonGrid = {};
  const uniqueOrigin = {};
  const uniqueGrid = {};

  const originKeys = Object.keys(gridLayoutOrigin);
  const gridKeys = Object.keys(gridLayout);


  for (const key in gridLayoutOrigin) {
    if (gridLayout[key]) {
      commonOrigin[key] = [];
      commonGrid[key] = [];

      uniqueOrigin[key] = [];
      uniqueGrid[key] = [];

      const originSeats = gridLayoutOrigin[key];
      const currentSeats = gridLayout[key];

      // Tìm phần chung và phần riêng
      const maxLength = Math.max(originSeats.length, currentSeats.length);
      for (let i = 0; i < maxLength; i++) {
        if (originSeats[i] && currentSeats[i]) {
          // Nếu cả hai có phần tử ở cùng chỉ số
          commonOrigin[key].push({...originSeats[i], x_position: i});
          commonGrid[key].push({...currentSeats[i], x_position: i});
        } 
        if (originSeats[i] && !currentSeats[i]) {
          // Nếu chỉ có ở gridLayoutOrigin
          uniqueOrigin[key].push({ ...originSeats[i], x_position: i });
        }
        if (!originSeats[i] && currentSeats[i]) {
          // Nếu chỉ có ở gridLayout
          uniqueGrid[key].push({ ...currentSeats[i], x_position: i });
        }
      }
    }
  }

  if(originKeys.length > gridKeys.length){
    const uniqueKeys = originKeys.filter((key) => !gridKeys.includes(key));
    for (const key of uniqueKeys) {
      uniqueOrigin[key] = gridLayoutOrigin[key];
    }
  }else if(originKeys.length < gridKeys.length) {
    const uniqueKeys = gridKeys.filter((key) => !originKeys.includes(key));
    for (const key of uniqueKeys) {
      uniqueGrid[key] = gridLayout[key];
    }
  }

  return {
    commonOrigin,
    commonGrid,
    uniqueOrigin,
    uniqueGrid,
  };
}