<script setup>
import { useLayouts } from '@layouts'
import { config } from '@layouts/config'
import { can } from '@layouts/plugins/casl'
import {
  getComputedNavLinkToProp,
  isNavLinkActive,
} from '@layouts/utils'

const props = defineProps({
  item: {
    type: null,
    required: true,
  },
  isSubItem: {
    type: Boolean,
    required: false,
    default: false,
  },
})

const { dynamicI18nProps } = useLayouts()
</script>

<template>
  <li
    v-if="can(item.action, item.subject)"
    class="nav-link"
    :class="[{
      'sub-item': props.isSubItem,
      'disabled': item.disable,
    }]"
  >
    <Component
      :is="item.to ? 'RouterLink' : 'a'"
      v-bind="getComputedNavLinkToProp(item)"
      :class="{ 'router-link-active router-link-exact-active': isNavLinkActive(item, $router) }"
    >
      <Component
        :is="config.app.iconRenderer || 'div'"
        class="nav-item-icon"
        v-bind="item.icon || config.verticalNav.defaultNavItemIconProps"
      />
      <Component
        :is="config.app.enableI18n ? 'i18n-t' : 'span'"
        class="nav-item-title"
        v-bind="dynamicI18nProps(item.title, 'span')"
      >
        {{ item.title }}
      </Component>
    </Component>
  </li>
</template>

<style lang="scss">
.layout-horizontal-nav {
  .nav-link a {
    display: flex;
    align-items: center;
  }
}
</style>
