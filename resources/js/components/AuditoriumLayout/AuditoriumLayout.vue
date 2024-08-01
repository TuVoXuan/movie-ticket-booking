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
        <button v-for="(cellType, index) in row"
          class="border-[1px] rounded-md p-2 text-sm h-10 cursor-pointer transition-all ease-linear hover:border-blue-300"
          :class="getCellClass(cellType)" @click="handleClickCell(rowLabel, index)"
          :disabled="cellType === CellType.Aisle">
        </button>
      </template>
    </div>
  </div>
</template>

<script>
import { Button, Popover, Select, Segmented } from 'ant-design-vue';
import { generateGridObject, getRangeData } from '../../utils/utils';
import { CellType } from '../../constant/enum';
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
        this.handleChangeCellType(row, index, cellType);
      }
    },
    handleClickCell(positionX, positionY) {
      if (this.selectMode == 'Single') {
        this.handleChangeCellType(positionX, positionY, this.chairType);
      } else {
        console.log('vo');
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
          for (let i = 0; i < this.gridLayout[key].length; i++) {
            if (i >= yStart && i <= yEnd) {
              this.handleChangeCellType(key, i, this.chairType);
            }
          }
        }
      }
    },
    handleChangeCellType(positionX, positionY, cellType) {
      this.gridLayout[positionX][positionY] = cellType;
    },
    getCellClass(cellType) {
      return {
        'bg-purple-300': cellType === CellType.SeatNormal,
        'bg-pink-300': cellType === CellType.SeatVIP,
        'bg-slate-300 cursor-default': cellType === CellType.Aisle,
        'border-blue-400 shadow-md bg-white': cellType === CellType.MultiSelect
      };
    },
  }
}
</script>