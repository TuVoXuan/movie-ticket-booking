<template>
  <div>
    <div class="flex justify-center items-center gap-x-3 mb-4">
      <div class="flex flex-col gap-2">
        <label>Chair type</label>
        <a-segmented v-model:value="chairType" :options="chairTypeOptions">
          <template #label="{ label }">
            {{ label }}
          </template>
        </a-segmented>
      </div>

      <div class="flex flex-col gap-2">
        <label>Select mode</label>
        <a-segmented v-model:value="selectMode" :options="selectModeOptions" />
      </div>
    </div>

    <div class="grid gap-2" :style="{ 'grid-template-columns': `repeat(${columns + 1}, minmax(0, 1fr))` }">
      <template v-for="n in columns">
        <div class="h-10 text-sm" :class="{ 'col-start-2': n === 1 }">
          <a-popover title="Actions">
            <template #content>
              <div class="flex gap-x-3">
                <a-button type="primary" @click="handleChangeColumType(n - 1, CellType.SeatNormal)">Mark as
                  chairs</a-button>
                <a-button @click="handleChangeColumType(n - 1, CellType.Aisle)">Mark as aisle</a-button>
              </div>
            </template>
            <a-button class="w-full" type="primary">{{ n }}</a-button>
          </a-popover>
        </div>
      </template>
    </div>
    <div class="grid gap-2 mb-4" :style="{ 'grid-template-columns': `repeat(${columns + 1}, minmax(0, 1fr))` }">
      <template v-for="(row, rowLabel) in gridLayout">
        <div class="content-center text-center">{{ rowLabel }}</div>
        <button v-for="(cell, index) in row"
          class="border-[1px] rounded-md p-2 text-sm h-10 cursor-pointer transition-all ease-linear hover:border-blue-300"
          :class="getCellClass(cell.type)" @click="handleClickCell(rowLabel, index)"
          :disabled="cell.type === CellType.Aisle">
          {{ cell.seatLabel }}
        </button>
      </template>
    </div>
  </div>
  <div class="flex justify-center mt-4 gap-x-3">
    <div class="flex items-center gap-x-2">
      <span class="h-5 w-5 rounded-md bg-purple-200"></span>
      <span>Ghế thường</span>
    </div>
    <div class="flex items-center gap-x-2">
      <span class="h-5 w-5 rounded-md bg-pink-200"></span>
      <span>Ghế VIP</span>
    </div>
    <div class="flex items-center gap-x-2">
      <span class="h-5 w-5 rounded-md bg-slate-300"></span>
      <span>Lối đi</span>
    </div>
  </div>
</template>

<script>
import { Button, Popover, Select, Segmented } from 'ant-design-vue';
import { generateGridObject, getRangeData } from '../../utils/utils';
import { CellType } from '../../constant/enum';
import { CodeSandboxCircleFilled } from '@ant-design/icons-vue';
export default {
  name: 'AuditoriumLayout',
  components: {
    Button,
    Popover,
    Select,
    Segmented
  },
  props: ['rows', 'columns'],
  data() {
    const chairTypeOptions = [
      {
        value: CellType.SeatNormal,
        label: 'Ghế thường'
      },
      {
        value: CellType.SeatVIP,
        label: 'Ghế VIP'
      }
    ];
    const selectModeOptions = ['Single', 'Multiple'];

    return {
      CellType,
      chairTypeOptions,
      selectModeOptions,
      selectMode: selectModeOptions[0],
      chairType: chairTypeOptions[0].value,
      gridLayout: {},
      multiCellSelected: []
    }
  },
  watch: {
    rows(newVal, oldVal) {
      this.gridLayout = generateGridObject(newVal, this.columns);
    },
    columns(newVal, oldVal) {
      this.gridLayout = generateGridObject(this.rows, newVal);
    }
  },
  methods: {
    handleChangeColumType(index, cellType) {
      for (const row in this.gridLayout) {
        const shouldUpdateCellLabel = this.needUpdateCellLabel(row, index, cellType)

        this.handleChangeCellType(row, index, cellType, true);
        if (shouldUpdateCellLabel) {
          this.handleMarkLabelForCell(row);
        }
      }
    },
    handleClickCell(positionX, positionY) {
      if (this.selectMode == 'Single') {
        const shouldUpdateCellLabel = this.needUpdateCellLabel(positionX, positionY, this.chairType);
        this.handleChangeCellType(positionX, positionY, this.chairType, true);
        if (shouldUpdateCellLabel) {
          this.handleMarkLabelForCell(positionX);
        }
      } else {
        if (this.multiCellSelected.length < 2) {
          this.multiCellSelected.push({
            x: positionX,
            y: positionY
          });

          this.handleChangeCellType(positionX, positionY, CellType.MultiSelect);
        }

        if (this.multiCellSelected.length == 2) {
          this.handleChangeMultipleCellType();
          this.multiCellSelected = [];
        }
      }
    },
    handleChangeMultipleCellType() {
      const { xEnd, xStart, yEnd, yStart } = getRangeData(this.multiCellSelected);
      for (const key in this.gridLayout) {
        if (key.charCodeAt(0) >= xStart.charCodeAt(0) && key.charCodeAt(0) <= xEnd.charCodeAt(0)) {
          let shouldUpdateLabelRow = false;
          for (let i = 0; i < this.gridLayout[key].length; i++) {
            if (i >= yStart && i <= yEnd) {
              if (this.gridLayout[key][i].type !== CellType.Aisle) {
                const canUpdateCellLabel = !shouldUpdateLabelRow && this.needUpdateCellLabel(key, i, this.chairType)
                this.handleChangeCellType(key, i, this.chairType);
                if (canUpdateCellLabel) {
                  this.handleMarkLabelForCell(key);
                  shouldUpdateLabelRow = this.needUpdateCellLabel(key, i, this.chairType);
                }
              }
            }
          }
        }
      }
    },
    handleChangeCellType(positionX, positionY, cellType, toggle) {
      if (this.gridLayout[positionX][positionY].type === cellType && toggle) {
        this.gridLayout[positionX][positionY].type = CellType.Unset;
      } else {
        this.gridLayout[positionX][positionY].type = cellType;
      }
    },
    handleMarkLabelForCell(row) {
      console.log('vo');
      let seatNumber = 0;
      for (let i = 0; i < this.gridLayout[row].length; i++) {
        const curCell = this.gridLayout[row][i]
        if (curCell.type === CellType.SeatNormal || curCell.type === CellType.SeatVIP) {
          seatNumber++;
          curCell.seatLabel = `${row}${seatNumber}`;
        } else {
          curCell.seatLabel = null;
        }
      }
    },
    needUpdateCellLabel(positionX, positionY, type) {
      const currCellType = this.gridLayout[positionX][positionY].type;

      const aisleUnset = [CellType.Aisle, CellType.Unset, CellType.MultiSelect];
      const seatTypes = [CellType.SeatNormal, CellType.SeatVIP];

      if (
        (aisleUnset.includes(currCellType) && seatTypes.includes(type)) ||
        (seatTypes.includes(currCellType) && aisleUnset.includes(type))
      ) {
        return true
      }

      return false;
    },
    getCellClass(cellType) {
      return {
        'bg-purple-200 text-purple-500 font-medium': cellType === CellType.SeatNormal,
        'bg-pink-200 text-pink-500 font-medium': cellType === CellType.SeatVIP,
        'bg-slate-300 cursor-default': cellType === CellType.Aisle,
        'border-blue-400 shadow-md bg-white': cellType === CellType.MultiSelect
      };
    },
  }
}
</script>