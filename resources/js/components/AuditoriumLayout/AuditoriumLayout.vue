<template>
  <div>
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
        <div v-for="(cellType, index) in row"
          class="border-[1px] p-2 text-sm h-10 cursor-pointer transition-colors ease-linear hover:bg-pink-100"
          :class="getCellClass(cellType)" @click="handleChangeCellType(rowLabel, index, CellType.SeatNormal)">
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { Button, Popover } from 'ant-design-vue';
import { generateGridObject } from '../../utils/utils';
import { CellType } from '../../constant/enum';
export default {
  name: 'AuditoriumLayout',
  components: {
    Button,
    Popover
  },
  props: ['rows', 'columns'],
  data() {
    return {
      CellType,
      gridLayout: {}
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
    handleChangeCellType(positionX, positionY, cellType) {
      this.gridLayout[positionX][positionY] = cellType;
    },
    getCellClass(cellType) {
      return {
        'bg-purple-300': cellType === CellType.SeatNormal,
        'bg-slate-500': cellType === CellType.Aisle,
        'bg-pink-300': cellType === CellType.SeatVIP,
      };
    },
  }
}
</script>