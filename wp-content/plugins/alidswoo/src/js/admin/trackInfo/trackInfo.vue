<template>
    <div v-if="open" class="track-modal"  @click="close">
        <div class="track-modal-content">
            <div class="header-modal">
                <div class="title">Tracking information</div>

                <div class="close"></div>
            </div>

            <template v-if="loader">
                <div class=""><div class="loading"><loading/></div></div>
            </template>
            <template v-else>

                <div class="tracking-info">
                    <div class="tracking"><span class="name">Tracking ID:</span> <a target="_blank" :href="`https://global.cainiao.com/detail.htm?mailNoList=${trackNum}`">{{trackNum}}</a></div>
                    <div class="order"><a target="_blank" :href="`https://trade.aliexpress.com/order_detail.htm?orderId=${orderNum}`">Order #{{orderNum}}</a></div>
                </div>

                <steps :points="points"/>

                <div class="wrap-list-pointer">
                    <ul class="list-pointer">
                        <li v-for="(item, i) in list">
                            <div class="box-time">
                                <div class="data">{{item.time[0]}}</div>
                                <div class="time">{{item.time[1]}}</div>
                            </div>
                            <div class="box-text">
                                <i class="icon" :class="{active : i === 0}"></i>
                                <div class="text">{{item.text}}</div>
                            </div>
                        </li>
                    </ul>
                </div>

            </template>
        </div>
    </div>
</template>

<script>



    import track from "./trackCainiao";

    import loading from "./loading";
    import steps from "./steps";

    function b64DecodeUnicode( str ) {
        return str ? window.Base64.decode( str ) : false;
    }

    function htmlToObj( html ) {
        var div = jQuery( '<div></div>' );
        return jQuery( div ).append( html );
    }

    let cash = {

    };

    export default {
        name: "trackInfo",
        components: {steps, loading},
        data(){
            return {
                list : [],
                points : [],
                url: '',
                open : false,
                loader : false,
                trackNum : null,
                orderNum : null,
            }
        },
        created() {

            let self = this;

            jQuery('body').on('click', '.js-track', function (e) {
                e.preventDefault();
                self.trackNum = jQuery(this).attr('data-tip');

                if(!self.trackNum){
                    return true;
                }

                self.orderNum = jQuery(this).closest('.table-item').attr('data-order_number');
                console.log("SELF:",self);
                if(!self.orderNum ){
                    self.orderNum = jQuery(this).closest('.wc-order-item-variation').find('.adsw_order_number').attr('data-order_number');
                    console.log("SELF:",self);
                }
                self.trankInfo(self.trackNum);
            });

            jQuery('body').append('<iframe id="iframediv" :src="https://t.17track.net/" width="100%" height="500" style="position: absolute;left: -99999px;"></iframe>');

            window.addEventListener( "message", ( event ) => {
                //console.log( 'addEventListener' );
                if ( event.source !== document.getElementById( "iframediv" ).contentWindow ) {
                    //console.log( 'НЕ ПРИНИМАЮ message' );
                    return;
                }

                //console.log( 'ПРИНИМАЮ message' );
                let request = event.data;
                if ( request.source && request.source === 'IFRAME_TO_PARENT' ) {
                    //console.log( 'NAME_SOURCE_BG_FROM_IFRAME' );
                    //console.log( request.action );
                    //console.log( request.info );
                    if(request.action === 'html'){
                        self.loader = false;
                        let html = b64DecodeUnicode( event.data.info.html );
                        this.parseParams(htmlToObj( html ));
                    }

                    if(request.action === 'init'){
                        document.getElementById( "iframediv" ).contentWindow.postMessage( {
                            source : 'PARENT_TO_IFRAME',
                            action : 'html',
                            info   : {
                                checkDom : '#waybill_list_val_box'
                            }
                        }, "*" );
                    }

                }

            }, false );

        },
        methods:{

            close(){
                this.open = false;
            },

            parseParams(obj){
              let waybill_list_val_box  = obj.find('#waybill_list_val_box').val();
              let trackingInfoList = jQuery.parseJSON(waybill_list_val_box);

             let params = new track(trackingInfoList).parse();

                cash[this.trackNum] = {
                    points : params.params,
                    list : params.list
                }

                this.points = params.params;
                this.list = params.list;
            },

            trankInfo(num){

                this.open = true;
                this.loader = true;
                if(typeof cash[num] !== "undefined"){
                    this.points = cash[num]['points'];
                    this.list = cash[num]['list'];
                    this.loader = false;
                    return;
                }

                jQuery('#iframediv').attr('src', `https://global.cainiao.com/detail.htm?mailNoList=${num.trim()}`);
            },
        }
    }
</script>

<style scoped lang="scss">
    .track-modal{
        height: 100%;
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        border-bottom: 0;
        background: rgba(0, 0, 0, 0.3);
        z-index: 99999999999;
        display: flex;
        justify-content: center;
        align-items: center;
        .track-modal-content{
            position: relative;
            background: #FFFFFF;
            box-shadow: 0px 4px 40px rgba(0, 0, 0, 0.25);
            border-radius: 3px;
            display: block;
            width: 900px;
            height: 600px;
            margin: auto;
        }

        .header-modal{
            position: relative;
            padding: 20px;
            .title{
                font-size: 18px;
                line-height: 21px;
                color: #212B36;
            }
        }
        .close{
            display: block;
            position: absolute;
            right: 20px;
            top: 20px;
            width: 20px;
            height: 20px;
            background: url(./cancel.svg) no-repeat;
        }
    }
    .wrap-list-pointer{
        padding: 10px 30px;
        overflow: auto;
        height: 380px;
    }
    .list-pointer{
        position: relative;
        color: #212B36;
        font-size: 14px;
        margin: 0;
        padding: 0;
        li{
            display: flex;
            margin: 0;
            padding: 0;
        }
        .box-time{
            width: 120px;
            padding-right: 30px;
            text-align: right;
            position: relative;
            top: -5px;
            .time{
                color: #919EAB;
                font-size: 12px;
            }
        }
        .box-text{
            position: relative;
            flex: 1;
            padding-left: 30px;
            border-left: 1px solid #DADADA;
            height: 50px;
            i{
                position: absolute;
                left: -5px;
                top: 0;
                border-radius: 50%;
                width: 10px;
                height: 10px;
                background: #DADADA;
            }

            i.active{
                position: absolute;
                left: -15px;
                top: -5px;
                border-radius: 50%;
                width: 27px;
                height: 27px;
                background: url('./icon-last.svg') no-repeat 7px 7px, #1daeea;
            }
        }

        li:last-child{
            .box-text{
                border-left: 1px solid #fff;
                .text{
                    position: relative;
                    top: -5px;
                }
            }
        }

    }

    .tracking-info{
        border-top: 1px solid #DADADA;
        display: flex;
        padding: 20px;
        justify-content: space-between;
        background: #F5F7F8;
        .name{
            font-weight: bold;
        }

        .order{
            a{
                color: #0073aa;
                text-decoration: none;
                &:hover{
                    color: #00a0d2;
                }
            }
        }
    }
    .loading{
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>