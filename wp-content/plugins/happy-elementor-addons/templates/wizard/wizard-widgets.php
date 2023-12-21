<?php
// $all_widgets = self::get_real_widgets_map();
$all_widgets_cats = self::get_free_widget_map_catwise();
?>

<div class="inner-content">
    <h2 class="title-small color-purple">Choose which widgets you need</h2>
    <p>You can enable/disable them anytime from the dashboard.</p>

    <div class="widget-container" :class="{ 'list masked': this.widgetMore }">
        <div class="widget-group"
            v-for="(cat, index) in widgetList" :key="index">
            <div class="ha-widget-cat-heading">
                <div class="title">{{makeTitle(index)}}</div>
                <div class="action">
                    <span @click="allAdd(index)">Enable All</span>
                    <span @click="allRemove(index)">Disable All</span>
                </div>
            </div>

            <div class="ha_item_widget"
                v-for="(widget,key) in sortByTitle(cat)" :key="widget.slug">
                <fieldset>
                <legend>{{makeLabel(widget.is_pro)}}</legend>
                    <div class="widget_inner">
                        <div class="widget-title">{{widget.title}}</div>
                        <div class="ha-dashboard-widgets__item-toggle ha-toggle">
                            <input 
                            :id="`ha-widget-${widget.slug}`" 
                            type="checkbox" 
                            :value="widget.slug" 
                            class="ha-toggle__check ha-widget" 
                            v-model="widget.is_active"
                            @click="isActive(widget.slug,widget.is_active)"
                            >
                            <b class="ha-toggle__switch"></b>
                            <b class="ha-toggle__track"></b>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    <div class="reveal-more" v-if="this.widgetMore" @click="revealWidgetList()">View All</div>
    </div>

    <ha-nav
    prev="welcome"
    next="features"
    done=""
    @set-tab="setTab"
    ></ha-nav>
</div>