class TrackCainiao {

    static statusMap = [
        [
            {statusDesc: "Picked", status: "PICKEDUP", className: ""},
            {statusDesc: "In transit", status: "SHIPPING", className: ""},
            {statusDesc: "Origin leaving", status: "DEPART_FROM_ORIGINAL_COUNTRY", className: ""},
            {statusDesc: "Destination arrived", status: "ARRIVED_AT_DEST_COUNTRY", className: ""},
            {statusDesc: "Delivered", status: "SIGNIN", className: ""}
        ],

        {statusDesc: "Not found", status: "ORDER_NOT_EXISTS",},
        {statusDesc: "Unsuccessful delivery attempt", status: "SIGNIN_EXC",},
        {statusDesc: "Parcel returned", status: "RETURN",},
        //{statusDesc: "Shipping over time", status: "SHIPPING_OVER_TIME", }
        {statusDesc: "Unsuccessful air delivery", status: "DEPART_FROM_ORIGINAL_COUNTRY_EXC",},
        {statusDesc: "Unsuccessful clearence", status: "ARRIVED_AT_DEST_COUNTRY_EXC",},
        {statusDesc: 'Not Found', status: "NOT_LAZADA_ORDER",},

        // OWS LIGHT map
        {statusDesc: "Order received by warehouse", status: "OWS_WHCACCEPT",},
        {statusDesc: "Left warehouse", status: "OWS_WHCOUTBOUND",},
        {statusDesc: "Shipment dispatched", status: "OWS_CPACCEPT",},
        {statusDesc: "In delivery", status: "OWS_DELIVERING",},
        {statusDesc: "Waiting self-take", status: "OWS_WAIT4SIGNIN",},
        {statusDesc: "Delivery failed", status: "OWS_DELIVER_FAIL",},
        {statusDesc: "Delivered", status: "OWS_SIGNIN",},
        // LTL LIGHT map
        {statusDesc: "Seller shipped", status: "LTL_CONSIGN",},
        {statusDesc: "In transit", status: "LTL_SHIPPING",},
        {statusDesc: "In delivery", status: "LTL_DELIVERING",},
        {statusDesc: "Waiting self-take", status: "LTL_WAIT4SIGNIN",},
        {statusDesc: "Delivery failed", status: "LTL_DELIVER_FAIL",},
        {statusDesc: "Delivered", status: "LTL_SIGNIN",},
        // CWS LIGHT map
        {statusDesc: "Order received by warehouse", status: "CWS_WHCACCEPT",},
        {statusDesc: "Departed warehouse", status: "CWS_OUTBOUND",},
        {statusDesc: "Origin leaving", status: "CWS_DEPART_FROM_ORIGINAL_COUNTRY",},
        {statusDesc: "Destination arrived", status: "CWS_ARRIVED_AT_DEST_COUNTRY",},
        {statusDesc: "Unsuccessful air delivery", status: "CWS_DEPART_FROM_ORIGINAL_COUNTRY_EXC",},
        {statusDesc: "Unsuccessful clearence", status: "CWS_ARRIVED_AT_DEST_COUNTRY_EXC",},
        {statusDesc: "Waiting for  picking", status: "CWS_WAIT4SIGNIN",},
        {statusDesc: "Unsuccessful delivery attempt", status: "CWS_SIGNIN_EXC",},
        {statusDesc: "Delivered", status: "CWS_SIGNIN",},
        // RETURN map
        {statusDesc: "Return", status: "RETURNED_STAGE_START",},
        {statusDesc: "Return", status: "RETURNED_STAGE_MIDDLE",},
        {statusDesc: "Return", status: "RETURNED_STAGE_END",},
        // DESTORY map
        {statusDesc: "Parcel is destroyed", status: "RDESTORYED_STAGE_START",},
        {statusDesc: "Parcel is destroyed", status: "RDESTORYED_STAGE_MIDDLE",},
        {statusDesc: "Parcel is destroyed", status: "RDESTORYED_STAGE_END",}

    ];

