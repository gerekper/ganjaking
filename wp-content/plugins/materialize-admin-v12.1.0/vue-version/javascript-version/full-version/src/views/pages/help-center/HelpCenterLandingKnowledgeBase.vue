<script setup>
const props = defineProps({
  categories: {
    type: Array,
    required: true,
  },
})

const totalArticles = category => {
  return category.subCategories.map(subCategory => subCategory.articles.length).reduce((partialSum, a) => partialSum + a, 0)
}
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      lg="10"
      class="mx-auto mb-8"
    >
      <VRow class="match-height">
        <VCol
          v-for="article in props.categories"
          :key="article.title"
          cols="12"
          sm="6"
          md="4"
        >
          <VCard :title="article.title">
            <template #prepend>
              <VAvatar

                rounded
                size="34"
                :color="article.avatarColor"
                variant="tonal"
              >
                <VIcon
                  size="24"
                  :icon="article.icon"
                />
              </VAvatar>
            </template>

            <VCardText>
              <ul
                class="ps-6"
                style="list-style: disc;"
              >
                <li
                  v-for="item in article.subCategories"
                  :key="item.title"
                  class="text-primary mb-2"
                >
                  <RouterLink
                    :to="{
                      name: 'pages-help-center-category-subcategory',
                      params: { category: article.slug, subcategory: item.slug },
                    }"
                  >
                    {{ item.title }}
                  </RouterLink>
                </li>
              </ul>

              <div class="mt-4">
                <RouterLink
                  :to="{
                    name: 'pages-help-center-category-subcategory',
                    params: { category: article.slug, subcategory: article.subCategories[0].slug },
                  }"
                  class="text-base font-weight-medium"
                >
                  {{ totalArticles(article) }} articles
                </RouterLink>
              </div>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </VCol>
  </VRow>
</template>
