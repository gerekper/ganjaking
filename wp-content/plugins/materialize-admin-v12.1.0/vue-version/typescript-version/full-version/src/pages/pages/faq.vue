<script setup lang="ts">
import type { FaqCategory } from '@/@fake-db/types'
import AppSearchHeader from '@/components/AppSearchHeader.vue'
import axios from '@axios'
import faqIllustration from '@images/illustrations/faq-illustration.png'

const faqSearchQuery = ref('')

const faqs = ref<FaqCategory[]>([])

const fetchFaqs = () => {
  return axios.get('/pages/faqs', {
    params: {
      q: faqSearchQuery.value,
    },
  }).then(response => {
    faqs.value = response.data
  }).catch(error => {
    console.error(error)
  })
}

const activeTab = ref('Payment')
const activeQuestion = ref(0)

watch(activeTab, () => activeQuestion.value = 0)
watch(faqSearchQuery, fetchFaqs, { immediate: true })

const contactUs = [
  {
    icon: 'mdi-phone',
    via: '+ (810) 2548 2568',
    tagLine: 'We are always happy to help!',
  },
  {
    icon: 'mdi-email-outline',
    via: 'hello@help.com',
    tagLine: 'Best way to get answer faster!',
  },
]
</script>

<template>
  <section>
    <!-- 👉 Search -->
    <AppSearchHeader
      v-model="faqSearchQuery"
      title="Hello, how can we help?"
      subtitle="or choose a category to quickly find the help you need"
      custom-class="mb-7"
    />

    <!-- 👉 Faq sections and questions -->
    <VRow>
      <VCol
        v-show="faqs.length"
        sd
        cols="12"
        sm="4"
        lg="3"
        class="position-relative"
      >
        <!-- 👉 Tabs -->
        <VTabs
          v-model="activeTab"
          direction="vertical"
          class="v-tabs-pill"
          grow
        >
          <VTab
            v-for="faq in faqs"
            :key="faq.faqTitle"
            :value="faq.faqTitle"
          >
            <VIcon
              :icon="faq.faqIcon"
              :size="20"
              start
            />
            {{ faq.faqTitle }}
          </VTab>
        </VTabs>
        <VImg
          height="195"
          :src="faqIllustration"
          class="d-none d-sm-block mt-6"
        />
      </VCol>

      <VCol
        cols="12"
        sm="8"
        lg="9"
      >
        <!-- 👉 Windows -->
        <VWindow
          v-model="activeTab"
          class="faq-v-window disable-tab-transition"
        >
          <VWindowItem
            v-for="faq in faqs"
            :key="faq.faqTitle"
            :value="faq.faqTitle"
          >
            <div class="d-flex align-center mb-6">
              <VAvatar
                rounded
                color="primary"
                variant="tonal"
                class="me-3"
                size="42"
              >
                <VIcon
                  size="24"
                  :icon="faq.faqIcon"
                />
              </VAvatar>

              <div>
                <h6 class="text-h6">
                  {{ faq.faqTitle }}
                </h6>
                <span class="text-sm">{{ faq.faqSubtitle }}</span>
              </div>
            </div>

            <VExpansionPanels
              v-model="activeQuestion"
              multiple
            >
              <VExpansionPanel
                v-for="item in faq.faqs"
                :key="item.question"
                :title="item.question"
                :text="item.answer"
              />
            </VExpansionPanels>
          </VWindowItem>
        </VWindow>
      </VCol>

      <VCol
        v-show="!faqs.length"
        cols="12"
        :class="!faqs.length ? 'd-flex justify-center align-center' : ''"
      >
        <VIcon
          icon="mdi-help-circle-outline"
          start
          size="20"
        />
        <span class="text-base font-weight-medium">
          No Results Found!!
        </span>
      </VCol>
    </VRow>

    <!-- 👉 You still have a question? -->
    <div class="text-center pt-15">
      <VChip
        label
        color="primary"
        size="small"
      >
        Question?
      </VChip>

      <h6 class="text-h6 my-2">
        You still have a question?
      </h6>
      <p class="text-sm mb-7">
        If you cannot find a question in our FAQ, you can always contact us. We will answer to you shortly!
      </p>

      <!-- contacts -->
      <VRow class="mt-4">
        <VCol
          v-for="contact in contactUs"
          :key="contact.icon"
          sm="6"
          cols="12"
        >
          <VCard
            flat
            variant="tonal"
          >
            <VCardText class="pa-6">
              <VAvatar
                rounded
                size="42"
                variant="tonal"
                class="me-3 mb-3"
              >
                <VIcon
                  size="24"
                  :icon="contact.icon"
                  color="high-emphasis"
                />
              </VAvatar>

              <h6 class="text-h6 mb-3">
                {{ contact.via }}
              </h6>
              <span>{{ contact.tagLine }}</span>
            </VCardText>
          </VCard>
        </VCol>
      </VRow>
    </div>
  </section>
</template>

<style lang="scss" scoped>
.faq-v-window {
  .v-window__container {
    z-index: 0;
  }
}
</style>
