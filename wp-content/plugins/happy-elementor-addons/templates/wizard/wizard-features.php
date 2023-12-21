<div class="inner-content">
    <h2 class="title-small color-purple">Choose the features you need right now!</h2>
    <p>You can always enable/disable any features later from the dashboard.</p>

    <div class="preadvise">We all love having as many features as possible. But it might impact Elementor editor loading time. So we suggest you to disable the unused features to keep everything super optimized.</div>

    <div class="feature-container">
        <div class="feature-group">

            <div class="ha_item_feature"
                v-for="(feature,key) in featureList" :key="feature.slug">
                <fieldset>
                <legend>{{makeLabel(feature.is_pro)}}</legend>
                    <div class="feature_inner">
                        <div class="feature-title">{{feature.title}}</div>
                        <div class="ha-dashboard-features__item-toggle ha-toggle">
                            <input 
                            :id="`ha-feature-${feature.slug}`" 
                            type="checkbox" 
                            :value="feature.slug" 
                            class="ha-toggle__check ha-feature" 
                            v-model="feature.is_active"
                            @click="isFeatureActive(feature.slug,feature.is_active)"
                            >
                            <b class="ha-toggle__switch"></b>
                            <b class="ha-toggle__track"></b>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
    
    <ha-nav
    prev="widgets"
    next="bepro"
    @set-tab="setTab"
    ></ha-nav>
</div>