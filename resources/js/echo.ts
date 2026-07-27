import { configureEcho } from "@laravel/echo-vue"

// Reverb carries the progress of the bookmark import
configureEcho({
    broadcaster: "reverb",
})
