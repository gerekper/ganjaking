<?php

namespace SearchWP\Dependencies\AsciiTable;

use SearchWP\Dependencies\Ds\Collection;
interface RowInterface
{
    /**
     * Add single cell to the row
     *
     * @param CellInterface $cell
     */
    public function addCell(\SearchWP\Dependencies\AsciiTable\CellInterface $cell);
    /**
     * Add multiple cells to row
     *
     * @param CellInterface ...$cells
     */
    public function addCells(\SearchWP\Dependencies\AsciiTable\CellInterface ...$cells);
    /**
     * Get single cell by name
     *
     * @param $columnName
     * @return CellInterface
     */
    public function getCell($columnName) : \SearchWP\Dependencies\AsciiTable\CellInterface;
    /**
     * Check if the row has a cell cell for given column
     *
     * @param $columnName
     * @return bool
     */
    public function hasCell($columnName) : bool;
    /**
     * Get all cells
     *
     * @return Collection
     */
    public function getCells() : \SearchWP\Dependencies\Ds\Collection;
}
