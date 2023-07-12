<script setup lang="ts">
import { useRoute } from 'vue-router'
import type { HelpCenterSubcategoriesType, HelpCenterSubcategoryArticlesType } from '@/@fake-db/types'
import axios from '@axios'

const route = useRoute()

const apiData = ref<HelpCenterSubcategoriesType>()
const activeTab = ref(route.params.article)
const activeArticle = ref<HelpCenterSubcategoryArticlesType>()

const fetchHelpCenterArticlesData = () => {
  return axios.get('/pages/help-center/article', {
    params: {
      article: activeTab.value,
      category: route.params.category,
      subcategory: route.params.subcategory,
    },
  }).then(res => {
    apiData.value = res.data.activeSubcategory
    activeArticle.value = res.data.activeArticle
  })
}

watch(activeTab, fetchHelpCenterArticlesData, { immediate: true })
</script>

<template>
  <VRow v-if="activeArticle && apiData">
    <VCol
      cols="12"
      md="4"
    >
      <h6 class="text-h6 mb-3">
        {{ apiData.title }}
      </h6>

      <VTabs
        v-model="activeTab"
        direction="vertical"
        class="v-tabs-pill"
      >
        <VTab
          v-for="data in apiData.articles"
          :key="data.slug"
          :value="data.slug"
          :to="{
            name: 'pages-help-center-category-subcategory-article',
            params: {
              category: route.params.category,
              subcategory: route.params.subcategory,
              article: data.slug,
            },
          }"
        >
          <span class="text-wrap">
            {{ data.title }}
          </span>
        </VTab>
      </VTabs>
    </VCol>

    <VCol
      cols="12"
      md="8"
    >
      <VWindow class="disable-tab-transition">
        <VWindowItem>
          <VCard>
            <VCardText class="pb-0">
              <VBtn
                variant="outlined"
                :to="{
                  name: 'pages-help-center-category-subcategory',
                  params: { category: route.params.category, subcategory: route.params.subcategory },
                }"
              >
                <VIcon
                  size="24"
                  icon="mdi-chevron-left"
                  class="flip-in-rtl"
                  start
                />
                <span>Back to categories</span>
              </VBtn>
            </VCardText>

            <VCardItem>
              <template #prepend>
                <VAvatar
                  color="primary"
                  variant="tonal"
                  rounded
                  size="34"
                >
                  <VIcon
                    size="24"
                    icon="mdi-currency-usd"
                  />
                </VAvatar>
              </template>
              <VCardTitle>{{ activeArticle.title }}</VCardTitle>
            </VCardItem>

            <!-- eslint-disable vue/no-v-html vue/no-v-text-v-html-on-component -->
            <VCardText
              class="help-center-article-content"
              v-html="activeArticle.content"
            />
            <!-- eslint-enable vue/no-v-html vue/no-v-text-v-html-on-component -->

            <VDivider />

            <VCardText>
              <div class="d-flex justify-space-between flex-wrap mb-2">
                <div class="article-info">
                  <p class="text-base font-weight-medium text-high-emphasis mb-1">
                    {{ activeArticle.title }}
                  </p>
                  <p class="mb-1">
                    55 People found this helpful
                  </p>
                </div>
                <p class="text-base font-weight-medium text-high-emphasis">
                  Still need help?
                  <a href="javascript:void(0);">Contact us?</a>
                </p>
              </div>

              <div class="article-votes d-flex align-center gap-3">
                <VAvatar
                  color="primary"
                  variant="outlined"
                  rounded
                  size="30"
                >
                  <VIcon
                    icon="mdi-thumb-up"
                    size="20"
                  />
                </VAvatar>
                <VAvatar
                  color="primary"
                  variant="outlined"
                  rounded
                  size="30"
                >
                  <VIcon
                    icon="mdi-thumb-down"
                    size="20"
                  />
                </VAvatar>
              </div>
            </VCardText>
          </VCard>
        </VWindowItem>
      </VWindow>
    </VCol>
  </VRow>
</template>

<route lang="yaml">
meta:
  navActiveLink: pages-help-center
</route>

<style lang="scss">
.help-center-article-content {
  ol,
  ul {
    list-style-position: inside;
  }
}
</style>
