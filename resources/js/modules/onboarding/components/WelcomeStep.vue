<template>
    <nord-stack gap="m">
        <p class="n-typescale-m">
            {{ t('onboarding.welcome.hello', { name: user.name }) }}
        </p>
        <p class="n-color-text-weaker">
            {{ t('onboarding.welcome.text') }}
        </p>

        <nord-stack gap="s">
            <nord-stack
                v-for="(item, index) in highlights"
                :key="item"
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <nord-badge>{{ index + 1 }}</nord-badge>
                <span class="n-typescale-s">{{ t(item) }}</span>
            </nord-stack>
        </nord-stack>
    </nord-stack>
</template>

<script
    lang="ts"
    setup
>
import { usePage } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import type { User } from "@/types"

const page = usePage()
const { t } = useI18n()

const user = page.props.auth.user as User

const highlights = ["onboarding.welcome.bookmarks", "onboarding.welcome.theme", "onboarding.welcome.newsletters"]
</script>
