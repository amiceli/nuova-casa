<template>
    <nord-card padding="l">
        <nord-stack
            direction="horizontal"
            align-items="center"
            gap="m"
        >
            <img
                class="n-size-icon-xl"
                :src="cardImage"
            />
            <nord-stack gap="xs">
                <a
                    class="n-truncate n-typescale-m n-color-text-link"
                    :href="openPage()"
                    target="_blank"
                >
                    {{ props.page.title }}
                </a>
                <span class="n-color-text-weaker n-typescale-s">
                    {{ createdAt }}
                </span>
            </nord-stack>
        </nord-stack>

        <Link
            slot="footer"
            v-if="!props.edit"
            :href="route('tag', {id: props.page.parent.id})"
        >
            <nord-badge>
                {{ props.page.parent.name }}
            </nord-badge>
        </Link>

        <nord-stack
            slot="footer"
            v-if="props.edit"
            direction="horizontal"
            align-items="center"
            justify-content="space-between"
        >
            <nord-stack
                direction="horizontal"
                gap="s"
                align-items="center"
            >
                <span class="n-color-text-weaker n-typescale-s">
                    {{ t('pages.favorite') }}
                </span>
                <nord-toggle
                    :checked="props.page.favorite"
                    :label="t('pages.changeFavorite')"
                    hide-label
                    @change="toggleFavorite()"
                ></nord-toggle>
            </nord-stack>
            <DeletePageButton :page="props.page" />
        </nord-stack>
    </nord-card>
</template>

<script lang="ts" setup>
import { Link, useForm } from "@inertiajs/vue3"
import { computed } from "vue"
import { useI18n } from "vue-i18n"
import { route } from "ziggy-js"
import { useDateFormat } from "@/composables/useDateFormat"
import { Page } from "@/modules/domain/Types"
import retroDefault from "../../../../assets/404_retro.png"
import DeletePageButton from "./DeletePageButton.vue"

const props = defineProps<{ page: Page; edit?: boolean }>()
const { t } = useI18n()
const { formatDate } = useDateFormat()

const cardImage = computed(() => (props.page.icon.includes("404") ? retroDefault : props.page.icon))

const createdAt = computed<string>(() => formatDate({ value: props.page.created_at }))

function openPage() {
    return props.page.url.startsWith("https://") ? props.page.url : `https://${props.page.url}`
}

function toggleFavorite() {
    const handler = document.querySelector("nord-toast-group")

    const form = useForm({
        favorite: !props.page.favorite,
    })

    form.put(route("update-page", { id: props.page.id }), {
        headers: { Accept: "application/json" },
        onSuccess: () => {
            handler?.addToast({
                variant: "success",
                message: t("pages.favoriteUpdated"),
                autoDismiss: 4000,
            })
        },
        onError: () => {
            handler?.addToast({
                variant: "danger",
                message: t("pages.favoriteFailed"),
                autoDismiss: 4000,
            })
        },
    })
}
</script>
