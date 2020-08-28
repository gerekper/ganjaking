<?php

declare (strict_types=1);
namespace SearchWP\Dependencies\AsciiTable;

use SearchWP\Dependencies\Ds\Map;
use SearchWP\Dependencies\Ds\Collection;
class Row implements \SearchWP\Dependencies\AsciiTable\RowInterface
{
    /**
     * @var Map
     */
    private $cells;
    public function __construct()
    {
        $this->cells = new \SearchWP\Dependencies\Ds\Map();
    }
    /**
     * {@inheritdoc}
     */
    public function addCell(\SearchWP\Dependencies\AsciiTable\CellInterface $cell)
    {
        $this->cells->put($cell->getColumnName(), $cell);
    }
    /**
     * {@inheritdoc}
     */
    public function addCells(\SearchWP\Dependencies\AsciiTable\CellInterface ...$cells)
    {
        foreach ($cells as $cell) {
            $this->addCell($cell);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getCell($columnName) : \SearchWP\Dependencies\AsciiTable\CellInterface
    {
        return $this->cells->get($columnName);
    }
    /**
     * {@inheritdoc}
     */
    public function hasCell($columnName) : bool
    {
        return $this->cells->hasKey($columnName);
    }
    /**
     * {@inheritdoc}
     */
    public function getCells() : \SearchWP\Dependencies\Ds\Collection
    {
        return $this->cells;
    }
}
