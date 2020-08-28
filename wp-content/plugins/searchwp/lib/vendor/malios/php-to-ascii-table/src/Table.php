<?php

declare (strict_types=1);
namespace SearchWP\Dependencies\AsciiTable;

use SearchWP\Dependencies\Ds\Map;
use SearchWP\Dependencies\Ds\Set;
class Table implements \SearchWP\Dependencies\AsciiTable\TableInterface
{
    /**
     * @var RowInterface[]
     */
    private $rows = [];
    /**
     * @var Set
     */
    private $visibleColumns;
    /**
     * @var Set
     */
    private $allColumns;
    /**
     * @var Map
     */
    private $biggestValues;
    public function __construct()
    {
        $this->visibleColumns = new \SearchWP\Dependencies\Ds\Set();
        $this->allColumns = new \SearchWP\Dependencies\Ds\Set();
        $this->biggestValues = new \SearchWP\Dependencies\Ds\Map();
    }
    /**
     * {@inheritdoc}
     */
    public function addRow(\SearchWP\Dependencies\AsciiTable\RowInterface $row)
    {
        foreach ($row->getCells() as $cell) {
            $columnName = $cell->getColumnName();
            $this->allColumns->add($columnName);
            $width = $cell->getWidth();
            if ($this->biggestValues->hasKey($columnName)) {
                if ($width > $this->biggestValues->get($columnName)) {
                    $this->biggestValues->put($columnName, $width);
                }
            } else {
                $this->biggestValues->put($columnName, $width);
            }
        }
        \array_push($this->rows, $row);
    }
    /**
     * {@inheritdoc}
     */
    public function getRows() : array
    {
        return $this->rows;
    }
    /**
     * {@inheritdoc}
     */
    public function isEmpty() : bool
    {
        return empty($this->rows);
    }
    /**
     * {@inheritdoc}
     */
    public function setVisibleColumns(array $columnNames)
    {
        $this->visibleColumns->clear();
        $this->visibleColumns->allocate(\count($columnNames));
        $this->visibleColumns->add(...$columnNames);
    }
    /**
     * {@inheritdoc}
     */
    public function getVisibleColumns() : \SearchWP\Dependencies\Ds\Set
    {
        if ($this->visibleColumns->isEmpty()) {
            return $this->getAllColumns();
        }
        return $this->visibleColumns;
    }
    /**
     * {@inheritdoc}
     */
    public function getAllColumns() : \SearchWP\Dependencies\Ds\Set
    {
        return $this->allColumns;
    }
    /**
     * {@inheritdoc}
     */
    public function getColumnWidth(string $columnName) : int
    {
        $width = 0;
        if ($this->biggestValues->hasKey($columnName)) {
            $width = $this->biggestValues->get($columnName);
        }
        $visibleColumns = $this->getVisibleColumns();
        if ($visibleColumns->contains($columnName) && \mb_strwidth($columnName) > $width) {
            $width = \mb_strwidth($columnName);
        }
        return $width;
    }
}
