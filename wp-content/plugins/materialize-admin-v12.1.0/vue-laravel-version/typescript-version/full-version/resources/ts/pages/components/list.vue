<script setup lang="ts">
import * as demoCode from '@/views/demos/components/list/demoCodeList'
</script>

<template>
  <VRow class="match-height">
    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Basic -->
      <AppCardCode
        title="Basic"
        no-padding
        :code="demoCode.basic"
      >
        <VCardText><code>v-list</code> component can contain an avatar, content, actions and much more.</VCardText>

        <VCardText>
          <DemoListBasic />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Rounded -->
      <AppCardCode
        title="Rounded"
        no-padding
        :code="demoCode.rounded"
      >
        <VCardText>You can make <code>v-list-item</code> rounded using <code>rounded</code> prop.</VCardText>

        <VCardText>
          <DemoListRounded />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Density  -->
      <AppCardCode
        title="Density"
        :code="demoCode.density"
        no-padding
      >
        <VCardText>Use <code>density</code> prop to adjusts the spacing within the component. Available options are: <code>default</code>, <code>comfortable</code>, and <code>compact</code>.</VCardText>

        <VCardText>
          <DemoListDensity />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Nav -->
      <AppCardCode
        title="Nav"
        no-padding
        :code="demoCode.nav"
      >
        <VCardText>Lists can receive an alternative <code>nav</code> styling that reduces the width <code>v-list-item</code> takes up as well as adding a border radius.</VCardText>

        <VCardText>
          <DemoListNav />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Action and item group -->
      <AppCardCode
        title="Action and item group"
        no-padding
        :code="demoCode.actionAndItemGroup"
      >
        <VCardText>A <code>three-line</code> list with actions. Utilizing <code>v-list-group</code>, easily connect actions to your tiles.</VCardText>

        <VCardText>
          <DemoListActionAndItemGroup />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Sub Group -->
      <AppCardCode
        title="Sub Group"
        no-padding
        :code="demoCode.subGroup"
      >
        <VCardText>
          Using the <code>v-list-group</code> component you can create up to 2 levels in depth using the sub-group prop.
        </VCardText>

        <VCardText>
          <DemoListSubGroup />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Two lines and Subheader -->
      <AppCardCode
        title="Two lines and subheader"
        no-padding
        :code="demoCode.twoLinesAndSubheader"
      >
        <VCardText>Lists can contain subheaders, dividers, and can contain 1 or more lines. The subtitle will overflow with ellipsis if it extends past one line.</VCardText>

        <VCardText>
          <DemoListTwoLinesAndSubheader />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Three Line -->
      <AppCardCode
        title="Three Line"
        no-padding
        :code="demoCode.threeLine"
      >
        <VCardText>For three line lists, the subtitle will clamp vertically at 2 lines and then ellipsis. This feature uses line-clamp and is not supported in all browsers.</VCardText>

        <VCardText>
          <DemoListThreeLine />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Users List -->
      <AppCardCode
        title="User List"
        no-padding
        :code="demoCode.userList"
      >
        <VCardText>
          <DemoListUserList />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Progress List -->
      <AppCardCode
        title="Progress List"
        no-padding
        :code="demoCode.progressList"
      >
        <VCardText>
          <DemoListProgressList />
        </VCardText>
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Shaped -->
      <AppCardCode
        title="Shaped"
        no-padding
        :code="demoCode.shaped"
      >
        <VCardText>
          Shaped lists have rounded borders on one side of the <code>v-list-item</code>.
        </VCardText>

        <VCardText>
          <DemoListShaped />
        </VCardText>
      </AppCardCode>
    </VCol>
  </VRow>
</template>
