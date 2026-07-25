<template>
    <nord-layout hide-default-nav-toggle>
        <nord-top-bar slot="top-bar">
            <strong class="n-typescale-l">{{ t("auth.brand") }}</strong>
            <nord-button
                slot="end"
                variant="primary"
                @click="goToAuth()"
            >
                {{ t("auth.login") }}
            </nord-button>
        </nord-top-bar>

        <nord-stack align-items="center">
            <nord-card padding="l">
                <h1
                    slot="header"
                    class="n-typescale-xl"
                >
                    {{ t("auth.welcomeTitle") }}
                </h1>

                <nord-stack>
                    <p class="n-color-text-weaker">{{ t("auth.welcomeSubtitle") }}</p>
                    <p class="n-color-text-weak">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod
                        tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                        quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                        consequat.
                    </p>
                    <p class="n-color-text-weak">
                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore
                        eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident,
                        sunt in culpa qui officia deserunt mollit anim id est laborum.
                    </p>
                </nord-stack>

                <nord-button
                    slot="footer"
                    variant="primary"
                    expand
                    @click="goToAuth()"
                >
                    {{ t("auth.loginWithGithub") }}
                </nord-button>
            </nord-card>
        </nord-stack>
    </nord-layout>
</template>

<script setup lang="ts">
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"

const { t } = useI18n()

function goToAuth() {
    window.location.assign(route("auth-redirect"))
}
</script>
