<?php
namespace WooCommercePDFInvoice\Frame;

use IteratorAggregate;
use WooCommercePDFInvoice\Frame;

/**
 * Pre-order IteratorAggregate
 *
 * @access private
 * @package dompdf
 */
class FrameTreeList implements IteratorAggregate
{
    /**
     * @var \WooCommercePDFInvoice\Frame
     */
    protected $_root;

    /**
     * @param \WooCommercePDFInvoice\Frame $root
     */
    public function __construct(Frame $root)
    {
        $this->_root = $root;
    }

    /**
     * @return FrameTreeIterator
     */
    public function getIterator()
    {
        return new FrameTreeIterator($this->_root);
    }
}
