<script setup>
import CardWidgetsActivityTimeline from '@/views/pages/cards/card-widgets/CardWidgetsActivityTimeline.vue'
import CardWidgetsExternalLinks from '@/views/pages/cards/card-widgets/CardWidgetsExternalLinks.vue'
import CardWidgetsMonthlyBudget from '@/views/pages/cards/card-widgets/CardWidgetsMonthlyBudget.vue'
import CardWidgetsOrganicSessions from '@/views/pages/cards/card-widgets/CardWidgetsOrganicSessions.vue'
import CardWidgetsPerformance from '@/views/pages/cards/card-widgets/CardWidgetsPerformance.vue'
import CardWidgetsPerformanceOverview from '@/views/pages/cards/card-widgets/CardWidgetsPerformanceOverview.vue'
import CardWidgetsProjectTimeline from '@/views/pages/cards/card-widgets/CardWidgetsProjectTimeline.vue'
import CardWidgetsSalesCountry from '@/views/pages/cards/card-widgets/CardWidgetsSalesCountry.vue'
import CardWidgetsTotalTransactions from '@/views/pages/cards/card-widgets/CardWidgetsTotalTransactions.vue'
import CardWidgetsVisitsByDay from '@/views/pages/cards/card-widgets/CardWidgetsVisitsByDay.vue'
import CardWidgetsWeeklyOverview from '@/views/pages/cards/card-widgets/CardWidgetsWeeklyOverview.vue'
import CardWidgetsWeeklySales from '@/views/pages/cards/card-widgets/CardWidgetsWeeklySales.vue'
</script>

<template>
  <VRow>
    <!-- 👉 Total Transactions -->
    <VCol
      cols="12"
      md="8"
    >
      <CardWidgetsTotalTransactions />
    </VCol>

    <!-- 👉 Performance Overview -->
    <VCol
      cols="12"
      md="4"
      sm="6"
    >
      <CardWidgetsPerformanceOverview />
    </VCol>

    <!-- 👉 Visits By Day -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <CardWidgetsVisitsByDay />
    </VCol>

    <!-- 👉 Organic Sessions -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <CardWidgetsOrganicSessions />
    </VCol>

    <!-- 👉 Weekly Sales -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <CardWidgetsWeeklySales />
    </VCol>

    <!-- 👉 Project Timeline -->
    <VCol
      cols="12"
      md="8"
    >
      <CardWidgetsProjectTimeline />
    </VCol>

    <!-- 👉 Monthly Budget -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <CardWidgetsMonthlyBudget />
    </VCol>

    <!-- 👉 Performance -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <CardWidgetsPerformance />
    </VCol>

    <!-- 👉 External Links -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <CardWidgetsExternalLinks />
    </VCol>

    <!-- 👉 Sales Country -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <CardWidgetsSalesCountry />
    </VCol>

    <!-- 👉 Activity Timeline -->
    <VCol
      cols="12"
      md="8"
    >
      <CardWidgetsActivityTimeline />
    </VCol>

    <!-- 👉 Weekly Overview -->
    <VCol
      cols="12"
      md="4"
    >
      <CardWidgetsWeeklyOverview />
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/libs/apex-chart.scss";
</style>
