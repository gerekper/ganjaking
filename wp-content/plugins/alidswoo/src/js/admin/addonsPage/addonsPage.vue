<template>
    <div class="ads-page-wrap">
        <div class="">
            <div class="tabs-addons">
                <div class="tab-addon" :class="{active: active === 'all'}" @click="active = 'all'">All</div>
                <div class="tab-addon" v-for="item in addons" :class="{active: active === item.name}"
                     @click="active = item.name">{{item.name}}
                </div>
            </div>

            <div v-for="item in addonsActive">
                <div class="addon-head">{{item.name}}</div>
                <div class="addons-list">
                    <div class="addon" v-for="addon in item.list" >
                        <div class="addon-main">
                            <div class="addon-image"><img :src="addon.image"
                                                          alt=""></div>
                            <div class="meta">
                                <div class="content">
                                    <div class="title">{{addon.name}}</div>
                                    <div class="description" v-html="addon.description"/>
                                    <div class="price">
                                        <span class="sale" v-if="addon.price.price">${{addon.price.price}}</span>
                                        <span v-if="addon.price.sale">US ${{addon.price.sale}}</span>
                                        <span v-else class="price-free">{{lan.free}}</span>
                                    </div>
                                </div>
                                <div class="action">
                                    <a target="_blank" :href="addon.byLink" class="by" v-if="!addon.installed">
                                        <span v-if="addon.price.sale">Buy Now</span>
                                        <span v-else>Get Free</span></a>
                                    <div class="installed" v-else>Installed</div>
                                    <a target="_blank" :href="addon.moreLink" class="more">More details</a>
                                </div>
                            </div>
                        </div>
                        <div class="addon-footer">
                            <a target="_blank" :href="addon.reviewsLink" class="star">
                                <i class="icon-star" v-for="star in addon.star"/><span class="count">({{addon.reviews}})</span></a>
                            <div class="icons"><span class="name">Supported platforms</span>
                                <i class="icon-alids"/>
                                <i v-if="addon.woo_support" class="icon-woo"/>
                                <i class="icon-wp"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "addonsPage",
        data() {
            return {
                active: 'all',
                addons: [],
                lan: {}
            }
        },
        created() {
            this.$http.post(window.ajaxurl, {
                action: 'adsw_addons_info',
            }).then((response) => {
                this.addons = response.body.addons;
                this.lan = response.body.lan;
            });
        },
        computed: {
            addonsActive() {
                return this.active === 'all' ? this.addons : this.addons.filter((i) => i.name === this.active)
            }
        }
    }
</script>

<style scoped lang="scss">
    .ads-page-wrap {
        .ads-page-head {
            font-size: 23px;
            line-height: 31px;
            margin-bottom: 15px;
        }
    }

    .tabs-addons {
        display: flex;
        background: #FFFFFF;
        border: 1px solid #CCD0D4;
        box-sizing: border-box;
        align-self: center;
        padding: 0 5px;
        margin-bottom: 20px;
        transition: outline 0.6s linear;

        .tab-addon {
            cursor: pointer;
            font-size: 13px;
            line-height: 20px;
            color: #666666;
            padding: 15px 0 11px 0;
            margin: 0 12px 0 12px;
            border-bottom: 4px solid #FFF;

            &:hover,
            &.active {
                color: #23282D;
                border-bottom: 4px solid #666666;
            }
        }
    }

    .addons-list {
        margin: -9px;
    }

    .addon-head {
        font-weight: 600;
        font-size: 17px;
        line-height: 23px;
        color: #23282D;
        margin: 20px 0;
    }

    .addon {
        background: #FFFFFF;
        border: 1px solid #DDDDDD;
        width: 559px;
        margin: 9px;
        display: inline-block;
        transition: .4s;
    }

    .addon-main {
        display: flex;

        .addon-image {
            width: 128px;
            height: 128px;
            margin: 20px;

            img {
                max-width: 100%;
            }
        }

        .meta {
            margin: 20px 20px 0 0;
            display: flex;
            flex: 1;

            .content {
                padding-right: 20px;
            }

            .title {
                color: #0073AF;
                font-weight: 600;
                font-size: 18px;
                line-height: 26px;
            }

            .description {
                font-weight: normal;
                font-size: 18px;
                line-height: 26px;
                color: #32373C;
                margin: 10px 0;
            }

            .price {
                font-size: 18px;
                line-height: 26px;
                text-transform: uppercase;
                color: #32373C;

                .sale {
                    font-size: 14px;
                    line-height: 20px;
                    text-decoration-line: line-through;
                    color: #555D66;
                }
            }

            .price-free{
                text-transform: uppercase;
            }

            .action {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-end;

                .by {
                    text-decoration: none;
                    cursor: pointer;
                    background: #F3F5F6;
                    border: 1px solid #0071A1;
                    box-sizing: border-box;
                    border-radius: 3px;
                    font-size: 13px;
                    line-height: 17px;
                    text-align: center;
                    color: #0071A1;
                    padding: 5px 10px;
                    width: 72px;
                    white-space: nowrap;
                }

                .installed {
                    font-size: 13px;
                    line-height: 17px;
                    text-align: center;
                    background: #F3F5F6;
                    border-radius: 3px;
                    color: #999999;
                    padding: 5px 10px;
                    width: 72px;
                }

                .more {
                    cursor: pointer;
                    text-decoration: none;
                    margin-top: 11px;
                    font-size: 14px;
                    line-height: 19px;
                    text-align: center;
                    color: #0071A1;
                    white-space: nowrap;

                }
            }

        }


    }

    .addon-footer {
        display: flex;
        padding: 12px 20px;
        font-size: 13px;
        line-height: 20px;
        color: #444444;
        background: #FAFAFA;
        border-top: 1px solid #DDDDDD;
        justify-content: space-between;
        align-items: center;

        .star {
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;

            .count {
                color: #444444;
            }
        }
    }

    .icon-star {
        width: 18px;
        height: 17px;
        display: inline-block;
        background: url("star.svg") center no-repeat;
        margin-right: 2px;
    }

    .icon-woo {
        width: 25px;
        height: 15px;
        display: inline-block;
        background: url("woo.svg") center no-repeat;
        margin-right: 8px;
    }

    .icon-alids {
        width: 25px;
        height: 15px;
        display: inline-block;
        background: url("alids.svg") center no-repeat;
        margin-right: 8px;
    }

    .icon-wp {
        width: 20px;
        height: 20px;
        display: inline-block;
        background: url("wp.svg") center no-repeat;
    }

    .icons {
        display: flex;
        align-items: center;

        .name {
            font-size: 14px;
            line-height: 20px;
            color: #444444;
            margin-right: 8px;
        }
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */ {
        opacity: 0;
    }

    .item-move {
        /* applied to the element when moving */
        transition: transform .5s cubic-bezier(.55,0,.1,1);
    }

</style>
