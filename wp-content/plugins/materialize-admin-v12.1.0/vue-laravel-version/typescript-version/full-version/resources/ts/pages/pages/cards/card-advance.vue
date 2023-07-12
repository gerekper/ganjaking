<script setup lang="ts">
import CardAdvanceActivityTimeLine from '@/views/pages/cards/card-advance/CardAdvanceActivityTimeLine.vue'
import CardAdvanceGeneralStatistics from '@/views/pages/cards/card-advance/CardAdvanceGeneralStatistics.vue'
import CardAdvanceIosFinanceApp from '@/views/pages/cards/card-advance/CardAdvanceIosFinanceApp.vue'
import CardAdvanceMeetingSchedule from '@/views/pages/cards/card-advance/CardAdvanceMeetingSchedule.vue'
import CardAdvanceMostSalesInCountries from '@/views/pages/cards/card-advance/CardAdvanceMostSalesInCountries.vue'
import CardAdvancePaymentHistory from '@/views/pages/cards/card-advance/CardAdvancePaymentHistory.vue'
import CardAdvanceProjectStatistics from '@/views/pages/cards/card-advance/CardAdvanceProjectStatistics.vue'
import CardAdvanceSocialNetworkVisits from '@/views/pages/cards/card-advance/CardAdvanceSocialNetworkVisits.vue'
import CardAdvanceSubscribersByCountries from '@/views/pages/cards/card-advance/CardAdvanceSubscribersByCountries.vue'
import CardAdvanceTopReferralSources from '@/views/pages/cards/card-advance/CardAdvanceTopReferralSources.vue'
import CardAdvanceTotalEarning from '@/views/pages/cards/card-advance/CardAdvanceTotalEarning.vue'
import CardAdvanceTransaction from '@/views/pages/cards/card-advance/CardAdvanceTransaction.vue'
import CardAdvanceUpgradeYourPlan from '@/views/pages/cards/card-advance/CardAdvanceUpgradeYourPlan.vue'
</script>

<template>
  <VRow class="match-height">
    <!-- 👉 Transaction  -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceTransaction />
    </VCol>

    <!-- 👉 Upgrade Your Plan -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceUpgradeYourPlan />
    </VCol>

    <!-- 👉 Meeting schedules -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceMeetingSchedule />
    </VCol>

    <!-- 👉 Project Statistics -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceProjectStatistics />
    </VCol>

    <!-- 👉 Top Referral Sources -->
    <VCol
      cols="12"
      md="12"
      lg="8"
    >
      <CardAdvanceTopReferralSources />
    </VCol>

    <!-- 👉 Total Earning  -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceTotalEarning />
    </VCol>

    <!-- 👉 Social Network Visits -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceSocialNetworkVisits />
    </VCol>

    <!-- 👉 General Statistics -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceGeneralStatistics />
    </VCol>

    <!-- 👉 Most Sales in Countries -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceMostSalesInCountries />
    </VCol>

    <!-- 👉 Payment History -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvancePaymentHistory />
    </VCol>

    <!--  👉 subscribers by country -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceSubscribersByCountries />
    </VCol>

    <!-- 👉 Activity TimeLine -->
    <VCol
      cols="12"
      md="6"
      lg="8"
    >
      <CardAdvanceActivityTimeLine />
    </VCol>

    <!-- 👉 Ios Finance App -->
    <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <CardAdvanceIosFinanceApp />
    </VCol>
  </VRow>
</template>
