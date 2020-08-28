<?php

function pm_pro_invoice_get_total_due( $invoice ) {
	$paid  = pm_pro_invoice_get_total_paid( $invoice['payments']['data'] );
    $total = pm_pro_invoice_get_invoice_total( $invoice['entryTasks'], $invoice['entryNames'], $invoice['discount']);

    $dueTotal = $total - $paid;
    
    return $dueTotal;
}

function pm_pro_invoice_get_total_paid( $payments ) {
    $totalPaid = 0;

    foreach ( $payments as $key => $payment ) {
        $totalPaid = $totalPaid + $payment['amount'];
    }
    
    return $totalPaid;
}

function pm_pro_invoice_get_invoice_total( $entryTasks, $entryNames, $discount ) {
	$subTotal      = pm_pro_calculate_sub_total( $entryTasks, $entryNames );
	$totalTax      = pm_pro_calculate_total_tax($entryTasks, $entryNames);
    $totalDiscount = pm_pro_calculate_total_discount($entryTasks, $entryNames, $discount);
    
    $total = $subTotal+$totalTax-$totalDiscount;

    return $total;
}

function pm_pro_calculate_sub_total($tasks, $names) {
    $subTotal = 0;

    foreach ( $tasks as $key => $task ) {
		$amount   = !empty( $task['amount'] ) ? $task['amount'] : 0;
		$hour     = !empty( $task['hour'] ) ? $task['hour'] : 1;
		$subTotal = $subTotal + ($amount*$hour);
    }

    foreach ( $names as $key => $name ) {
		$amount   = !empty( $name['amount'] ) ? $name['amount'] : 0;
		$quantity = !empty( $name['quantity'] ) ? $name['quantity'] : 1;
		$subTotal = $subTotal + ($amount*$quantity);
    }

    return $subTotal;
}

function pm_pro_calculate_total_tax( $tasks, $names ) {
    $taxTotal = 0;

    foreach ( $tasks as $key => $task ) {
        $line_tax = pm_pro_line_tax( $task, 'task' );
        $taxTotal = $taxTotal + $line_tax;
    }

    foreach ( $names as $key => $name ) {
        $line_tax = pm_pro_line_tax( $name, 'name' );
        $taxTotal = $taxTotal + $line_tax;
    }
    
    return $taxTotal;
}

function pm_pro_line_tax( $task, $type ) {
    
    if ( $type == 'task') {
        $hour      = $task['hour'] ? $task['hour'] : 1;
        $tax       = floatval($task['tax'])/100;
        $taxAmount = floatval($task['amount'])*$hour*$tax;

        return $taxAmount;
    }

    if ( $type == 'name') {

        $quantity  = $task['quantity'] ? $task['quantity'] : 1;
        $tax       = floatval($task['tax'])/100;
        $taxAmount = floatval($task['amount'])*$quantity*$tax;

        return $taxAmount;
    }
}

function pm_pro_calculate_total_discount ( $tasks, $names, $discount ) {
    
    $discountTotal = 0;

    foreach ( $tasks as $key => $task) {
        $line_discount = pm_pro_line_discount( $task, $discount, 'task' );
        $discountTotal = $discountTotal + $line_discount;
    }

    foreach ( $names as $key => $name ) {
        $line_discount = pm_pro_line_discount( $name, $discount, 'name' );
        $discountTotal = $discountTotal + $line_discount;
    }

    return $discountTotal;
}

function pm_pro_line_discount($task, $discount, $type) {

    if ($type == 'task') {
        $hour  = $task['hour'] ? $task['hour'] : 1;
        $discount = floatval($discount)/100;
        $discountAmount = $task['amount']*$hour*$discount;

        return $discountAmount;
    }

    if ( $type == 'name') {
        $quantity  = $task['quantity'] ? $task['quantity'] : 1;
        $discount = floatval($discount)/100;
        $discountAmount = $task['amount']*$quantity*$discount;

        return $discountAmount;
    }
}

function pm_pro_task_line_total( $task ) {
    $hour  = $task['hour'] ? $task['hour'] : 0;
    $line_total = $task['amount']*$hour;

    return $line_total;
}

function pm_pro_name_line_total( $name ) {

    $quantity  = $name['quantity'] ? $name['quantity'] : 0;
    $lineTotal = $name['amount']*$quantity;

    return $lineTotal;
}

function pm_pro_get_invoice_currencey_symbol() {
    $invoice_settings = pm_get_setting( 'invoice' );
    $currency_code    = empty( $invoice_settings['currency_code'] ) ? 'USD' : $invoice_settings['currency_code'];
    $currency_symbols = require_once PM_PRO_INVOICE_PATH . 'includes/Currency_Symbols.php';
    $currency_symbol  = empty( $currency_symbols[$currency_code] ) ? 'USD' : html_entity_decode($currency_symbols[$currency_code]);
    
    return $currency_symbol;
}

