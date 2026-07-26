<template>
    <Head title="Login" />

    <nord-layout hide-default-nav-toggle>
        <nord-top-bar slot="top-bar">
            <nord-stack
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <img
                    class="brand-logo"
                    :src="logo"
                    alt="nuova casa"
                />
                <strong class="brand-name n-typescale-l">{{ t("auth.brand") }}</strong>
            </nord-stack>
            <nord-stack
                slot="end"
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <nord-button
                    href="https://github.com/amiceli/nuova-casa"
                    target="_blank"
                    rel="noopener"
                >
                    {{ t("auth.github") }}
                </nord-button>
                <nord-button
                    variant="primary"
                    @click="goToAuth()"
                >
                    {{ t("auth.login") }}
                </nord-button>
            </nord-stack>
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
import logo from "@assets/casa-logo.webp"
import { Head } from "@inertiajs/vue3"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"

const { t } = useI18n()

function goToAuth() {
    window.location.assign(route("auth-redirect"))
}
</script>

<style scoped>
.brand-logo {
    height: 32px;
    width: 32px;
    border-radius: var(--n-border-radius);
    object-fit: cover;
}

.brand-name {
    color: #fff;
}
</style>