    getParams = (status, bizType) => {

        let value = {
            "LTL_CONSIGN":
                [["Seller shipped"], ["In transit", "In delivery", "Delivered"]],
            "LTL_SHIPPING":
                [["Seller shipped", "In transit"], ["In delivery", "Delivered"]],
            "LTL_DELIVERING":
                [["Seller shipped", "In transit", "In delivery"], ["Delivered"]],
            "LTL_SIGNIN":
                [["Seller shipped", "In transit", "In delivery", "Delivered"], []],
            "OWS_WHCACCEPT":
                [["Order received by warehouseOrder received by warehouse"], ["Origin leaving", "Destination arrived", "Delivered"]],
            "OWS_WHCOUTBOUND":
                [["Left warehouse"], ["Shipment dispatched", "In delivery", "Delivered"]],
            "OWS_CPACCEPT":
                [["Order received by warehouse", "Shipment dispatched"], ["In delivery", "Delivered"]],
            "OWS_DELIVERING":
                [["Order received by warehouse", "Shipment dispatched", "In delivery"], ["Delivered"]],
            "OWS_SIGNIN":
                [["Left warehouse", "Shipment dispatched", "In delivery", "Delivered"], []],
            "CWS_OUTBOUND":
                [["Departed warehouse"], ["Origin leaving", "Destination arrived", "Delivered"]],
            "CWS_WHCACCEPT":
                [["Order received by warehouseOrder received by warehouse"], ["Origin leaving", "Destination arrived", "Delivered"]],
            "CWS_DEPART_FROM_ORIGINAL_COUNTRY":
                [["Order received by warehouseOrder received by warehouse", "Origin leaving"], ["Destination arrived", "Delivered"]],
            "CWS_ARRIVED_AT_DEST_COUNTRY":
                [["Order received by warehouseOrder received by warehouse", "Origin leaving", "Destination arrived"], ["Delivered"]],
            "CWS_SIGNIN":
                [["Departed warehouse", "Origin leaving", "Destination arrived", "Delivered"], []],
            "PICKEDUP":
                [["Picked"], ["Origin leaving", "Destination arrived", "Delivered"]],
            "SHIPPING":
                [["Picked", "In transit"], ["Origin leaving", "Destination arrived", "Delivered"]],
            "DEPART_FROM_ORIGINAL_COUNTRY":
                [["Picked", "Origin leaving"], ["Destination arrived", "Delivered"]],
            "ARRIVED_AT_DEST_COUNTRY":
                [["Picked", "Origin leaving", "Destination arrived"], ["Delivered"]],
            "SIGNIN":
                [["Picked", "Origin leaving", "Destination arrived", "Delivered"], []],
            "WAIT4SIGNIN":
                [["Picked", "Origin leaving", "Destination arrived", "Waiting for  picking"], ["Delivered"]],
            "OWS_WAIT4SIGNIN":
                [["Order received by warehouse", "Shipment dispatched", "In delivery", "Waiting self-take"], ["Delivered"]],
            "CWS_WAIT4SIGNIN":
                [["Order received by warehouseOrder received by warehouse", "Origin leaving", "Destination arrived", "Waiting self-take"], ["Delivered"]],
            "LTL_WAIT4SIGNIN":
                [["Seller shipped", "In transit", "In delivery", "Waiting for  picking"], ["Delivered"]],
            "SIGNIN_EXC":
                [["Picked", "Origin leaving", "Destination arrived", ""], ["Delivered"]],
            "OWS_SIGNIN_EXC":
                [["Order received by warehouse", "Shipment dispatched", "In delivery", "Delivery failed"], ["Delivered"]],
            "CWS_SIGNIN_EXC":
                [["Order received by warehouseOrder received by warehouse", "Origin leaving", "Destination arrived", "Unsuccessful delivery attempt"], ["Delivered"]],
            "LTL_SIGNIN_EXC":
                [["Seller shipped", "In transit", "In delivery", "Delivery failed"], ["Delivered"]],
            "ARRIVED_AT_DEST_COUNTRY_EXC":
                [["Picked", "Origin leaving"], ["Destination arrived", "Delivered"]],
            "DEPART_FROM_ORIGINAL_COUNTRY_EXC":
                [["Picked"], ["Origin leaving", "Destination arrived", "Delivered"]]
        };


        if ("p2p" === bizType) {
            value['RDESTORYED_STAGE_START'] = [
                [
                    "Picked",
                    "Destroyed",
                ],
                [
                    "Origin leaving",
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        } else if ("cws" === bizType) {
            value['RDESTORYED_STAGE_START'] = [
                [
                    "Order received by warehouse",
                    "Destroyed",
                ],
                [
                    "Origin leaving",
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        }


        if ("p2p" === bizType) {
            value['RDESTORYED_STAGE_MIDDLE'] = [
                [
                    "Picked",
                    "Origin leaving",
                    "Destroyed",
                ],
                [
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        } else if ("cws" === bizType) {
            value['RDESTORYED_STAGE_MIDDLE'] = [
                [

                    "Order received by warehouse",
                    "Origin leaving",
                    "Destroyed",
                ],
                [
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        }


        if ("p2p" === bizType) {
            value['RDESTORYED_STAGE_END'] = [
                [
                    "Picked",
                    "Origin leaving",
                    "Destination arrived",
                    "Destroyed",
                ],
                [
                    "Delivered",
                ]
            ]
        } else if ("cws" === bizType) {
            value['RDESTORYED_STAGE_END'] = [
                [
                    "Order received by warehouse",
                    "Origin leaving",
                    "Destination arrived",
                    "Destroyed",
                ],
                [
                    "Delivered",
                ]
            ]
        }


        if ("p2p" === bizType) {
            value['RETURNED_STAGE_START'] = [
                [
                    "Picked",

                ],
                [
                    "Origin leaving",
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        } else if ("cws" === bizType) {
            value['RETURNED_STAGE_START'] = [
                [
                    "Order received by warehouse",
                    "Parcel returned",
                ],
                [
                    "Origin leaving",
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        } else if ("ltl" === bizType) {
            value['RETURNED_STAGE_START'] = [
                [
                    "Seller shipped",
                    "Parcel returned",
                ],
                [
                    "In transit",
                    "In delivery",
                    "Delivered",
                ]
            ]
        } else if ("ows" === bizType) {
            value['RETURNED_STAGE_START'] = [
                [
                    "Order received by warehouse",
                    "Parcel returned",
                ],
                [
                    "Shipment dispatched",
                    "In delivery",
                    "Delivered",
                ]
            ]
        }


        if ("p2p" === bizType) {
            value['RETURNED_STAGE_MIDDLE'] = [
                [
                    "Picked",
                    "Origin leaving",
                    "Parcel returned",

                ],
                [
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        } else if ("cws" === bizType) {
            value['RETURNED_STAGE_MIDDLE'] = [
                [

                    "Order received by warehouse",
                    "Origin leaving",
                    "Parcel returned",
                ],
                [
                    "Destination arrived",
                    "Delivered",
                ]
            ]
        } else if ("ltl" === bizType) {
            value['RETURNED_STAGE_MIDDLE'] = [
                [
                    "Seller shipped",
                    "In transit",
                    "Parcel returned",
                ],
                [
                    "In delivery",
                    "Delivered",
                ]
            ]
        } else if ("ows" === bizType) {
            value['RETURNED_STAGE_START'] = [
                [
                    "Order received by warehouse",
                    "Shipment dispatched",
                    "Parcel returned",
                ],
                [
                    "In delivery",
                    "Delivered",
                ]
            ]
        }


        if ("p2p" === bizType) {
            value['RETURNED_STAGE_END'] = [
                [
                    "Picked",
                    "Origin leaving",
                    "Destination arrived",
                    "Parcel returned",
                ],
                [
                    "Delivered",
                ]
            ]
        } else if ("cws" === bizType) {
            value['RETURNED_STAGE_END'] = [
                [
                    "Order received by warehouse",
                    "Origin leaving",
                    "Destination arrived",
                    "Parcel returned",
                ],
                [
                    "Delivered",
                ]
            ]
        } else if ("ltl" === bizType) {
            value['RETURNED_STAGE_END'] = [
                [
                    "Seller shipped",
                    "In transit",
                    "In delivery",
                    "Parcel returned",
                ],
                [
                    "Delivered",
                ]
            ]
        } else if ("ows" === bizType) {
            value['RETURNED_STAGE_END'] = [
                [

                    "Order received by warehouse",
                    "Shipment dispatched",
                    "In delivery",
                    "Parcel returned",
                ],
                [
                    "Delivered",
                ]
            ]
        }

        return value[status];

    };

    constructor(params) {
        this.params = params;
    }

    getStatus(status) {
        let text = TrackCainiao.statusMap.find((i) => {
            return status === i.status
        });

        if (!text) {
            return status;
        }

        return text.statusDesc;
    }


    parse() {

        if (!this.params.success) {
            return {
                status: [],
                list: []
            }
        }

        let data = this.params.data[0];

        let statusLast = data.status;
        let statusDescLast = data.statusDesc;
        let bizType = data.bizType;

        let status = [];

        let list = data.section2.detailList.map((i) => {

            let nameStatus = this.getStatus(i.status);

            if (i.status) {
                status.push({
                    key: i.status,
                    text: nameStatus
                })
            }

            return {
                text: i.desc,
                time: i.time.split(' '),//"2019-06-22 16:45:51",
            }
        });

        return {
            params: this.getParams(statusLast, bizType),
            status: status.reverse(),
            list: list
        }
    }
}

export default TrackCainiao;