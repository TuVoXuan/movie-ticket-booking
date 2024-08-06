<template>
  <div ref="container">
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

    <div class="flex justify-center mb-4">
      Number of seats : {{ seatCount }}/{{ capacity }}
    </div>

    <div class="flex flex-col gap-2 mb-4 overflow-x-auto relative">
      <div ref="columnsIndex" class="flex gap-2 m-auto" :class="{ 'flex-row-reverse': seatDirection === 'RTL' }">
        <div class="w-10 h-10 bg-white shrink-0 sticky left-0 z-[1]"></div>
        <div class="text-sm" v-for="n in columns">
          <a-popover title="Actions">
            <template #content>
              <div class="flex gap-x-3">
                <a-button type="primary" @click="handleChangeColumType(n - 1, CellType.SeatNormal)">Mark as
                  chairs</a-button>
                <a-button @click="handleChangeColumType(n - 1, CellType.Aisle)">Mark as aisle</a-button>
              </div>
            </template>
            <a-button class="w-10 h-10 shadow-none p-2" type="primary">{{ n }}</a-button>
          </a-popover>
        </div>
      </div>

      <div v-for="(row, rowLabel) in gridLayout" class="flex gap-2 m-auto"
        :class="{ 'flex-row-reverse': seatDirection === 'RTL' }">
        <div class="sticky left-0 bg-white w-10 flex-shrink-0 content-center text-center">{{ rowLabel }}</div>
        <button v-for="(cell, index) in row"
          class="flex-shrink-0 border-[1px] rounded-md p-2 text-sm h-10 w-10 cursor-pointer transition-all ease-linear hover:border-blue-300"
          :class="getCellClass(cell.type)" @click="handleClickCell(index, rowLabel)"
          :disabled="cell.type === CellType.Aisle">
          {{ cell.seatLabel }}
        </button>
      </div>
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
import { Button, Popover, Select, Segmented, notification } from 'ant-design-vue';
import { generateGridObject, getRangeData, compareGridLayouts } from '../../utils/utils';
import { CellType } from '../../constant/enum';

