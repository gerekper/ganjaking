<template>
    <div v-if="open" class="product-export-modal"  >
        <div class="over" @click="close"></div>
        <div class="modal-content">
            <div class="header-modal">
                <div class="title">Export products</div>

                <div class="close" @click="close"></div>
            </div>
            <div class="product-export-option">
                <div class="checkbox " v-for="i in list"><label>
                    <div class="checker"><input v-model="selected" :id="i.value" :value="i.value" type="checkbox"><span/></div>{{i.name}}</label></div>
              </div>
            <div class="foot">
                <div class="btn btn-blue" @click="close">Cancel</div>
                <a class="btn btn-green" :href="href">Apply</a>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        name: "exportProducts",
        data(){
            return {
                href : '',
                open : false,
                selected : [
                    'title',
                    'link',
                    'price',
                    'sale_price',
                    'supplier_cost',
                    'variations',
                    'category',
                    'orders',
                    'amount',
                    'supplier',
                ],
                list : [
                    {
                        value : 'title',
                        name : 'Title'
                    },
                    {
                        value : 'link',
                        name : 'Link'
                    },
                    {
                        value : 'price',
                        name : 'Regular price'
                    },
                    {
                        value : 'sale_price',
                        name : 'Sale price'
                    },
                    {
                        value : 'supplier_cost',
                        name : 'Supplier cost'
                    },
                    {
                        value : 'variations',
                        name : 'Variations'
                    },
                    {
                        value : 'category',
                        name : 'Category'
                    },
                    {
                        value : 'orders',
                        name : 'Orders'
                    },
                    {
                        value : 'amount',
                        name : 'Amount'
                    },
                    {
                        value : 'supplier',
                        name : 'Supplier'
                    },
                ]
              }
        },
        created(){
            let self = this;
             jQuery('body').on('click', '.export-products', function (e) {
                 let href = jQuery(this).attr('data-href');

                 self._href = href;
                 self.href = self._href + '&' + self.selected.join('&');
                 self.open = true;
                 console.log(href);
             })
        },
        methods: {
            close() {
                this.open = false;
            },
        },
        watch: {
            selected(){
                this.href = this._href + '&' + this.selected.join('&');
                console.log(this.selected.join('&'));
                console.log(this.selected);
            }
        }
    }
</script>