export default {
  name: 'AuditoriumLayout',
  components: {
    Button,
    Popover,
    Select,
    Segmented
  },
  props: ['rows', 'columns', 'seatDirection', 'capacity', 'defaultGridLayout'],
  expose: ['gridLayout', 'seatCount', 'getUpdatedGridLayoutCompareToOrigin'],
  data() {
    const chairTypeOptions = [
      {
        value: CellType.SeatNormal,
        label: 'Ghế thường'
      },
      {
        value: CellType.SeatVIP,
        label: 'Ghế VIP'
      },
      {
        value: CellType.Unset,
        label: 'Trống'
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
      multiCellSelected: [],
      gridLayoutOrigin: {}
    }
  },
  computed: {
    seatCount() {
      let count = 0;
      for (const key in this.gridLayout) {
        this.gridLayout[key].forEach(cell => {
          const seatType = [CellType.SeatNormal, CellType.SeatVIP];
          if (seatType.includes(cell.type)) {
            count++;
          }
        });
      }
      return count;
    },
    getUpdatedGridLayoutCompareToOrigin() {
      const updatedCells = [];
      const addedCells = [];
      const deletedCells = [];

      const { commonGrid, commonOrigin, uniqueGrid, uniqueOrigin } = compareGridLayouts(this.gridLayoutOrigin, this.gridLayout);

      for (const row in commonGrid) {
        for (let index = 0; index < commonGrid[row].length; index++) {
          const cell = commonGrid[row][index];
          const originCell = commonOrigin[row][index];

          const seatType = [CellType.SeatNormal, CellType.SeatVIP, CellType.Aisle];
          if (seatType.includes(cell.type) && seatType.includes(originCell.type)
            && originCell?.id && cell.type !== originCell.type) {
            updatedCells.push({
              id: originCell.id,
              ...cell,
              y_position: row
            })
          } else if (originCell.type === CellType.Unset && seatType.includes(cell.type) && !originCell?.id) {
            addedCells.push({
              ...cell,
              y_position: row
            })
          } else if (seatType.includes(originCell.type) && cell.type === CellType.Unset && originCell?.id) {
            deletedCells.push({
              id: originCell.id,
              seatLabel: originCell.seatLabel,
            })
          }
        }
      }

      for (const key in uniqueGrid) {
        for (let index = 0; index < uniqueGrid[key].length; index++) {
          if (uniqueGrid[key][index].type !== CellType.Unset) {
            addedCells.push({
              ...uniqueGrid[key][index]
            })
          }
        }
      }

      for (const key in uniqueOrigin) {
        for (let index = 0; index < uniqueOrigin[key].length; index++) {
          if (uniqueOrigin[key][index].type !== CellType.Unset) {
            deletedCells.push({
              ...uniqueOrigin[key][index]
            })
          }
        }
      }

      return { updatedCells, addedCells, deletedCells }
    }
  },
  watch: {
    rows: {
      handler(newVal, oldVal) {
        this.gridLayout = generateGridObject(newVal, this.columns);
      },
      immediate: true
    },
    columns: {
      handler(newVal, oldVal) {
        this.gridLayout = generateGridObject(this.rows, newVal);
      },
      immediate: true
    },
    defaultGridLayout: {
      handler(newVal, oldVal) {
        if (newVal) {
          for (const key in this.gridLayout) {
            for (let index = 0; index < this.gridLayout[key].length; index++) {
              const existCell = newVal[key].find((cell) => cell.x_position === index);
              if (existCell) {
                this.gridLayout[key][index] = {
                  id: existCell.id,
                  seatLabel: existCell.label,
                  type: existCell.seat_type
                }
              }
            }
          }

          this.gridLayoutOrigin = JSON.parse(JSON.stringify(this.gridLayout));
        }
      },
      immediate: true
    }
  },
  methods: {
    handleChangeColumType(index, cellType) {
      for (const row in this.gridLayout) {
        const shouldUpdateCellLabel = this.needUpdateCellLabel(index, row, cellType)

        this.handleChangeCellType(index, row, cellType, true);
        if (shouldUpdateCellLabel) {
          this.handleMarkLabelForCell(row);
        }
      }
    },
    handleClickCell(positionX, positionY) {
      if (this.selectMode == 'Single') {
        const currCell = this.gridLayout[positionY][positionX];

        if (this.seatCount + 1 > this.capacity && currCell.type === CellType.Unset) {
          notification['warning']({
            message: 'Exceed Capacity',
            description: 'You can not add new seat because it is full of capacity'
          })
          return;
        }

        const shouldUpdateCellLabel = this.needUpdateCellLabel(positionX, positionY, this.chairType);
        this.handleChangeCellType(positionX, positionY, this.chairType, true);
        if (shouldUpdateCellLabel) {
          this.handleMarkLabelForCell(positionY);
        }
      } else {
        if (this.multiCellSelected.length < 2) {
          const curCell = this.gridLayout[positionY][positionX];
          this.multiCellSelected.push({
            x: positionX,
            y: positionY,
            originType: curCell.type
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
      const numOfNewSeats = this.countNewSeatWillBeAdd();
      if (this.seatCount + numOfNewSeats > this.capacity && this.chairType !== CellType.Unset) {
        notification['warning']({
          message: 'Exceed Capacity',
          description: 'You can not add new seat because it is full of capacity'
        })
        this.multiCellSelected.forEach((cell) => this.handleChangeCellType(cell.x, cell.y, cell.originType))
        return;
      }

      const { xEnd, xStart, yEnd, yStart } = getRangeData(this.multiCellSelected);
      const rowsShouldUpdateCellLabel = [];

      for (const key in this.gridLayout) {
        if (key.charCodeAt(0) >= yStart.charCodeAt(0) && key.charCodeAt(0) <= yEnd.charCodeAt(0)) {
          let shouldUpdateLabelRow = false;

          for (let i = 0; i < this.gridLayout[key].length; i++) {
            if (i >= xStart && i <= xEnd) {
              if (this.gridLayout[key][i].type !== CellType.Aisle) {
                if (!shouldUpdateLabelRow) {
                  shouldUpdateLabelRow = this.needUpdateCellLabel(i, key, this.chairType);
                }

                this.handleChangeCellType(i, key, this.chairType);
              }
            }
          }

          if (shouldUpdateLabelRow) {
            rowsShouldUpdateCellLabel.push(key);
          }
        }
      }

      rowsShouldUpdateCellLabel.forEach((row) => this.handleMarkLabelForCell(row));
    },
    handleChangeCellType(positionX, positionY, cellType, toggle) {
      if (this.gridLayout[positionY][positionX].type === cellType && toggle) {
        this.gridLayout[positionY][positionX].type = CellType.Unset;
      } else {
        this.gridLayout[positionY][positionX].type = cellType;
      }
    },
    handleMarkLabelForCell(row) {
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
      const currCellType = this.gridLayout[positionY][positionX].type;

      const aisleUnset = [CellType.Aisle, CellType.Unset, CellType.MultiSelect];
      const seatTypes = [CellType.SeatNormal, CellType.SeatVIP];

      if (
        (aisleUnset.includes(currCellType) && seatTypes.includes(type)) ||
        (seatTypes.includes(currCellType) && aisleUnset.includes(type)) ||
        currCellType === type
      ) {
        return true
      }

      return false;
    },
    countNewSeatWillBeAdd() {
      const { xEnd, xStart, yEnd, yStart } = getRangeData(this.multiCellSelected);
      let count = 0;
      for (const key in this.gridLayout) {
        if (key.charCodeAt(0) >= yStart.charCodeAt(0) && key.charCodeAt(0) <= yEnd.charCodeAt(0)) {
          for (let i = 0; i < this.gridLayout[key].length; i++) {
            if (i >= xStart && i <= xEnd) {
              if (this.gridLayout[key][i].type === CellType.Unset
                || (this.gridLayout[key][i].type === CellType.MultiSelect && !this.gridLayout[key][i].seatLabel)
              ) {
                count++;
              }
            }
          }
        }
      }

      return count;
    },
    getCellClass(cellType) {
      return {
        'bg-purple-200 text-purple-500 font-medium': cellType === CellType.SeatNormal,
        'bg-pink-200 text-pink-500 font-medium': cellType === CellType.SeatVIP,
        'bg-slate-300 cursor-default': cellType === CellType.Aisle,
        'border-blue-400 shadow-md bg-white': cellType === CellType.MultiSelect
      };
    },
  },
}
</script>