<style scoped lang="scss">
    .product-export-modal{
        .over{
            height: 100%;
            position: fixed;
            left: 0;
            right: 0;
            top: 0;
            border-bottom: 0;
        }
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
        .modal-content{
            position: relative;
            background: #FFFFFF;
            box-shadow: 0px 4px 40px rgba(0, 0, 0, 0.25);
            border-radius: 3px;
            display: block;
            width: 300px;
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
            cursor: pointer;
            display: block;
            position: absolute;
            right: 20px;
            top: 20px;
            width: 20px;
            height: 20px;
            background: url(./cancel.svg) no-repeat;
        }

        .foot{
            display: flex;
            justify-content: flex-end;
            margin: 20px;
        }

        .btn{
            display: inline-block;
            margin-bottom: 0;
            font-weight: normal;
            text-align: center;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            background-image: none;
            border: 1px solid #0000;
            white-space: nowrap;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            border-radius: 1px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            text-decoration: none;
            margin-left: 15px;
        }

        .btn-green {
            color: #fff;
            background-color: #46ab88;
            border-color: #46ab88;
        }

        .btn i{margin-right: 5px;}
        .btn-blue{color:#fff;background-color:#1daeea;border-color:#1daeea;}
        .btn-blue:focus,.btn-blue.focus,.btn-blue:hover,.btn-blue:active,.btn-blue.active,.open>.dropdown-toggle.btn-blue,.btn-blue:active:hover,.btn-blue.active:hover,.open>.dropdown-toggle.btn-blue:hover,.btn-blue:active:focus,.btn-blue.active:focus,.open>.dropdown-toggle.btn-blue:focus,.btn-blue:active.focus,.btn-blue.active.focus,.open>.dropdown-toggle.btn-blue.focus{color:#fff;background-color:#3ac1f9;border-color:#3ac1f9}.btn-blue:active,.btn-blue.active,.open>.dropdown-toggle.btn-blue{background-image:none}
        .btn-blue.disabled:hover,.btn-blue[disabled]:hover,fieldset[disabled] .btn-blue:hover,.btn-blue.disabled:focus,.btn-blue[disabled]:focus,fieldset[disabled] .btn-blue:focus,.btn-blue.disabled.focus,.btn-blue[disabled].focus,fieldset[disabled] .btn-blue.focus{background-color:#337ab7;border-color:#2e6da4}
        .btn-blue .badge{color:#337ab7;background-color:#fff}
        .btn-blue-transparent{color:#1daeea;background-color:transparent;border-color:#1daeea;}
        .btn-blue-transparent:focus,.btn-blue-transparent.focus,.btn-blue-transparent:hover,.btn-blue-transparent:active,
        .btn-blue-transparent.active,.open>.dropdown-toggle.btn-blue-transparent,.btn-blue-transparent:active:hover,
        .btn-blue-transparent.active:hover,.open>.dropdown-toggle.btn-blue-transparent:hover,.btn-blue-transparent:active:focus,
        .btn-blue-transparent.active:focus,.open>.dropdown-toggle.btn-blue-transparent:focus,.btn-blue-transparent:active.focus,
        .btn-blue-transparent.active.focus,.open>.dropdown-toggle.btn-blue-transparent.focus{color:#3ac1f9;background-color:transparent;border-color:#3ac1f9}
        .btn-blue-transparent:active,.btn-blue-transparent.active,.open>.dropdown-toggle.btn-blue-transparent{background-image:none}
        .btn-blue-transparent.disabled:hover,.btn-blue-transparent[disabled]:hover,fieldset[disabled] .btn-blue-transparent:hover,
        .btn-blue-transparent.disabled:focus,.btn-blue-transparent[disabled]:focus,fieldset[disabled] .btn-blue-transparent:focus,
        .btn-blue-transparent.disabled.focus,.btn-blue-transparent[disabled].focus,fieldset[disabled] .btn-blue-transparent.focus{background-color:transparent;border-color:#2e6da4}
        .btn-blue-transparent .badge{color:#fff;background-color:#337ab7}

        a.btn-green{color:#fff}
        .btn-green{color:#fff;background-color:#46ab88;border-color:#46ab88}
        .btn-green:focus,.btn-green.focus,.btn-green:hover,.btn-green:active,.btn-green.active,.open>.dropdown-toggle.btn-green,.btn-green:active:hover,.btn-green.active:hover,.open>.dropdown-toggle.btn-green:hover,.btn-green:active:focus,.btn-green.active:focus,.open>.dropdown-toggle.btn-green:focus,.btn-green:active.focus,.btn-green.active.focus,.open>.dropdown-toggle.btn-green.focus{color:#fff;background-color:#52c89f;border-color:#52c89f}
        .btn-green:active,.btn-green.active,.open>.dropdown-toggle.btn-green{background-image:none}
        .btn-green.disabled:hover,.btn-green[disabled]:hover,fieldset[disabled] .btn-green:hover,.btn-green.disabled:focus,.btn-green[disabled]:focus,fieldset[disabled] .btn-green:focus,.btn-green.disabled.focus,.btn-green[disabled].focus,fieldset[disabled] .btn-green.focus{background-color:#52c89f;border-color:#52c89f}
        .btn-green .badge{color:#46ab88;background-color:#fff}

        div.checker{
            position: relative;
            margin-right: 5px;
            input{
                opacity: 0;
            }
        }
        .checkbox {
            margin-bottom: 20px;
            label{
                display: flex;
            }
        }

        div.checker span {
            -webkit-transition: border-color ease-in-out .15s;
            -o-transition: border-color ease-in-out .15s;
            transition: border-color ease-in-out .15s;
            color: #455a64;
            border: 1px solid #607d8b;
            display: inline-block;
            text-align: center;
            border-radius: 2px;
            width: 19px;
            height: 19px;
            position: absolute;
            left: 0;
        }

        .checker span:after {
            font-family: 'FontAwesome';
            content: "\f00c";
            font-size: 13px;
            line-height: 1;
            position: absolute;
            top: 3px;
            left: 3px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            opacity: 0;
        }

        .checker input:checked + span:after {
            opacity: 1;
        }

        .product-export-option{
            margin: 20px;
        }
    }


</style>